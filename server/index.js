const express = require('express');
const cors = require('cors');
const jwt = require('jsonwebtoken');
const multer = require('multer');
const fs = require('fs');
const path = require('path');
const AdmZip = require('adm-zip');

const app = express();
const PORT = 5000;
const SECRET_KEY = 'kolab_secret_key_change_this_in_prod';

// --- CONFIGURATION ---
const STORAGE_ROOT = 'D:\\KolabPanel'; // Root directory for all user projects
const UPLOAD_TEMP = 'uploads/'; // Temp folder for zips

// Ensure root directories exist
if (!fs.existsSync(STORAGE_ROOT)) fs.mkdirSync(STORAGE_ROOT, { recursive: true });
if (!fs.existsSync(UPLOAD_TEMP)) fs.mkdirSync(UPLOAD_TEMP, { recursive: true });

// Middleware
app.use(cors());
app.use(express.json());

// Multer Config
const storage = multer.diskStorage({
  destination: (req, file, cb) => cb(null, UPLOAD_TEMP),
  filename: (req, file, cb) => cb(null, Date.now() + '-' + file.originalname)
});
const upload = multer({ storage });

// --- IN-MEMORY DATABASE (Metadata only) ---
let users = [
  { id: 'u1', username: 'demo_user', password: 'password', email: 'user@example.com', role: 'USER', plan: 'Basic', avatar: 'https://picsum.photos/200', status: 'ACTIVE' },
  { id: 'a1', username: 'sys_admin', password: 'admin', email: 'admin@kolabpanel.com', role: 'ADMIN', plan: 'Premium', avatar: 'https://picsum.photos/201', status: 'ACTIVE' }
];

let sites = [
  // Example metadata, files will be checked from disk
];

let plans = [
    { id: 'plan_basic', name: 'Basic', price: 0, currency: 'Rp', features: ['1 Site', '100MB Storage', 'Shared Database'], limits: { sites: 1, storage: 100, databases: 0 }, isPopular: false },
    { id: 'plan_pro', name: 'Pro', price: 50000, currency: 'Rp', features: ['5 Sites', '1GB Storage', 'Private Database'], limits: { sites: 5, storage: 1024, databases: 1 }, isPopular: true },
    { id: 'plan_premium', name: 'Premium', price: 100000, currency: 'Rp', features: ['Unlimited Sites', '10GB Storage'], limits: { sites: 9999, storage: 10240, databases: 5 }, isPopular: false }
];

let domains = [
    { id: 'd1', name: 'kolabpanel.com', isPrimary: true }
];

let payments = [];

// Helper functions
const findUser = (username, password) => users.find(u => u.username === username && u.password === password);
const findUserById = (id) => users.find(u => u.id === id);

