const express = require('express');
const cors = require('cors');
const jwt = require('jsonwebtoken');
const multer = require('multer');
const fs = require('fs');
const path = require('path');
const AdmZip = require('adm-zip');
const { Client } = require('ssh2');
const bcrypt = require('bcrypt');
const crypto = require('crypto');
const { sendVerificationEmail, sendMySQLCredentials } = require('./utils/emailService');
const { createMySQLUser } = require('./utils/mysqlUserManager');

// Load env deterministically from server/.env (override any existing env vars)
require('./loadEnv').loadEnv();

// DB must be required after env is loaded
const db = require('./db');

const app = express();
const PORT = process.env.PORT || 5000;
const SECRET_KEY = process.env.JWT_SECRET;

// --- CONFIGURATION ---
const normalizeWindowsPath = (raw) => {
    if (!raw) return raw;
    let value = String(raw).trim();

    // Strip wrapping quotes
    if ((value.startsWith('"') && value.endsWith('"')) || (value.startsWith("'") && value.endsWith("'"))) {
        value = value.slice(1, -1);
    }

    // Convert //server/share -> \\server\share for Windows UNC
    if (value.startsWith('//')) {
        value = `\\\\${value.slice(2).replace(/\//g, '\\')}`;
    }

    // Handle drive-relative paths like X:folder -> X:\folder
    if (/^[A-Za-z]:[^\\/]/.test(value)) {
        value = `${value[0]}:${path.win32.sep}${value.slice(2)}`;
    }

    return value;
};

const resolveEnvPath = (raw) => {
    const normalized = normalizeWindowsPath(raw);
    return path.resolve(normalized);
};

const STORAGE_ROOT = process.env.STORAGE_ROOT
    ? resolveEnvPath(process.env.STORAGE_ROOT)
    : path.resolve(__dirname, '..', 'userdata');

const PAYMENT_PROOF_PATH = process.env.PAYMENT_PROOF_PATH
    ? resolveEnvPath(process.env.PAYMENT_PROOF_PATH)
    : path.resolve(__dirname, '..', 'user_billing');

// --- CUSTOMIZABLE AVATAR PATH ---
const AVATAR_ROOT = process.env.AVATAR_ROOT
    ? resolveEnvPath(process.env.AVATAR_ROOT)
    : path.join(STORAGE_ROOT, 'avatars');

const ensureWritableDirSync = (dirPath, isRemote = false) => {
    // Skip mkdir and write test for remote/network paths (UNC paths)
    if (isRemote) {
        console.log('[storage] Remote path detected, skipping write test:', dirPath);
        return;
    }

    fs.mkdirSync(dirPath, { recursive: true });

    const probe = path.join(dirPath, `.write_test_${Date.now()}_${Math.random().toString(16).slice(2)}`);
    fs.writeFileSync(probe, 'ok');
    fs.unlinkSync(probe);
};

const isUNCPath = (p) => p.startsWith('\\\\');

try {
    ensureWritableDirSync(STORAGE_ROOT, isUNCPath(STORAGE_ROOT));
    ensureWritableDirSync(PAYMENT_PROOF_PATH, isUNCPath(PAYMENT_PROOF_PATH));
    ensureWritableDirSync(AVATAR_ROOT, isUNCPath(AVATAR_ROOT));
} catch (e) {
    console.error('[storage] Storage path is not writable:', e.message);
    process.exit(1);
}

console.log('[storage] STORAGE_ROOT resolved =', STORAGE_ROOT);
console.log('[storage] AVATAR_ROOT resolved =', AVATAR_ROOT);

const DEPLOY_RETRY_COUNT = 5;
const DEPLOY_RETRY_BASE_DELAY_MS = 300;
const NETWORK_WRITE_CHUNK_SIZE = 64 * 1024; // 64KB chunks for optimal network write
const PARALLEL_EXTRACT_LIMIT = 5; // Extract max 5 files simultaneously

const safeRm = async (targetPath) => {
    try {
        await fs.promises.rm(targetPath, { recursive: true, force: true });
    } catch {
        // ignore
    }
};

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

const isTransientFsError = (err) => {
    const code = err && err.code;
    return [
        'EPERM', 'EACCES', 'EBUSY', 'ETIMEDOUT', 'EIO', 'ENETUNREACH', 'ECONNRESET', 'ENOTEMPTY'
    ].includes(code);
};

const withRetries = async (fn) => {
    let lastErr;
    for (let attempt = 0; attempt <= DEPLOY_RETRY_COUNT; attempt += 1) {
        try {
            return await fn();
        } catch (e) {
            lastErr = e;
            if (attempt >= DEPLOY_RETRY_COUNT || !isTransientFsError(e)) throw e;
            const jitter = Math.floor(Math.random() * 100);
            const delay = Math.min(5000, DEPLOY_RETRY_BASE_DELAY_MS * (2 ** attempt) + jitter);
            await sleep(delay);
        }
    }
    throw lastErr;
};

// --- BACKGROUND DEPLOY JOBS ---
const deployJobs = new Map();
const deployQueue = [];
let activeDeployJobs = 0;
const DEPLOY_JOB_MAX_CONCURRENT = 2;

const newJobId = () => `deploy_${Date.now()}_${Math.random().toString(16).slice(2)}`;

const setJob = (jobId, patch) => {
    const prev = deployJobs.get(jobId) || {};
    if (patch.status === 'completed' && patch.progress !== 100) {
        delete patch.status;
    }
    deployJobs.set(jobId, { ...prev, ...patch, updatedAt: new Date().toISOString() });
};

const getJob = (jobId) => deployJobs.get(jobId);

const enqueueDeployJob = (jobId, handler) => {
    deployQueue.push({ jobId, handler });
    pumpDeployQueue();
};

const pumpDeployQueue = () => {
    while (activeDeployJobs < DEPLOY_JOB_MAX_CONCURRENT && deployQueue.length > 0) {
        const next = deployQueue.shift();
        activeDeployJobs += 1;
        Promise.resolve()
            .then(next.handler)
            .catch((e) => {
                try {
                    setJob(next.jobId, {
                        status: 'failed',
                        phase: 'failed',
                        error: e && e.message ? e.message : String(e),
                    });
                } catch { }
            })
            .finally(() => {
                activeDeployJobs -= 1;
                pumpDeployQueue();
            });
    }
};

// --- DATABASE INITIALIZATION ---
const initDB = async () => {
    try {
        const schemaPath = path.join(__dirname, 'schema.sql');
        const schemaSql = fs.readFileSync(schemaPath, 'utf8');
        const connection = await db.getConnection();
        try {
            await connection.query(schemaSql);
            console.log('Database initialized successfully.');
        } finally {
            connection.release();
        }
    } catch (err) {
        console.error('Database initialization failed:', err.message);
    }
};

// Middleware
app.use(cors());
app.use(express.json({ limit: '10mb' })); // Increase limit for base64 images

// Serve Avatars Statically
app.use('/avatars', express.static(AVATAR_ROOT));

// Multer Config
const upload = multer({
    storage: multer.memoryStorage(),
    limits: { fileSize: 100 * 1024 * 1024 } // 100MB limit
});

// Security: Prevent Directory Traversal
const getSafePath = async (userId, siteName, relativePath) => {
    const [users] = await db.execute('SELECT username FROM users WHERE id = ?', [userId]);
    if (users.length === 0) return null;
    const username = users[0].username;

    const userDir = path.join(STORAGE_ROOT, username);
    const siteDir = path.join(userDir, siteName);
    const safePath = path.resolve(siteDir, (relativePath || '/').replace(/^\/+/g, ''));

    if (!safePath.startsWith(siteDir)) return null;

    return { fullPath: safePath, siteDir, userDir };
};

// Load auth routes (register, verify-email)
const sitesRoutes = require('./routes/sitesRoutes');
app.use('/api/sites', sitesRoutes);

require('./routes/authRoutes')(app, db, SECRET_KEY);

// --- ROUTES ---

