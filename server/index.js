const express = require('express');
const cors = require('cors');
const jwt = require('jsonwebtoken');
const multer = require('multer');
const fs = require('fs');
const path = require('path');
const AdmZip = require('adm-zip');

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

const UPLOAD_TEMP = process.env.UPLOAD_TEMP
    ? resolveEnvPath(process.env.UPLOAD_TEMP)
    : path.resolve(__dirname, 'uploads');

const PAYMENT_PROOF_PATH = process.env.PAYMENT_PROOF_PATH
    ? resolveEnvPath(process.env.PAYMENT_PROOF_PATH)
    : path.resolve(__dirname, '..', 'user_billing');

const ensureWritableDirSync = (dirPath, isRemote = false) => {
    fs.mkdirSync(dirPath, { recursive: true });
    
    // Skip write test for remote/network paths (UNC paths)
    if (isRemote) {
        console.log('[storage] Remote path detected, skipping write test:', dirPath);
        return;
    }
    
    const probe = path.join(dirPath, `.write_test_${Date.now()}_${Math.random().toString(16).slice(2)}`);
    fs.writeFileSync(probe, 'ok');
    fs.unlinkSync(probe);
};

const isUNCPath = (p) => p.startsWith('\\\\');

try {
    ensureWritableDirSync(STORAGE_ROOT, isUNCPath(STORAGE_ROOT));
    ensureWritableDirSync(UPLOAD_TEMP);
    ensureWritableDirSync(PAYMENT_PROOF_PATH);
} catch (e) {
    console.error('[storage] Storage path is not writable:', e.message);
    console.error('[storage] STORAGE_ROOT=', process.env.STORAGE_ROOT);
    console.error('[storage] UPLOAD_TEMP=', process.env.UPLOAD_TEMP);
    console.error('[storage] PAYMENT_PROOF_PATH=', process.env.PAYMENT_PROOF_PATH);
    process.exit(1);
}

console.log('[storage] STORAGE_ROOT resolved =', STORAGE_ROOT);
console.log('[storage] UPLOAD_TEMP resolved =', UPLOAD_TEMP);
console.log('[storage] PAYMENT_PROOF_PATH resolved =', PAYMENT_PROOF_PATH);

const DEPLOY_RETRY_COUNT = 5;
const DEPLOY_RETRY_BASE_DELAY_MS = 300;

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
    // Network shares / Windows can surface transient issues as these codes
    return [
        'EPERM',
        'EACCES',
        'EBUSY',
        'ETIMEDOUT',
        'EIO',
        'ENETUNREACH',
        'ECONNRESET',
        'ENOTEMPTY'
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
                } catch {}
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
    
    // Split queries strictly if needed, but multipleStatements: true handles most cases.
    // However, executing a large block is safer with pool.query vs pool.execute for multiple statements
    const connection = await db.getConnection();
    try {
        await connection.query(schemaSql);
        console.log('Database initialized successfully (tables checked/created).');
    } finally {
        connection.release();
    }
  } catch (err) {
    console.error('Database initialization failed:', err.message);
    // Don't exit process, maybe DB isn't ready yet, but log error
  }
};

// Middleware
app.use(cors());
app.use(express.json());

// Multer Config
const storage = multer.diskStorage({
  destination: (req, file, cb) => cb(null, UPLOAD_TEMP),
  filename: (req, file, cb) => cb(null, Date.now() + '-' + file.originalname)
});
const upload = multer({ storage });

// Security: Prevent Directory Traversal
const getSafePath = async (userId, siteName, relativePath) => {
    const [users] = await db.execute('SELECT username FROM users WHERE id = ?', [userId]);
    if (users.length === 0) return null;
    const username = users[0].username;

    const userDir = path.join(STORAGE_ROOT, username);
    const siteDir = path.join(userDir, siteName);
    const safePath = path.resolve(siteDir, (relativePath || '/').replace(/^\/+/g, '')); 

    // Ensure the resolved path is still inside the site directory
    // Note: If relativePath is '/', safePath equals siteDir
    if (!safePath.startsWith(siteDir)) return null;
    
    return { fullPath: safePath, siteDir, userDir };
};

const getSiteSize = (dirPath) => {
    let size = 0;
    if (!fs.existsSync(dirPath)) return 0;
    
    const files = fs.readdirSync(dirPath);
    for (const file of files) {
        const filePath = path.join(dirPath, file);
        const stats = fs.statSync(filePath);
        if (stats.isDirectory()) {
            size += getSiteSize(filePath);
        } else {
            size += stats.size;
        }
    }
    return size;
};

// --- ROUTES ---