// Security: Prevent Directory Traversal
const getSafePath = (userId, siteName, relativePath) => {
    // Structure: D:\KolabPanel\username\siteName\relativePath
    const user = findUserById(userId);
    if (!user) return null;

    const userDir = path.join(STORAGE_ROOT, user.username);
    const siteDir = path.join(userDir, siteName);
    const safePath = path.resolve(siteDir, relativePath.replace(/^\/+/g, '')); // Remove leading slashes

    // Ensure the resolved path is still inside the site directory
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

// 1. Auth & Profiles (Standard)
app.post('/api/auth/login', (req, res) => {
    const { username, password } = req.body;
    const user = findUser(username, password);
    if (user) {
        const token = jwt.sign({ id: user.id, role: user.role }, SECRET_KEY, { expiresIn: '1h' });
        const { password, ...u } = user;
        res.json({ token, user: u });
    } else {
        res.status(401).json({ message: 'Invalid credentials' });
    }
});

app.get('/api/auth/me', (req, res) => {
    const authHeader = req.headers.authorization;
    if (authHeader) {
        const token = authHeader.split(' ')[1];
        jwt.verify(token, SECRET_KEY, (err, userDecoded) => {
            if (err) return res.status(403).json({ message: 'Invalid Token' });
            const user = findUserById(userDecoded.id);
            if (!user) return res.status(404).json({ message: 'User not found' });
            const { password, ...u } = user;
            res.json(u);
        });
    } else {
        res.status(401).json({ message: 'Unauthorized' });
    }
});

// 2. Sites & Deployment (With Real File System)
app.get('/api/sites', (req, res) => {
    const userId = req.query.userId;
    if (userId) {
        const user = findUserById(userId);
        if(!user) return res.json([]);

        // Sync metadata with real folders if needed, for now just return metadata
        // Update storage usage dynamically
        const userSites = sites.filter(s => s.userId === userId).map(site => {
            const userDir = path.join(STORAGE_ROOT, user.username);
            const siteDir = path.join(userDir, site.name); // Using name as folder name
            const sizeBytes = getSiteSize(siteDir);
            return { ...site, storageUsed: parseFloat((sizeBytes / (1024 * 1024)).toFixed(2)) };
        });
        res.json(userSites);
    } else {
        res.json(sites);
    }
});

// DEPLOY ENDPOINT: Upload ZIP -> Extract to D:/KolabPanel/User/Site
app.post('/api/sites/deploy', upload.single('file'), (req, res) => {
    const { userId, name, framework, subdomain } = req.body;
    const file = req.file;

    if (!file) return res.status(400).json({ message: 'No file uploaded' });

    const user = findUserById(userId);
    if (!user) return res.status(404).json({ message: 'User not found' });

    // 1. Prepare Paths
    const userDir = path.join(STORAGE_ROOT, user.username);
    const siteDir = path.join(userDir, name);

    try {
        // 2. Create Directories
        if (!fs.existsSync(userDir)) fs.mkdirSync(userDir, { recursive: true });
        if (fs.existsSync(siteDir)) {
             // Cleanup old site version if exists (simple overwrite logic)
             fs.rmSync(siteDir, { recursive: true, force: true });
        }
        fs.mkdirSync(siteDir, { recursive: true });

        // 3. Extract Zip
        const zip = new AdmZip(file.path);
        zip.extractAllTo(siteDir, true);

        // 4. Cleanup Temp Zip
        fs.unlinkSync(file.path);

        // 5. Save Metadata
        const newSite = {
            id: `s${Date.now()}`,
            userId,
            name, // Folder name
            framework,
            subdomain,
            status: 'ACTIVE',
            createdAt: new Date().toISOString().split('T')[0],
            storageUsed: 0
        };
        sites.push(newSite);

        res.json(newSite);
    } catch (err) {
        console.error("Deploy Error:", err);
        res.status(500).json({ message: 'Deployment failed: ' + err.message });
    }
});

// 3. File Manager API (Real FS)
app.get('/api/files', (req, res) => {
    const { siteId, path: relPath } = req.query;
    const site = sites.find(s => s.id === siteId);
    
    if (!site) return res.status(404).json({ message: 'Site not found' });

    const pathInfo = getSafePath(site.userId, site.name, relPath || '/');
    if (!pathInfo) return res.status(403).json({ message: 'Access denied' });

    if (!fs.existsSync(pathInfo.fullPath)) {
        return res.json([]); // Empty if path doesn't exist yet
    }

    try {
        const dirents = fs.readdirSync(pathInfo.fullPath, { withFileTypes: true });
        const files = dirents.map(dirent => {
            const stats = fs.statSync(path.join(pathInfo.fullPath, dirent.name));
            return {
                id: `${relPath}-${dirent.name}`, // unique-ish id
                name: dirent.name,
                type: dirent.isDirectory() ? 'folder' : 'file',
                size: dirent.isDirectory() ? '-' : (stats.size > 1024 * 1024 ? (stats.size / (1024*1024)).toFixed(2) + ' MB' : (stats.size / 1024).toFixed(2) + ' KB'),
                path: relPath === '/' ? '/' : relPath, // Parent path
                createdAt: stats.birthtime.toISOString()
            };
        });
        res.json(files);
    } catch (err) {
        res.status(500).json({ message: 'Error reading directory' });
    }
});

app.post('/api/files/folder', (req, res) => {
    const { siteId, path: relPath, folderName } = req.body;
    const site = sites.find(s => s.id === siteId);
    if (!site) return res.status(404).json({ message: 'Site not found' });

    const pathInfo = getSafePath(site.userId, site.name, relPath);
    const newFolderPath = path.join(pathInfo.fullPath, folderName);

    if (!fs.existsSync(newFolderPath)) {
        fs.mkdirSync(newFolderPath);
        res.json({ success: true });
    } else {
        res.status(400).json({ message: 'Folder already exists' });
    }
});

app.post('/api/files/upload', upload.single('file'), (req, res) => {
    const { siteId, path: relPath } = req.body;
    const file = req.file;
    const site = sites.find(s => s.id === siteId);
    
    if (!site || !file) return res.status(400).json({ message: 'Missing data' });

    const pathInfo = getSafePath(site.userId, site.name, relPath);
    const destPath = path.join(pathInfo.fullPath, file.originalname);

    // Move from temp to destination
    fs.renameSync(file.path, destPath);
    res.json({ success: true });
});

app.delete('/api/files', (req, res) => {
    const { siteId, path: relPath, name } = req.body;
    const site = sites.find(s => s.id === siteId);
    if (!site) return res.status(404).json({ message: 'Site not found' });

    const pathInfo = getSafePath(site.userId, site.name, relPath);
    const itemPath = path.join(pathInfo.fullPath, name);

    if (fs.existsSync(itemPath)) {
        fs.rmSync(itemPath, { recursive: true, force: true });
        res.json({ success: true });
    } else {
        res.status(404).json({ message: 'File not found' });
    }
});

app.put('/api/files/rename', (req, res) => {
    const { siteId, path: relPath, oldName, newName } = req.body;
    const site = sites.find(s => s.id === siteId);
    if (!site) return res.status(404).json({ message: 'Site not found' });

    const pathInfo = getSafePath(site.userId, site.name, relPath);
    const oldPath = path.join(pathInfo.fullPath, oldName);
    const newPath = path.join(pathInfo.fullPath, newName);

    if (fs.existsSync(oldPath)) {
        fs.renameSync(oldPath, newPath);
        res.json({ success: true });
    } else {
        res.status(404).json({ message: 'File not found' });
    }
});

// 4. Admin API (Stats)
app.get('/api/admin/stats', (req, res) => {
    // Note: Tunnels & Apache stats are now fetched from their respective external APIs by the frontend
    res.json({
        totalUsers: users.length,
        totalSites: sites.length,
        activeRevenue: '4.2M'
    });
});
app.get('/api/admin/users', (req, res) => res.json(users.map(({password, ...u}) => u)));
app.get('/api/admin/payments', (req, res) => res.json(payments));
app.get('/api/plans', (req, res) => res.json(plans));
app.get('/api/domains', (req, res) => res.json(domains));

app.listen(PORT, () => {
    console.log(`KolabPanel API running on http://localhost:${PORT}`);
    console.log(`Storage Root: ${STORAGE_ROOT}`);
});