// 1. Auth
app.post('/api/auth/login', async (req, res) => {
    const { username, password } = req.body;
    console.log('[Login] Attempt:', username);
    try {
        const [users] = await db.execute('SELECT * FROM users WHERE username = ?', [username]);
        const user = users[0];
        console.log('[Login] User found:', !!user);

        if (!user) {
            return res.status(401).json({ message: 'Invalid credentials' });
        }

        // Check password (support both hashed and plain for demo accounts)
        let isPasswordValid = false;
        if (user.password.startsWith('$2b$')) {
            // Hashed password (new users)
            isPasswordValid = await bcrypt.compare(password, user.password);
        } else {
            // Plain password (demo accounts)
            isPasswordValid = (user.password === password);
        }

        if (!isPasswordValid) {
            return res.status(401).json({ message: 'Invalid credentials' });
        }

        // Check email verification (skip for demo accounts)
        if (!user.email_verified && user.status === 'PENDING') {
            return res.status(403).json({
                message: 'Please verify your email first. Check your inbox for verification link.'
            });
        }

        const token = jwt.sign({ id: user.id, role: user.role }, SECRET_KEY, { expiresIn: '12h' });
        const { password: _, ...userWithoutPassword } = user;
        res.json({ token, user: userWithoutPassword });
    } catch (err) {
        res.status(500).json({ message: err.message });
    }
});

app.get('/api/auth/me', async (req, res) => {
    const authHeader = req.headers.authorization;
    if (authHeader) {
        const token = authHeader.split(' ')[1];
        jwt.verify(token, SECRET_KEY, async (err, userDecoded) => {
            if (err) return res.status(403).json({ message: 'Invalid Token' });
            try {
                const [users] = await db.execute('SELECT * FROM users WHERE id = ?', [userDecoded.id]);
                if (users.length === 0) return res.status(404).json({ message: 'User not found' });
                const { password, ...userWithoutPassword } = users[0];
                res.json(userWithoutPassword);
            } catch (e) {
                res.status(500).json({ message: 'Server error' });
            }
        });
    } else {
        res.status(401).json({ message: 'Unauthorized' });
    }
});

app.put('/api/auth/profile', async (req, res) => {
    const { id, ...data } = req.body;

    // Handle Base64 Image Upload if present in 'avatar' field
    if (data.avatar && data.avatar.startsWith('data:image')) {
        try {
            const matches = data.avatar.match(/^data:([A-Za-z-+\/]+);base64,(.+)$/);
            if (matches && matches.length === 3) {
                const buffer = Buffer.from(matches[2], 'base64');
                const filename = `avatar_${id}_${Date.now()}.png`;
                const filePath = path.join(AVATAR_ROOT, filename);

                // Save file to custom path
                fs.writeFileSync(filePath, buffer);

                // Update avatar field with public URL
                // Assuming the server is reachable at the same host/port for static files
                // Using relative path for frontend to resolve or full URL if domain known
                const protocol = req.protocol;
                const host = req.get('host');
                data.avatar = `${protocol}://${host}/avatars/${filename}`;
            }
        } catch (err) {
            console.error("Failed to save avatar:", err);
            // Don't fail the whole request, just keep old avatar or base64 (might be too large for DB though)
        }
    }

    // Construct query dynamically
    const keys = Object.keys(data);
    const values = Object.values(data);
    if (keys.length === 0) return res.json({});

    const setClause = keys.map(k => `${k} = ?`).join(', ');

    try {
        await db.execute(`UPDATE users SET ${setClause} WHERE id = ?`, [...values, id]);
        const [users] = await db.execute('SELECT * FROM users WHERE id = ?', [id]);
        const { password, ...u } = users[0];
        res.json(u);
    } catch (err) {
        res.status(500).json({ message: err.message });
    }
});

app.post('/api/auth/change-password', async (req, res) => {
    const { userId, current, newPass } = req.body;
    try {
        const [users] = await db.execute('SELECT password FROM users WHERE id = ?', [userId]);
        if (users.length === 0) return res.status(404).json({ message: 'User not found' });

        if (users[0].password === current) {
            await db.execute('UPDATE users SET password = ? WHERE id = ?', [newPass, userId]);
            res.json({ success: true });
        } else {
            res.status(400).json({ message: 'Incorrect current password' });
        }
    } catch (err) {
        res.status(500).json({ message: err.message });
    }
});

// 2. Sites
app.get('/api/sites', async (req, res) => {
    const { userId } = req.query;
    if (!userId) return res.status(400).json({ message: 'userId is required' });
    try {
        const [sites] = await db.execute('SELECT * FROM sites WHERE user_id = ? ORDER BY created_at DESC', [userId]);
        const mapped = sites.map((s) => ({
            id: s.id, userId: s.user_id, name: s.name, subdomain: s.subdomain, framework: s.framework,
            status: s.status, createdAt: s.created_at, storageUsed: s.storage_used, hasDatabase: !!s.has_database,
        }));
        res.json(mapped);
    } catch (err) { res.status(500).json({ message: err.message }); }
});