// 1. Auth
app.post('/api/auth/login', async (req, res) => {
    const { username, password } = req.body;
    console.log('[login] Attempt:', username);
    try {
        const [users] = await db.execute('SELECT * FROM users WHERE username = ?', [username]);
        console.log('[login] Users found:', users.length);
        const user = users[0];
        
        if (user && user.password === password) {
            console.log('[login] Success:', username);
            const token = jwt.sign({ id: user.id, role: user.role }, SECRET_KEY, { expiresIn: '12h' });
            const { password, ...u } = user;
            res.json({ token, user: u });
        } else {
            console.log('[login] Failed: Invalid credentials for', username);
            res.status(401).json({ message: 'Invalid credentials' });
        }
    } catch (err) {
        console.error('[login] Error:', err);
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
                const { password, ...u } = users[0];
                res.json(u);
            } catch (e) {
                res.status(500).json({ message: 'Server error' });
            }
        });
    } else {
        res.status(401).json({ message: 'Unauthorized' });
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

app.put('/api/auth/profile', async (req, res) => {
    const { id, ...data } = req.body;
    // Construct query dynamically
    const keys = Object.keys(data);
    const values = Object.values(data);
    if(keys.length === 0) return res.json({});

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

// 2. Sites
app.get('/api/sites', async (req, res) => {
    const { userId } = req.query;
    if (!userId) return res.status(400).json({ message: 'userId is required' });

    try {
        const [sites] = await db.execute('SELECT * FROM sites WHERE user_id = ? ORDER BY created_at DESC', [userId]);
        const mapped = sites.map((s) => ({
            id: s.id,
            userId: s.user_id,
            name: s.name,
            subdomain: s.subdomain,
            framework: s.framework,
            status: s.status,
            createdAt: s.created_at,
            storageUsed: s.storage_used,
            hasDatabase: !!s.has_database,
        }));
        res.json(mapped);
    } catch (err) {
        res.status(500).json({ message: err.message });
    }
});

app.post('/api/sites/deploy', upload.single('file'), async (req, res) => {
    const { userId, name, framework, subdomain, needsDatabase, attachedDatabaseId } = req.body;
    const file = req.file;

    if (!userId || !name || !framework || !subdomain) {
        if (file && file.path) await safeRm(file.path);
        return res.status(400).json({ message: 'Missing required fields' });
    }

    try {
        const [users] = await db.execute('SELECT username FROM users WHERE id = ?', [userId]);
        if (users.length === 0) {
            if (file && file.path) await safeRm(file.path);
            return res.status(404).json({ message: 'User not found' });
        }
        const username = users[0].username;

        // Create background job
        const jobId = newJobId();
        setJob(jobId, {
            id: jobId,
            status: 'queued',
            createdAt: new Date().toISOString(),
            phase: 'queued',
            request: { userId, username, name, framework, subdomain, needsDatabase, attachedDatabaseId, zipPath: file ? file.path : null },
        });

        // Run deployment in background
        enqueueDeployJob(jobId, async () => {
            const job = getJob(jobId);
            if (!job) return;

            setJob(jobId, { status: 'running', phase: 'extracting' });
            const reqData = job.request;

            const userDir = path.join(STORAGE_ROOT, reqData.username);
            const finalSiteDir = path.join(userDir, reqData.name);

            let extractSuccess = false;
            let retryCount = 0;

            while (!extractSuccess && retryCount <= DEPLOY_RETRY_COUNT) {
                try {
                    await safeRm(finalSiteDir);
                    await withRetries(() => fs.promises.mkdir(userDir, { recursive: true }));
                    await withRetries(() => fs.promises.mkdir(finalSiteDir, { recursive: true }));

                    if (reqData.zipPath) {
                        const zip = new AdmZip(reqData.zipPath);
                        const entries = zip.getEntries();
                        
                        await withRetries(() => {
                            for (const entry of entries) {
                                const entryPath = entry.entryName.replace(/\\/g, '/');
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
                                    const targetDir = path.join(finalSiteDir, entry.entryName);
                                    fs.mkdirSync(targetDir, { recursive: true });
                                } else {
                                    const targetFile = path.join(finalSiteDir, entry.entryName);
                                    const targetDir = path.dirname(targetFile);
                                    fs.mkdirSync(targetDir, { recursive: true });
                                    fs.writeFileSync(targetFile, entry.getData());
                                }
                            }
                        });
                        
                        await safeRm(reqData.zipPath);
                    } else {
                        await withRetries(() => 
                            fs.promises.writeFile(path.join(finalSiteDir, 'index.html'), '<h1>Hello World</h1>')
                        );
                    }

                    extractSuccess = true;
                } catch (err) {
                    console.warn(`[deploy] Job ${jobId} attempt ${retryCount + 1}/${DEPLOY_RETRY_COUNT + 1} failed:`, err.message);
                    retryCount += 1;
                    
                    if (retryCount > DEPLOY_RETRY_COUNT) {
                        await safeRm(finalSiteDir);
                        if (reqData.zipPath) await safeRm(reqData.zipPath);
                        throw new Error(`Deployment failed after ${DEPLOY_RETRY_COUNT + 1} attempts: ${err.message}`);
                    }

                    const jitter = Math.floor(Math.random() * 100);
                    const delay = Math.min(5000, DEPLOY_RETRY_BASE_DELAY_MS * (2 ** retryCount) + jitter);
                    await sleep(delay);
                }
            }

            // Save to database
            setJob(jobId, { phase: 'saving' });
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

            setJob(jobId, { status: 'completed', phase: 'done', result: { site } });
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
    if(keys.length === 0) return res.json({});
    
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
    } catch(err) { res.status(500).json({ message: err.message }); }
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
     } catch(err) { res.status(500).json({ message: err.message }); }
});

app.put('/api/files/rename', async (req, res) => {
    const { siteId, path: relPath, oldName, newName } = req.body;
    try {
        const [sites] = await db.execute('SELECT * FROM sites WHERE id = ?', [siteId]);
        const site = sites[0];
        const pathInfo = await getSafePath(site.user_id, site.name, relPath);
        const oldP = path.join(pathInfo.fullPath, oldName);
        const newP = path.join(pathInfo.fullPath, newName);
        if(fs.existsSync(oldP)) {
            fs.renameSync(oldP, newP);
            res.json({success:true});
        } else {
            res.status(404).json({message: 'File not found'});
        }
    } catch(err) { res.status(500).json({ message: err.message }); }
});

app.delete('/api/files', async (req, res) => {
    const { siteId, path: relPath, name } = req.body;
    // Note: DELETE with body is unusual but express supports it
    try {
        const [sites] = await db.execute('SELECT * FROM sites WHERE id = ?', [siteId]);
        const site = sites[0];
        const pathInfo = await getSafePath(site.user_id, site.name, relPath);
        const p = path.join(pathInfo.fullPath, name);
        if(fs.existsSync(p)) {
            fs.rmSync(p, { recursive: true, force: true });
            res.json({success:true});
        } else {
             res.status(404).json({message: 'File not found'});
        }
    } catch(err) { res.status(500).json({ message: err.message }); }
});

// 4. Admin API
app.get('/api/admin/stats', async (req, res) => {
    const [[{count: totalUsers}]] = await db.execute('SELECT COUNT(*) as count FROM users');
    const [[{count: totalSites}]] = await db.execute('SELECT COUNT(*) as count FROM sites');
    res.json({ totalUsers, totalSites, activeRevenue: '0' });
});

app.get('/api/admin/users', async (req, res) => {
    const [users] = await db.execute('SELECT id, username, email, role, plan, avatar, status FROM users');
    res.json(users);
});

app.put('/api/admin/users/:id/toggle', async (req, res) => {
    const { id } = req.params;
    const [users] = await db.execute('SELECT status FROM users WHERE id = ?', [id]);
    if(users.length) {
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
        if(payments.length) {
            await db.execute('UPDATE users SET plan = ? WHERE id = ?', [payments[0].plan, payments[0].user_id]);
        }
    }
    const [updated] = await db.execute('SELECT * FROM payments WHERE id = ?', [id]);
    res.json({...updated[0], userId: updated[0].user_id, proofUrl: updated[0].proof_url});
});

// Payment proof upload endpoint
app.post('/api/payments', upload.single('proof'), async (req, res) => {
    const { userId, plan, amount } = req.body;
    const proofFile = req.file;

    if (!userId || !plan || !amount) {
        if (proofFile && proofFile.path) await safeRm(proofFile.path);
        return res.status(400).json({ message: 'Missing required fields' });
    }

    if (!proofFile) {
        return res.status(400).json({ message: 'Payment proof file is required' });
    }

    try {
        const [users] = await db.execute('SELECT username FROM users WHERE id = ?', [userId]);
        if (users.length === 0) {
            if (proofFile && proofFile.path) await safeRm(proofFile.path);
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

        // Move file from temp to final location
        await withRetries(() => fs.promises.rename(proofFile.path, finalPath));

        // Store relative path in database (username/filename)
        const relativeProofPath = `${username}/${filename}`;
        const paymentId = `pay_${timestamp}`;

        await db.execute(
            'INSERT INTO payments (id, user_id, plan, amount, proof_url, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())',
            [paymentId, userId, plan, amount, relativeProofPath, 'PENDING']
        );

        const payment = {
            id: paymentId,
            userId,
            plan,
            amount: parseFloat(amount),
            proofUrl: relativeProofPath,
            status: 'PENDING',
            createdAt: new Date(),
        };

        res.json(payment);
    } catch (err) {
        console.error('[payment] Error:', err);
        if (proofFile && proofFile.path) await safeRm(proofFile.path);
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
            'SELECT id, user_id as userId, plan, amount, proof_url as proofUrl, status, created_at as createdAt FROM payments WHERE user_id = ? ORDER BY created_at DESC',
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

app.listen(PORT, async () => {
    await initDB();
    console.log(`KolabPanel API running on http://localhost:${PORT}`);
});