app.post('/api/sites/deploy', upload.single('file'), async (req, res) => {
    const { userId, name, framework, subdomain, needsDatabase, attachedDatabaseId } = req.body;
    const file = req.file; // file.buffer contains the uploaded file data

    if (!userId || !name || !framework || !subdomain) {
        return res.status(400).json({ message: 'Missing required fields' });
    }

    try {
        const [users] = await db.execute('SELECT username FROM users WHERE id = ?', [userId]);
        if (users.length === 0) {
            return res.status(404).json({ message: 'User not found' });
        }
        const username = users[0].username;

        // Create background job
        const jobId = newJobId();
        const zipBuffer = file ? file.buffer : null;

        setJob(jobId, {
            id: jobId,
            status: 'queued',
            createdAt: new Date().toISOString(),
            phase: 'queued',
            progress: 0,
            request: { userId, username, name, framework, subdomain, needsDatabase, attachedDatabaseId, zipBuffer },
        });

        // Run deployment in background with retry
        enqueueDeployJob(jobId, async () => {
            const job = getJob(jobId);
            if (!job) return;

            setJob(jobId, { status: 'running', phase: 'writing', progress: 10 });
            const reqData = job.request;

            const userDir = path.join(STORAGE_ROOT, reqData.username);
            const finalSiteDir = path.join(userDir, reqData.name);
            const zipPath = path.join(finalSiteDir, `upload_${Date.now()}.zip`);

            let extractSuccess = false;
            let retryCount = 0;

            while (!extractSuccess && retryCount <= DEPLOY_RETRY_COUNT) {
                try {
                    setJob(jobId, { progress: 20 + (retryCount * 10) });

                    // Create directory structure with retry
                    await withRetries(() => fs.promises.mkdir(finalSiteDir, { recursive: true }));

                    if (reqData.zipBuffer) {
                        setJob(jobId, { phase: 'uploading', progress: 30 });

                        // Write ZIP buffer to UNC path with retry - chunked for better network performance
                        await withRetries(async () => {
                            const writeStream = fs.createWriteStream(zipPath, {
                                highWaterMark: NETWORK_WRITE_CHUNK_SIZE,
                                flags: 'w'
                            });

                            return new Promise((resolve, reject) => {
                                let offset = 0;

                                const writeChunk = () => {
                                    const chunk = reqData.zipBuffer.slice(offset, offset + NETWORK_WRITE_CHUNK_SIZE);
                                    if (chunk.length === 0) {
                                        writeStream.end();
                                        return;
                                    }

                                    offset += chunk.length;

                                    if (!writeStream.write(chunk)) {
                                        writeStream.once('drain', writeChunk);
                                    } else {
                                        setImmediate(writeChunk);
                                    }
                                };

                                writeStream.on('finish', resolve);
                                writeStream.on('error', reject);
                                writeChunk();
                            });
                        });

                        setJob(jobId, { phase: 'extracting', progress: 40 });

                        // Extract with retry - parallel processing for better performance
                        await withRetries(async () => {
                            const zip = new AdmZip(zipPath);
                            const entries = zip.getEntries();

                            // Separate directories and files
                            const dirs = [];
                            const files = [];

                            for (const entry of entries) {
                                const entryPath = entry.entryName.replace(/\\/g, '/');

                                // Skip unwanted files
                                if (
                                    entryPath.includes('/.git/') ||
                                    entryPath.startsWith('.git/') ||
                                    entryPath.endsWith('/.git') ||
                                    entryPath === '.git' ||
                                    entryPath.includes('/.DS_Store') ||
                                    entryPath.includes('/Thumbs.db') ||
                                    entryPath.includes('/__MACOSX/')
                                ) {
                                    continue;
                                }

                                if (entry.isDirectory) {
                                    dirs.push(entry);
                                } else {
                                    files.push(entry);
                                }
                            }

                            // Create all directories first
                            for (const entry of dirs) {
                                const targetDir = path.join(finalSiteDir, entry.entryName);
                                await fs.promises.mkdir(targetDir, { recursive: true });
                            }

                            // Extract files in parallel (batched)
                            let processed = 0;
                            const total = files.length;

                            for (let i = 0; i < files.length; i += PARALLEL_EXTRACT_LIMIT) {
                                const batch = files.slice(i, i + PARALLEL_EXTRACT_LIMIT);

                                await Promise.all(batch.map(async (entry) => {
                                    const targetFile = path.join(finalSiteDir, entry.entryName);
                                    const targetDir = path.dirname(targetFile);

                                    // Ensure parent directory exists
                                    await fs.promises.mkdir(targetDir, { recursive: true });

                                    // Write file with chunking for large files
                                    const data = entry.getData();
                                    if (data.length > NETWORK_WRITE_CHUNK_SIZE) {
                                        // Use stream for large files
                                        const writeStream = fs.createWriteStream(targetFile, {
                                            highWaterMark: NETWORK_WRITE_CHUNK_SIZE
                                        });

                                        await new Promise((resolve, reject) => {
                                            let offset = 0;

                                            const writeNext = () => {
                                                const chunk = data.slice(offset, offset + NETWORK_WRITE_CHUNK_SIZE);
                                                if (chunk.length === 0) {
                                                    writeStream.end();
                                                    return;
                                                }

                                                offset += chunk.length;

                                                if (!writeStream.write(chunk)) {
                                                    writeStream.once('drain', writeNext);
                                                } else {
                                                    setImmediate(writeNext);
                                                }
                                            };

                                            writeStream.on('finish', resolve);
                                            writeStream.on('error', reject);
                                            writeNext();
                                        });
                                    } else {
                                        // Write small files directly
                                        await fs.promises.writeFile(targetFile, data);
                                    }

                                    processed++;
                                }));

                                const progress = 40 + Math.floor((processed / total) * 40);
                                setJob(jobId, { progress });
                            }
                        });

                        // Delete ZIP after successful extraction
                        await safeRm(zipPath);
                        setJob(jobId, { progress: 85 });
                    } else {
                        // No file uploaded, create default index.html
                        await withRetries(() =>
                            fs.promises.writeFile(path.join(finalSiteDir, 'index.html'), '<h1>Hello World</h1>')
                        );
                        setJob(jobId, { progress: 75 });
                    }

                    extractSuccess = true;
                } catch (err) {
                    console.warn(`[deploy] Job ${jobId} attempt ${retryCount + 1}/${DEPLOY_RETRY_COUNT + 1} failed:`, err.message);
                    retryCount += 1;

                    if (retryCount > DEPLOY_RETRY_COUNT) {
                        // Cleanup: hapus folder project yang gagal
                        console.error(`[deploy] Job ${jobId} FAILED after ${DEPLOY_RETRY_COUNT + 1} attempts, cleaning up...`);
                        await safeRm(finalSiteDir);
                        throw new Error(`Deployment failed after ${DEPLOY_RETRY_COUNT + 1} attempts: ${err.message}`);
                    }

                    // Exponential backoff with jitter
                    const jitter = Math.floor(Math.random() * 100);
                    const delay = Math.min(5000, DEPLOY_RETRY_BASE_DELAY_MS * (2 ** retryCount) + jitter);
                    setJob(jobId, { phase: `retrying (${retryCount}/${DEPLOY_RETRY_COUNT})`, progress: 15 + (retryCount * 5) });
                    await sleep(delay);
                }
            }

            // Save to database
            setJob(jobId, { phase: 'saving', progress: 90 });
            const hasDb = reqData.needsDatabase === 'true' || !!reqData.attachedDatabaseId;
            const siteId = `s_${Date.now()}`;

            if (reqData.attachedDatabaseId) {
                await db.execute('DELETE FROM sites WHERE id = ?', [reqData.attachedDatabaseId]);
            }

            const status = 'ACTIVE';
            await db.execute(
                'INSERT INTO sites (id, user_id, name, subdomain, framework, status, storage_used, has_database) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                [siteId, reqData.userId, reqData.name, reqData.subdomain, reqData.framework, status, 15, hasDb]
            );

            const site = {
                id: siteId,
                userId: reqData.userId,
                name: reqData.name,
                subdomain: reqData.subdomain,
                framework: reqData.framework,
                status,
                createdAt: new Date(),
                storageUsed: 15,
                hasDatabase: hasDb,
            };

            setJob(jobId, { status: 'completed', phase: 'done', progress: 100, result: { site } });
        });

        // Return immediately with job ID
        res.status(202).json({ jobId, status: 'queued' });
    } catch (err) {
        console.error('[deploy] Error:', err);
        if (file && file.path) await safeRm(file.path);
        res.status(500).json({ message: err.message || 'Deployment failed' });
    }
});

// Deploy job status polling endpoint
app.get('/api/deploy/:jobId', (req, res) => {
    const job = getJob(req.params.jobId);
    if (!job) return res.status(404).json({ message: 'Job not found' });

    const safeJob = { ...job };
    if (safeJob.request) {
        safeJob.request = {
            userId: safeJob.request.userId,
            username: safeJob.request.username,
            name: safeJob.request.name,
            framework: safeJob.request.framework,
            subdomain: safeJob.request.subdomain,
        };
    }
    res.json(safeJob);
});

app.delete('/api/sites/:id', async (req, res) => {
    const { id } = req.params;
    const { deleteDb } = req.body;

    try {
        const [sites] = await db.execute('SELECT * FROM sites WHERE id = ?', [id]);
        if (sites.length === 0) return res.status(404).json({ message: 'Site not found' });
        const site = sites[0];

        // Delete files with retry mechanism
        const pathInfo = await getSafePath(site.user_id, site.name, '/');
        if (pathInfo && fs.existsSync(pathInfo.fullPath)) {
            let deleteSuccess = false;
            let retryCount = 0;

            while (!deleteSuccess && retryCount <= DEPLOY_RETRY_COUNT) {
                try {
                    await withRetries(() => fs.promises.rm(pathInfo.fullPath, { recursive: true, force: true }));
                    deleteSuccess = true;
                } catch (err) {
                    console.warn(`[delete] Attempt ${retryCount + 1}/${DEPLOY_RETRY_COUNT + 1} failed:`, err.message);
                    retryCount += 1;

                    if (retryCount > DEPLOY_RETRY_COUNT) {
                        throw new Error(`Failed to delete site files after ${DEPLOY_RETRY_COUNT + 1} attempts: ${err.message}`);
                    }

                    const jitter = Math.floor(Math.random() * 100);
                    const delay = Math.min(5000, DEPLOY_RETRY_BASE_DELAY_MS * (2 ** retryCount) + jitter);
                    await sleep(delay);
                }
            }
        }

        if (deleteDb) {
            await db.execute('DELETE FROM sites WHERE id = ?', [id]);
        } else {
            await db.execute('UPDATE sites SET status = ?, storage_used = 0 WHERE id = ?', ['DB_ONLY', id]);
        }

        res.json({ success: true });
    } catch (err) {
        console.error('[delete] Error:', err);
        res.status(500).json({ message: err.message || 'Delete failed' });
    }
});

app.put('/api/sites/:id', async (req, res) => {
    // Only status update mostly
    const { id } = req.params;
    const data = req.body;
    const keys = Object.keys(data);
    const values = Object.values(data);
    if (keys.length === 0) return res.json({});

    // Map camcelCase to snake_case for DB
    const colMap = { 'storageUsed': 'storage_used', 'hasDatabase': 'has_database' };
    const setClause = keys.map(k => `${colMap[k] || k} = ?`).join(', ');

    try {
        await db.execute(`UPDATE sites SET ${setClause} WHERE id = ?`, [...values, id]);
        res.json({ success: true });
    } catch (err) {
        res.status(500).json({ message: err.message });
    }
});

// 3. Files
app.get('/api/files', async (req, res) => {
    const { siteId, path: relPath } = req.query;
    try {
        const [sites] = await db.execute('SELECT * FROM sites WHERE id = ?', [siteId]);
        if (sites.length === 0) return res.status(404).json({ message: 'Site not found' });
        const site = sites[0];

        const pathInfo = await getSafePath(site.user_id, site.name, relPath || '/');
        if (!pathInfo) return res.status(403).json({ message: 'Access denied' });

        if (!fs.existsSync(pathInfo.fullPath)) return res.json([]);

        const dirents = fs.readdirSync(pathInfo.fullPath, { withFileTypes: true });
        const files = dirents.map(dirent => {
            const stats = fs.statSync(path.join(pathInfo.fullPath, dirent.name));
            return {
                id: `${relPath}-${dirent.name}`,
                name: dirent.name,
                type: dirent.isDirectory() ? 'folder' : 'file',
                size: dirent.isDirectory() ? '-' : (stats.size / 1024).toFixed(2) + ' KB',
                path: relPath || '/',
                createdAt: stats.birthtime.toISOString()
            };
        });
        res.json(files);
    } catch (err) {
        res.status(500).json({ message: err.message });
    }
});

app.post('/api/files/folder', async (req, res) => {
    const { siteId, path: relPath, folderName } = req.body;
    try {
        const [sites] = await db.execute('SELECT * FROM sites WHERE id = ?', [siteId]);
        if (sites.length === 0) return res.status(404).json({ message: 'Site not found' });
        const site = sites[0];

        const pathInfo = await getSafePath(site.user_id, site.name, relPath);
        const newFolderPath = path.join(pathInfo.fullPath, folderName);

        if (!fs.existsSync(newFolderPath)) {
            fs.mkdirSync(newFolderPath);
            res.json({ success: true });
        } else {
            res.status(400).json({ message: 'Folder exists' });
        }
    } catch (err) { res.status(500).json({ message: err.message }); }
});

app.post('/api/files/upload', upload.single('file'), async (req, res) => {
    const { siteId, path: relPath } = req.body;
    const file = req.file;
    if (!file) return res.status(400).json({ message: 'No file' });

    try {
        const [sites] = await db.execute('SELECT * FROM sites WHERE id = ?', [siteId]);
        if (sites.length === 0) return res.status(404).json({ message: 'Site not found' });
        const site = sites[0];

        const pathInfo = await getSafePath(site.user_id, site.name, relPath);
        const destPath = path.join(pathInfo.fullPath, file.originalname);

        fs.renameSync(file.path, destPath);
        res.json({ success: true });
    } catch (err) { res.status(500).json({ message: err.message }); }
});

app.put('/api/files/rename', async (req, res) => {
    const { siteId, path: relPath, oldName, newName } = req.body;
    try {
        const [sites] = await db.execute('SELECT * FROM sites WHERE id = ?', [siteId]);
        const site = sites[0];
        const pathInfo = await getSafePath(site.user_id, site.name, relPath);
        const oldP = path.join(pathInfo.fullPath, oldName);
        const newP = path.join(pathInfo.fullPath, newName);
        if (fs.existsSync(oldP)) {
            fs.renameSync(oldP, newP);
            res.json({ success: true });
        } else {
            res.status(404).json({ message: 'File not found' });
        }
    } catch (err) { res.status(500).json({ message: err.message }); }
});

app.delete('/api/files', async (req, res) => {
    const { siteId, path: relPath, name } = req.body;
    // Note: DELETE with body is unusual but express supports it
    try {
        const [sites] = await db.execute('SELECT * FROM sites WHERE id = ?', [siteId]);
        const site = sites[0];
        const pathInfo = await getSafePath(site.user_id, site.name, relPath);
        const p = path.join(pathInfo.fullPath, name);
        if (fs.existsSync(p)) {
            fs.rmSync(p, { recursive: true, force: true });
            res.json({ success: true });
        } else {
            res.status(404).json({ message: 'File not found' });
        }
    } catch (err) { res.status(500).json({ message: err.message }); }
});

// 4. Admin API
app.get('/api/admin/stats', async (req, res) => {
    const [[{ count: totalUsers }]] = await db.execute('SELECT COUNT(*) as count FROM users');
    const [[{ count: totalSites }]] = await db.execute('SELECT COUNT(*) as count FROM sites');
    res.json({ totalUsers, totalSites, activeRevenue: '0' });
});

app.get('/api/admin/users', async (req, res) => {
    const [users] = await db.execute('SELECT id, username, email, role, plan, avatar, status FROM users');
    res.json(users);
});

app.put('/api/admin/users/:id/toggle', async (req, res) => {
    const { id } = req.params;
    const [users] = await db.execute('SELECT status FROM users WHERE id = ?', [id]);
    if (users.length) {
        const newStatus = users[0].status === 'ACTIVE' ? 'SUSPENDED' : 'ACTIVE';
        await db.execute('UPDATE users SET status = ? WHERE id = ?', [newStatus, id]);
        res.json({ status: newStatus });
    } else {
        res.status(404).json({ message: 'User not found' });
    }
});

app.get('/api/admin/payments', async (req, res) => {
    const [payments] = await db.execute(`
        SELECT p.*, u.username 
        FROM payments p 
        LEFT JOIN users u ON p.user_id = u.id
    `);
    // Map snake_case to camelCase for frontend
    const mapped = payments.map(p => ({
        ...p,
        userId: p.user_id,
        proofUrl: p.proof_url
    }));
    res.json(mapped);
});

app.put('/api/admin/payments/:id/verify', async (req, res) => {
    const { id } = req.params;
    const { status } = req.body;

    await db.execute('UPDATE payments SET status = ? WHERE id = ?', [status, id]);

    if (status === 'VERIFIED') {
        const [payments] = await db.execute('SELECT * FROM payments WHERE id = ?', [id]);
        if (payments.length) {
            await db.execute('UPDATE users SET plan = ? WHERE id = ?', [payments[0].plan, payments[0].user_id]);
        }
    }
    const [updated] = await db.execute('SELECT * FROM payments WHERE id = ?', [id]);
    res.json({ ...updated[0], userId: updated[0].user_id, proofUrl: updated[0].proof_url });
});

// Payment proof upload endpoint
app.post('/api/payments', upload.single('proof'), async (req, res) => {
    const { userId, plan, amount, method } = req.body;
    const proofFile = req.file;

    if (!userId || !plan || !amount || !method) {
        return res.status(400).json({ message: 'Missing required fields' });
    }

    if (!proofFile) {
        return res.status(400).json({ message: 'Payment proof file is required' });
    }

    try {
        const [users] = await db.execute('SELECT username FROM users WHERE id = ?', [userId]);
        if (users.length === 0) {
            return res.status(404).json({ message: 'User not found' });
        }
        const username = users[0].username;

        // Create user folder in payment proof path
        const userProofDir = path.join(PAYMENT_PROOF_PATH, username);
        await withRetries(() => fs.promises.mkdir(userProofDir, { recursive: true }));

        // Generate unique filename with timestamp
        const timestamp = Date.now();
        const fileExt = path.extname(proofFile.originalname);
        const filename = `proof_${plan}_${timestamp}${fileExt}`;
        const finalPath = path.join(userProofDir, filename);

        // Write buffer directly to file (memoryStorage)
        await withRetries(() => fs.promises.writeFile(finalPath, proofFile.buffer));

        // Store relative path in database (username/filename)
        const relativeProofPath = `${username}/${filename}`;
        const paymentId = `pay_${timestamp}`;

        await db.execute(
            'INSERT INTO payments (id, user_id, plan, amount, method, proof_url, status, date) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())',
            [paymentId, userId, plan, amount, method, relativeProofPath, 'PENDING']
        );

        const payment = {
            id: paymentId,
            userId,
            plan,
            amount: parseFloat(amount),
            method,
            proofUrl: relativeProofPath,
            status: 'PENDING',
            createdAt: new Date(),
        };

        res.json(payment);
    } catch (err) {
        console.error('[payment] Error:', err);
        res.status(500).json({ message: err.message || 'Payment submission failed' });
    }
});

// Serve payment proof files (admin can view)
app.get('/api/payments/proof/:username/:filename', async (req, res) => {
    const { username, filename } = req.params;
    const filePath = path.join(PAYMENT_PROOF_PATH, username, filename);

    try {
        await fs.promises.access(filePath, fs.constants.R_OK);
        res.sendFile(filePath);
    } catch (err) {
        res.status(404).json({ message: 'Proof file not found' });
    }
});

// Get payment history for user
app.get('/api/payments/history/:userId', async (req, res) => {
    const { userId } = req.params;
    try {
        const [payments] = await db.execute(
            'SELECT id, user_id as userId, plan, amount, method, proof_url as proofUrl, status, date as createdAt FROM payments WHERE user_id = ? ORDER BY date DESC',
            [userId]
        );
        res.json(payments);
    } catch (err) {
        res.status(500).json({ message: err.message });
    }
});

// 5. Support
app.post('/api/tickets', async (req, res) => {
    const { userId, username, subject } = req.body;
    const id = `t_${Date.now()}`;
    await db.execute('INSERT INTO tickets (id, user_id, subject) VALUES (?, ?, ?)', [id, userId, subject]);
    res.json({ id, userId, username, subject, status: 'OPEN', createdAt: new Date() });
});

app.get('/api/tickets', async (req, res) => {
    const { userId } = req.query;
    let query = `
        SELECT t.id, t.user_id as userId, u.username, t.subject, t.status, t.created_at as createdAt, t.last_message_at as lastMessageAt
        FROM tickets t
        LEFT JOIN users u ON t.user_id = u.id
    `;
    let params = [];
    if (userId) {
        query += ' WHERE t.user_id = ?';
        params.push(userId);
    }
    query += ' ORDER BY t.last_message_at DESC';
    const [tickets] = await db.execute(query, params);
    res.json(tickets);
});

app.get('/api/tickets/:id/messages', async (req, res) => {
    const { id } = req.params;
    const [msgs] = await db.execute('SELECT id, ticket_id as ticketId, sender_id as senderId, text, timestamp, is_admin as isAdmin FROM messages WHERE ticket_id = ? ORDER BY timestamp ASC', [id]);

    // Fetch sender names
    const enriched = await Promise.all(msgs.map(async m => {
        const [u] = await db.execute('SELECT username FROM users WHERE id = ?', [m.senderId]);
        return { ...m, senderName: u[0] ? u[0].username : 'Unknown', isAdmin: !!m.isAdmin };
    }));
    res.json(enriched);
});

app.post('/api/tickets/:id/messages', async (req, res) => {
    const { id } = req.params;
    const { senderId, text, isAdmin } = req.body;
    const msgId = `m_${Date.now()}`;

    await db.execute(
        'INSERT INTO messages (id, ticket_id, sender_id, text, is_admin) VALUES (?, ?, ?, ?, ?)',
        [msgId, id, senderId, text, isAdmin]
    );
    await db.execute('UPDATE tickets SET last_message_at = NOW() WHERE id = ?', [id]);

    const [u] = await db.execute('SELECT username FROM users WHERE id = ?', [senderId]);

    res.json({
        id: msgId, ticketId: id, senderId,
        senderName: u[0] ? u[0].username : 'Unknown',
        text, timestamp: new Date(), isAdmin
    });
});

app.put('/api/tickets/:id/close', async (req, res) => {
    const { id } = req.params;
    await db.execute('UPDATE tickets SET status = ? WHERE id = ?', ['CLOSED', id]);
    res.json({ success: true });
});

// 6. Plans & Domains
app.get('/api/plans', async (req, res) => {
    const [plans] = await db.execute('SELECT * FROM plans');
    res.json(plans);
});

app.post('/api/plans', async (req, res) => {
    const plan = req.body;
    const id = `p_${Date.now()}`;
    await db.execute(
        'INSERT INTO plans (id, name, price, currency, features, limits, is_popular) VALUES (?, ?, ?, ?, ?, ?, ?)',
        [id, plan.name, plan.price, plan.currency, JSON.stringify(plan.features), JSON.stringify(plan.limits), plan.isPopular]
    );
    res.json({ ...plan, id });
});

app.put('/api/plans/:id', async (req, res) => {
    const { id } = req.params;
    const plan = req.body;
    // Simplification: Update fields
    await db.execute(
        'UPDATE plans SET name=?, price=?, currency=?, features=?, limits=?, is_popular=? WHERE id=?',
        [plan.name, plan.price, plan.currency, JSON.stringify(plan.features), JSON.stringify(plan.limits), plan.isPopular, id]
    );
    res.json({ ...plan, id });
});

app.delete('/api/plans/:id', async (req, res) => {
    await db.execute('DELETE FROM plans WHERE id = ?', [req.params.id]);
    res.json({ success: true });
});

app.get('/api/domains', async (req, res) => {
    const [domains] = await db.execute('SELECT id, name, is_primary as isPrimary FROM domains');
    res.json(domains);
});

app.post('/api/domains', async (req, res) => {
    const { name } = req.body;
    const id = `d_${Date.now()}`;
    await db.execute('INSERT INTO domains (id, name) VALUES (?, ?)', [id, name]);
    res.json({ id, name, isPrimary: false });
});

app.delete('/api/domains/:id', async (req, res) => {
    await db.execute('DELETE FROM domains WHERE id = ?', [req.params.id]);
    res.json({ success: true });
});

// ========================================
// Terminal Command Execution (SSH)
// ========================================

// Execute single terminal command via SSH
app.post('/api/sites/:id/execute', async (req, res) => {
    try {
        const siteId = req.params.id;
        const { command } = req.body;

        if (!command) {
            return res.status(400).json({ success: false, error: 'Command is required' });
        }

        // Get site info untuk menentukan framework dan subdomain
        const [sites] = await db.execute('SELECT * FROM sites WHERE id = ?', [siteId]);
        if (sites.length === 0) {
            return res.status(404).json({ success: false, error: 'Site not found' });
        }

        const site = sites[0];
        const framework = site.framework;
        const subdomain = site.subdomain;

        // Get username untuk path structure
        const [users] = await db.execute('SELECT username FROM users WHERE id = ?', [site.user_id]);
        if (users.length === 0) {
            return res.status(404).json({ success: false, error: 'User not found' });
        }
        const username = users[0].username;

        // Untuk Laravel, gunakan SSH
        if (framework === 'Laravel') {
            const sshConfig = {
                host: process.env.SSH_HOST || 'synology-ssh.kolab.top',
                port: parseInt(process.env.SSH_PORT || '22'),
                username: process.env.SSH_USER || 'Aslabkolab',
                password: process.env.SSH_PASSWORD
            };

            if (process.env.SSH_PRIVATE_KEY_PATH) {
                sshConfig.privateKey = require('fs').readFileSync(process.env.SSH_PRIVATE_KEY_PATH);
                delete sshConfig.password;
            }

            // Whitelisted commands untuk keamanan
            const allowedCommands = [
                '/usr/local/bin/php82 /usr/local/bin/composer install',
                '/usr/local/bin/php82 /usr/local/bin/composer update',
                'npm install',
                'npm run build',
                'npm run dev',
                '/usr/local/bin/php82 artisan migrate',
                '/usr/local/bin/php82 artisan db:seed',
                '/usr/local/bin/php82 artisan storage:link',
                '/usr/local/bin/php82 artisan cache:clear',
                '/usr/local/bin/php82 artisan config:cache',
                '/usr/local/bin/php82 artisan route:cache',
                '/usr/local/bin/php82 artisan view:clear',
                '/usr/local/bin/php82 artisan optimize'
            ];

            const isAllowed = allowedCommands.some(allowed =>
                command.trim().startsWith(allowed) || command.includes(allowed)
            );

            if (!isAllowed) {
                return res.status(403).json({
                    success: false,
                    error: 'Command not allowed. Only whitelisted Laravel commands are permitted.'
                });
            }

            // Working directory untuk project ini
            // Path mapping: Windows \\100.90.80.70\web\project\kohost_users = Synology /var/services/web/project/kohost_users
            // Struktur: /var/services/web/project/kohost_users/{username}/{project_name}
            // Project name adalah bagian sebelum domain (misal: tes.kolabpanel.com -> tes)
            const projectName = subdomain.split('.')[0]; // Extract 'tes' from 'tes.kolabpanel.com'
            const workingDir = `/var/services/web/project/kohost_users/${username}/${projectName}`;

            // Replace $USERNAME placeholder dengan username sebenarnya
            let processedCommand = command.replace('$USERNAME', username);

            // Untuk command ls/pwd/find, cd ke working dir terlebih dahulu (jika bukan absolute path)
            const isAbsoluteCommand = processedCommand.includes('/var/') || processedCommand.includes('/volume');
            const isPwdCommand = processedCommand.trim() === 'pwd';

            let fullCommand;
            if (isAbsoluteCommand) {
                fullCommand = processedCommand; // ls -la /absolute/path tetap standalone
            } else if (isPwdCommand) {
                fullCommand = `cd ${workingDir} 2>/dev/null && pwd || echo "Directory not found: ${workingDir}"`; // pwd harus cd dulu, handle error
            } else {
                fullCommand = `cd ${workingDir} 2>/dev/null && ${processedCommand} || echo "Directory not found: ${workingDir}"`; // command lain perlu cd, handle error
            }

            console.log(`[ssh] Executing on ${sshConfig.host}: ${fullCommand}`);

            const result = await executeSSHCommand(sshConfig, fullCommand);

            return res.json({
                success: result.exitCode === 0,
                output: {
                    stdout: result.stdout,
                    stderr: result.stderr,
                    exitCode: result.exitCode
                }
            });

        } else {
            // Untuk framework lain (React, Next.js, Node.js), bisa ditambahkan logic lain
            // Saat ini return error
            return res.status(400).json({
                success: false,
                error: `Command execution not yet supported for ${framework}`
            });
        }

    } catch (error) {
        console.error('[execute] Error:', error);
        res.status(500).json({ success: false, error: error.message });
    }
});

// Execute terminal command dengan streaming output (SSE)
app.post('/api/sites/:id/execute-stream', async (req, res) => {
    try {
        const siteId = req.params.id;
        const { command } = req.body;

        if (!command) {
            return res.status(400).json({ success: false, error: 'Command is required' });
        }

        // Get site info
        const [sites] = await db.execute('SELECT * FROM sites WHERE id = ?', [siteId]);
        if (sites.length === 0) {
            return res.status(404).json({ success: false, error: 'Site not found' });
        }

        const site = sites[0];
        const framework = site.framework;

        // Get username
        const [users] = await db.execute('SELECT username FROM users WHERE id = ?', [site.user_id]);
        if (users.length === 0) {
            return res.status(404).json({ success: false, error: 'User not found' });
        }
        const username = users[0].username;
        const projectName = site.subdomain.split('.')[0];

        // Setup SSE
        res.setHeader('Content-Type', 'text/event-stream');
        res.setHeader('Cache-Control', 'no-cache');
        res.setHeader('Connection', 'keep-alive');
        res.flushHeaders();

        const sendEvent = (type, data) => {
            res.write(`event: ${type}\n`);
            res.write(`data: ${JSON.stringify(data)}\n\n`);
        };

        if (framework === 'Laravel') {
            const sshConfig = {
                host: process.env.SSH_HOST || '100.90.80.70',
                port: parseInt(process.env.SSH_PORT || '22'),
                username: process.env.SSH_USER || 'Aslabkolab',
                password: process.env.SSH_PASSWORD
            };

            // Define allowed commands for Laravel
            const allowedCommands = [
                'php artisan',
                '/usr/local/bin/php82 artisan',
                'composer',
                'npm',
                '/usr/local/bin/php82 artisan migrate',
                '/usr/local/bin/php82 artisan db:seed',
                '/usr/local/bin/php82 artisan cache:clear',
                '/usr/local/bin/php82 artisan config:clear',
                '/usr/local/bin/php82 artisan route:cache',
                '/usr/local/bin/php82 artisan view:clear',
                '/usr/local/bin/php82 artisan optimize'
            ];

            // Detect if command should run locally (migration/seeding commands)
            const isMigrationCommand = command.includes('artisan migrate') || command.includes('artisan db:seed');

            if (isMigrationCommand) {
                // Execute locally on Windows (to access Laragon MySQL)
                const windowsProjectPath = `\\\\${sshConfig.host}\\web\\project\\kohost_users\\${username}\\${projectName}`;

                try {
                    await executeLocalCommandStreaming(windowsProjectPath, command, sendEvent);
                    sendEvent('done', { success: true });
                    res.end();
                } catch (err) {
                    sendEvent('error', { message: err.message });
                    res.end();
                }
            } else if (process.env.SSH_PRIVATE_KEY_PATH) {
                // Execute via SSH for non-migration commands
                const isAllowed = allowedCommands.some(allowed =>
                    command.trim().startsWith(allowed) || command.includes(allowed)
                );

                if (!isAllowed) {
                    sendEvent('error', { message: 'Command not allowed' });
                    return res.end();
                }

                const workingDir = `/var/services/web/project/kohost_users/${username}/${projectName}`;
                let processedCommand = command.replace('$USERNAME', username);

                const isAbsoluteCommand = processedCommand.includes('/var/') || processedCommand.includes('/volume');
                const isPwdCommand = processedCommand.trim() === 'pwd';

                let fullCommand;
                if (isAbsoluteCommand) {
                    fullCommand = processedCommand;
                } else if (isPwdCommand) {
                    fullCommand = `cd ${workingDir} 2>/dev/null && pwd || echo "Directory not found: ${workingDir}"`;
                } else {
                    fullCommand = `cd ${workingDir} 2>/dev/null && ${processedCommand} || echo "Directory not found: ${workingDir}"`;
                }

                // Execute dengan streaming
                await executeSSHCommandStreaming(sshConfig, fullCommand, sendEvent);

                sendEvent('done', { success: true });
                res.end();
            }
        } else {
            sendEvent('error', { message: `Command execution not yet supported for ${framework}` });
            res.end();
        }

    } catch (error) {
        console.error('[execute-stream] Error:', error);
        res.write(`event: error\n`);
        res.write(`data: ${JSON.stringify({ message: error.message })}\n\n`);
        res.end();
    }
});

// Helper function untuk execute command locally (Windows)
// Strategy: Try direct execution first (60s timeout), fallback to copy if needed
// While direct runs, also prepare copy in background for faster fallback
async function executeLocalCommandStreaming(projectPath, command, sendEvent) {
    const { exec, execSync, spawn } = require('child_process');
    const os = require('os');
    const isWindows = os.platform() === 'win32';

    // Non-Windows: simple execution
    if (!isWindows) {
        return new Promise((resolve, reject) => {
            const unixProjectPath = projectPath.replace(/\\/g, '/');
            const bashCommand = `cd "${unixProjectPath}" && php ${command}`;
            exec(bashCommand, (error, stdout, stderr) => {
                if (stdout) sendEvent('log', { type: 'stdout', text: stdout });
                if (stderr) sendEvent('log', { type: 'stderr', text: stderr });
                sendEvent('exit', { code: error ? 1 : 0 });
                if (error) reject(error);
                else resolve({ exitCode: 0 });
            });
        });
    }

    // Windows: Find PHP
    let phpPath = process.env.PHP_PATH;
    if (!phpPath) {
        const locations = [
            'D:\\laragon\\bin\\php\\php-8.3.26-Win32-vs16-x64\\php.exe',
            'C:\\laragon\\bin\\php\\php-8.3.0\\php.exe',
            'C:\\laragon\\bin\\php\\php-8.2.0\\php.exe',
        ];
        for (const loc of locations) {
            if (fs.existsSync(loc)) { phpPath = loc; break; }
        }
        if (!phpPath) {
            try {
                phpPath = execSync('where php', { encoding: 'utf8', windowsHide: true }).split('\n')[0].trim();
            } catch (e) {
                sendEvent('log', { type: 'stderr', text: 'PHP not found.' });
                throw new Error('PHP not found');
            }
        }
    }

    // Clean command
    let cleanCommand = command.replace(/^php\s+/, '');
    if (!cleanCommand.includes('--no-interaction')) {
        cleanCommand = cleanCommand.includes('--force')
            ? cleanCommand.replace('--force', '--force --no-interaction')
            : cleanCommand + ' --no-interaction';
    }

    // Convert UNC to mapped drive for source
    let sourcePath = projectPath;
    if (projectPath.startsWith('\\\\')) {
        sourcePath = projectPath.replace(/^\\\\[^\\]+\\web/, 'X:').replace(/\//g, '\\');
    }

    console.log('[local-exec] Source:', sourcePath);
    console.log('[local-exec] PHP:', phpPath);

    // Run direct execution (no timeout, shows progress indicator)
    sendEvent('log', { type: 'info', text: 'Running migration...' });

    const result = await tryDirectExecution(sourcePath, phpPath, cleanCommand, sendEvent);

    console.log('[local-exec] Execution completed');
    sendEvent('exit', { code: result.exitCode });
    return { exitCode: result.exitCode };
}

// Try direct execution - no hard timeout, shows progress indicator
function tryDirectExecution(sourcePath, phpPath, command, sendEvent, timeout) {
    return new Promise((resolve) => {
        const { spawn } = require('child_process');
        const os = require('os');

        const batFile = path.join(os.tmpdir(), `direct_${Date.now()}.bat`);
        const batContent = `@echo off
chcp 65001 > nul
cd /d "${sourcePath}"
"${phpPath}" ${command}
exit /b %errorlevel%
`;
        fs.writeFileSync(batFile, batContent, 'utf8');

        let hasOutput = false;
        let outputBuffer = '';
        let completed = false;
        let exitCode = 0;
        let elapsedSeconds = 0;

        const child = spawn('cmd.exe', ['/c', batFile], {
            windowsHide: true,
            stdio: ['ignore', 'pipe', 'pipe']
        });

        // Progress indicator - shows every 10 seconds while waiting
        const progressInterval = setInterval(() => {
            if (!completed && !hasOutput) {
                elapsedSeconds += 10;
                sendEvent('log', { type: 'info', text: `Processing... (${elapsedSeconds}s)` });
            }
        }, 10000);

        child.stdout.on('data', (data) => {
            hasOutput = true;
            outputBuffer += data.toString();
            const lines = outputBuffer.split(/\r?\n/);
            outputBuffer = lines.pop() || '';

            lines.forEach(line => {
                if (line.trim()) {
                    console.log('[local-exec-direct] stdout:', line);
                    sendEvent('log', { type: 'stdout', text: line });
                }
            });
        });

        child.stderr.on('data', (data) => {
            hasOutput = true;
            data.toString().split(/\r?\n/).forEach(line => {
                if (line.trim()) {
                    console.log('[local-exec-direct] stderr:', line);
                    sendEvent('log', { type: 'stderr', text: line });
                }
            });
        });

        child.on('close', (code) => {
            completed = true;
            clearInterval(progressInterval);

            if (outputBuffer.trim()) {
                sendEvent('log', { type: 'stdout', text: outputBuffer });
            }

            try { fs.unlinkSync(batFile); } catch (e) { }

            exitCode = code || 0;
            console.log('[local-exec-direct] Exit code:', exitCode);

            // Always resolve as success since process completed
            resolve({ success: true, exitCode });
        });

        child.on('error', (err) => {
            completed = true;
            clearInterval(progressInterval);
            console.error('[local-exec-direct] Error:', err);
            try { fs.unlinkSync(batFile); } catch (e) { }
            resolve({ success: false, reason: 'error', error: err });
        });
    });
}

// Helper function untuk execute SSH command dengan streaming output
async function executeSSHCommandStreaming(sshConfig, command, sendEvent) {
    return new Promise((resolve, reject) => {
        const conn = new Client();

        conn.on('ready', () => {
            console.log('[ssh-stream] Connection established');
            sendEvent('log', { type: 'info', text: 'SSH connection established' });

            conn.exec(command, (err, stream) => {
                if (err) {
                    conn.end();
                    return reject(err);
                }

                stream.on('close', (code) => {
                    console.log(`[ssh-stream] Command exited with code ${code}`);
                    sendEvent('exit', { code });
                    conn.end();
                    resolve({ exitCode: code });
                });

                // Stream stdout line by line
                let stdoutBuffer = '';
                stream.on('data', (data) => {
                    stdoutBuffer += data.toString();
                    const lines = stdoutBuffer.split('\n');
                    stdoutBuffer = lines.pop(); // Keep incomplete line in buffer

                    lines.forEach(line => {
                        if (line.trim()) {
                            sendEvent('log', { type: 'stdout', text: line });
                        }
                    });
                });

                // Stream stderr line by line
                let stderrBuffer = '';
                stream.stderr.on('data', (data) => {
                    stderrBuffer += data.toString();
                    const lines = stderrBuffer.split('\n');
                    stderrBuffer = lines.pop();

                    lines.forEach(line => {
                        if (line.trim()) {
                            sendEvent('log', { type: 'stderr', text: line });
                        }
                    });
                });

                // Send remaining buffer on close
                stream.on('close', () => {
                    if (stdoutBuffer.trim()) {
                        sendEvent('log', { type: 'stdout', text: stdoutBuffer });
                    }
                    if (stderrBuffer.trim()) {
                        sendEvent('log', { type: 'stderr', text: stderrBuffer });
                    }
                });
            });
        });

        conn.on('error', (err) => {
            console.error('[ssh-stream] Connection error:', err);
            reject(err);
        });

        conn.connect(sshConfig);
    });
}

// Helper function untuk execute single SSH command
async function executeSSHCommand(sshConfig, command) {
    return new Promise((resolve, reject) => {
        const conn = new Client();
        let stdout = '';
        let stderr = '';
        let exitCode = null;

        conn.on('ready', () => {
            console.log('[ssh] Connection established');

            conn.exec(command, (err, stream) => {
                if (err) {
                    conn.end();
                    return reject(err);
                }

                stream.on('close', (code, signal) => {
                    exitCode = code;
                    console.log(`[ssh] Command exited with code ${code}`);
                    if (stdout) console.log('[ssh] STDOUT:', stdout);
                    if (stderr) console.log('[ssh] STDERR:', stderr);
                    conn.end();
                    resolve({ stdout, stderr, exitCode });
                });

                stream.on('data', (data) => {
                    stdout += data.toString();
                });

                stream.stderr.on('data', (data) => {
                    stderr += data.toString();
                });
            });
        });

        conn.on('error', (err) => {
            console.error('[ssh] Connection error:', err);
            reject(err);
        });

        conn.connect(sshConfig);
    });
}

// ========================================
// Laravel Deployment Endpoints (SSH)
// ========================================

// Laravel setup via SSH (composer, npm, artisan commands on Synology)
app.post('/api/sites/:id/laravel-setup', async (req, res) => {
    const { id } = req.params;
    const { steps } = req.body; // ['composer', 'npm', 'storage-link', 'build', 'migrate']

    try {
        const [sites] = await db.execute('SELECT * FROM sites WHERE id = ?', [id]);
        if (!sites.length) return res.status(404).json({ message: 'Site not found' });

        const site = sites[0];
        const [users] = await db.execute('SELECT username FROM users WHERE id = ?', [site.user_id]);
        const username = users[0].username;

        // Path di Synology (via SSH)
        const remotePath = `/volume1/web/project/kohost_users/${username}/${site.subdomain}`;

        console.log(`[laravel-setup] Site: ${site.subdomain}, Path: ${remotePath}, Steps:`, steps);

        // Execute commands via SSH
        const output = await executeSSHCommands(remotePath, steps);

        res.json({ success: true, output });
    } catch (err) {
        console.error('[laravel-setup] Error:', err);
        res.status(500).json({ message: err.message });
    }
});

// Database migration endpoint (via local MySQL Laragon)
app.post('/api/sites/:id/migrate-db', async (req, res) => {
    const { id } = req.params;
    const { sqlFile } = req.body; // Base64 encoded SQL file

    try {
        const [sites] = await db.execute('SELECT * FROM sites WHERE id = ?', [id]);
        if (!sites.length) return res.status(404).json({ message: 'Site not found' });

        const site = sites[0];
        const [users] = await db.execute('SELECT username FROM users WHERE id = ?', [site.user_id]);
        const username = users[0].username;

        // Create database for this site
        const sanitizedSubdomain = site.subdomain.replace(/[^a-z0-9]/gi, '_');
        const sanitizedUsername = username.replace(/[^a-z0-9]/gi, '');
        const dbName = `${sanitizedSubdomain}_${sanitizedUsername}`;

        console.log(`[migrate-db] Creating database: ${dbName}`);

        await db.execute(`CREATE DATABASE IF NOT EXISTS \`${dbName}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci`);

        // Import SQL file if provided
        if (sqlFile) {
            const sqlContent = Buffer.from(sqlFile, 'base64').toString('utf-8');
            const statements = sqlContent.split(';').filter(s => s.trim());

            await db.execute(`USE \`${dbName}\``);

            for (const stmt of statements) {
                if (stmt.trim()) {
                    try {
                        await db.execute(stmt);
                    } catch (err) {
                        console.error('[migrate-db] SQL Error:', err.message);
                        // Continue with other statements
                    }
                }
            }

            console.log(`[migrate-db] Imported ${statements.length} SQL statements`);
        }

        res.json({
            success: true,
            database: dbName,
            host: 'localhost',
            username: 'root',
            password: ''
        });
    } catch (err) {
        console.error('[migrate-db] Error:', err);
        res.status(500).json({ message: err.message });
    }
});

// SSH Command Executor
async function executeSSHCommands(workDir, steps) {
    return new Promise((resolve, reject) => {
        const conn = new Client();
        const outputs = [];

        conn.on('ready', () => {
            const commands = [];

            // Whitelist commands only for security
            if (steps.includes('composer')) {
                commands.push(`cd ${workDir} && composer install --no-dev --optimize-autoloader 2>&1`);
            }
            if (steps.includes('npm')) {
                commands.push(`cd ${workDir} && npm install 2>&1`);
            }
            if (steps.includes('storage-link')) {
                commands.push(`cd ${workDir} && php artisan storage:link 2>&1`);
            }
            if (steps.includes('build')) {
                commands.push(`cd ${workDir} && npm run build 2>&1`);
            }
            if (steps.includes('migrate')) {
                // Note: This runs artisan migrate on remote, but DB should be local
                commands.push(`cd ${workDir} && php artisan migrate --force 2>&1`);
            }
            if (steps.includes('cache-clear')) {
                commands.push(`cd ${workDir} && php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear 2>&1`);
            }

            if (commands.length === 0) {
                conn.end();
                return resolve([{ message: 'No commands to execute' }]);
            }

            // Execute sequentially
            executeCommandsSequentially(conn, commands, 0, outputs, () => {
                conn.end();
                resolve(outputs);
            }, reject);
        });

        conn.on('error', (err) => {
            console.error('[ssh] Connection error:', err);
            reject(err);
        });

        const sshConfig = {
            host: process.env.SSH_HOST,
            port: parseInt(process.env.SSH_PORT || '22'),
            username: process.env.SSH_USER,
            readyTimeout: 30000,
        };

        // Use password or key
        if (process.env.SSH_PRIVATE_KEY_PATH && fs.existsSync(process.env.SSH_PRIVATE_KEY_PATH)) {
            sshConfig.privateKey = fs.readFileSync(process.env.SSH_PRIVATE_KEY_PATH);
        } else if (process.env.SSH_PASSWORD) {
            sshConfig.password = process.env.SSH_PASSWORD;
        } else {
            return reject(new Error('SSH credentials not configured'));
        }

        console.log(`[ssh] Connecting to ${sshConfig.username}@${sshConfig.host}:${sshConfig.port}`);
        conn.connect(sshConfig);
    });
}

function executeCommandsSequentially(conn, commands, index, outputs, onComplete, onError) {
    if (index >= commands.length) {
        return onComplete();
    }

    const cmd = commands[index];
    console.log(`[ssh] Executing [${index + 1}/${commands.length}]: ${cmd}`);

    conn.exec(cmd, (err, stream) => {
        if (err) {
            console.error('[ssh] Exec error:', err);
            return onError(err);
        }

        let stdout = '';
        let stderr = '';

        stream.on('close', (code) => {
            const output = {
                command: cmd,
                stdout: stdout.trim(),
                stderr: stderr.trim(),
                exitCode: code,
                success: code === 0
            };

            outputs.push(output);
            console.log(`[ssh] Command finished with exit code ${code}`);

            if (code !== 0) {
                console.error(`[ssh] Command failed: ${stderr || stdout}`);
                return onError(new Error(`Command failed with exit code ${code}: ${stderr || stdout}`));
            }

            // Continue to next command
            executeCommandsSequentially(conn, commands, index + 1, outputs, onComplete, onError);
        });

        stream.on('data', (data) => {
            const text = data.toString();
            stdout += text;
            console.log('[ssh] stdout:', text);
        });

        stream.stderr.on('data', (data) => {
            const text = data.toString();
            stderr += text;
            console.error('[ssh] stderr:', text);
        });
    });
}

// phpMyAdmin redirect endpoint - ensure MySQL user exists
app.get('/phpmyadmin', async (req, res) => {
    try {
        // Get user from token (if authenticated)
        const token = req.headers.authorization?.split(' ')[1] || req.query.token;

        if (token) {
            // Verify token and ensure MySQL user exists
            const decoded = jwt.verify(token, SECRET_KEY);

            if (decoded && decoded.userId) {
                const [users] = await db.execute('SELECT id, username FROM users WHERE id = ?', [decoded.userId]);

                if (users.length > 0) {
                    const user = users[0];
                    const masterDbUser = `sql_${user.username.toLowerCase().replace(/[^a-z0-9]/g, '')}`;
                    const masterDbPass = `kp_${user.id.substring(0, 4)}@${user.username.substring(0, 3).toUpperCase()}#88`;

                    // Ensure MySQL user exists with proper grants (async, don't wait)
                    ensureMySQLUser(masterDbUser, masterDbPass, user.username).catch(err => {
                        console.error('[mysql] Error ensuring user:', err.message);
                    });
                }
            }
        }
    } catch (err) {
        console.error('[phpmyadmin] Token error:', err.message);
    }

    // Always redirect to phpMyAdmin (user logs in manually with credentials from page)
    const phpmyadminUrl = process.env.PHPMYADMIN_URL || 'http://localhost/phpmyadmin';
    res.redirect(phpmyadminUrl);
});

// Helper function to ensure MySQL user exists
async function ensureMySQLUser(username, password, actualUsername) {
    try {
        // Check if user exists
        const [existing] = await db.execute(
            "SELECT User FROM mysql.user WHERE User = ?",
            [username]
        );

        if (existing.length === 0) {
            // Create user
            await db.execute(
                `CREATE USER '${username}'@'localhost' IDENTIFIED BY '${password}'`
            );
            console.log(`[mysql] Created user: ${username}`);
        } else {
            // Update password if user exists
            await db.execute(
                `ALTER USER '${username}'@'localhost' IDENTIFIED BY '${password}'`
            );
        }

        // Grant privileges to all databases matching pattern
        const sanitizedUsername = actualUsername.toLowerCase().replace(/[^a-z0-9]/g, '');

        // Grant to databases with pattern: *_{username}_*
        const dbPattern = `%_${sanitizedUsername}_%`;
        await db.execute(
            `GRANT ALL PRIVILEGES ON \`${dbPattern}\`.* TO '${username}'@'localhost'`
        );

        // Also grant to main user database
        const mainDbName = `sql_${sanitizedUsername}`;
        await db.execute(
            `GRANT ALL PRIVILEGES ON \`${mainDbName}\`.* TO '${username}'@'localhost'`
        );

        await db.execute('FLUSH PRIVILEGES');
        console.log(`[mysql] Granted privileges to ${username} for databases: ${dbPattern}, ${mainDbName}`);

    } catch (err) {
        console.error('[mysql] Error ensuring user:', err.message);
        throw err;
    }
}

app.listen(PORT, async () => {
    await initDB();
    console.log(`KolabPanel API running on http://localhost:${PORT}`);
});

