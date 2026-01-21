const pool = require('../db');
const os = require('os');
const fs = require('fs');
const { APACHE_SITES_PATH, APACHE_HTTPD_PATH } = require('../config/paths');
const { getCpuUsage } = require('../utils/helpers');

// --- STATS ---
exports.getStats = async (req, res) => {
    try {
        const [[{count: totalUsers}]] = await pool.execute('SELECT COUNT(*) as count FROM users');
        const [[{count: totalSites}]] = await pool.execute('SELECT COUNT(*) as count FROM sites');
        const [[{count: totalTunnels}]] = await pool.execute('SELECT COUNT(*) as count FROM tunnels');
        const [[{revenue}]] = await pool.execute("SELECT SUM(amount) as revenue FROM payments WHERE status = 'VERIFIED'");
        
        let totalApacheSites = 0;
        try {
            if (fs.existsSync(APACHE_SITES_PATH)) {
                totalApacheSites = fs.readdirSync(APACHE_SITES_PATH).filter(f => f.endsWith('.conf')).length;
            }
        } catch {}

        res.json({ totalUsers, totalSites, activeRevenue: (revenue || 0).toLocaleString(), totalTunnels, totalApacheSites });
    } catch (e) { res.status(500).json({message: e.message}); }
};

exports.getSystemHealth = async (req, res) => {
    try {
        const cpuUsage = await getCpuUsage();
        res.json({
            cpu: parseFloat(cpuUsage),
            memory: { total: os.totalmem(), free: os.freemem(), used: os.totalmem() - os.freemem() },
            uptime: os.uptime(),
            platform: `${os.type()} ${os.release()} (${os.arch()})`
        });
    } catch (e) { res.status(500).json({ message: "Failed to fetch metrics" }); }
};

// --- TUNNELS ---
exports.listTunnels = async (req, res) => {
    try {
        const [tunnels] = await pool.execute('SELECT * FROM tunnels ORDER BY created_at DESC');
        res.json(tunnels);
    } catch (e) { res.status(500).json({message: e.message}); }
};

exports.createTunnel = async (req, res) => {
    const { hostname, service } = req.body;
    try {
        await pool.execute('INSERT INTO tunnels (hostname, service) VALUES (?, ?)', [hostname, service]);
        res.json({ success: true });
    } catch (e) { res.status(500).json({message: e.message}); }
};

exports.editTunnel = async (req, res) => {
    const { hostname, newHostname, service } = req.body;
    try {
        await pool.execute('UPDATE tunnels SET hostname = ?, service = ? WHERE hostname = ?', [newHostname, service, hostname]);
        res.json({ success: true });
    } catch (e) { res.status(500).json({message: e.message}); }
};

exports.deleteTunnel = async (req, res) => {
    const { hostname } = req.body;
    try {
        await pool.execute('DELETE FROM tunnels WHERE hostname = ?', [hostname]);
        res.json({ success: true });
    } catch (e) { res.status(500).json({message: e.message}); }
};

// --- USERS & COMMON ---
exports.listUsers = async (req, res) => {
    try {
        const [users] = await pool.execute('SELECT * FROM users');
        const safeUsers = users.map(({password, ...u}) => u);
        res.json(safeUsers);
    } catch (e) { res.status(500).json({message: e.message}); }
};

exports.getPlans = async (req, res) => {
    try {
        const [plans] = await pool.execute('SELECT * FROM plans');
        res.json(plans);
    } catch (e) { res.status(500).json({message: e.message}); }
};

exports.getDomains = async (req, res) => {
    try {
        const [domains] = await pool.execute('SELECT * FROM domains');
        const mapped = domains.map(d => ({...d, isPrimary: !!d.is_primary}));
        res.json(mapped);
    } catch (e) { res.status(500).json({message: e.message}); }
};

exports.getPayments = async (req, res) => {
    try {
        const [payments] = await pool.execute(`
            SELECT p.*, u.username 
            FROM payments p 
            LEFT JOIN users u ON p.user_id = u.id 
            ORDER BY p.date DESC
        `);
        const mapped = payments.map(p => ({
            ...p,
            userId: p.user_id,
            proofUrl: p.proof_url
        }));
        res.json(mapped);
    } catch (e) { res.status(500).json({message: e.message}); }
};

exports.listApacheSites = async (req, res) => {
    try {
        if (!fs.existsSync(APACHE_SITES_PATH)) return res.json([]);
        const files = fs.readdirSync(APACHE_SITES_PATH).filter(f => f.endsWith('.conf'));
        res.json(files);
    } catch (e) { res.status(500).json({message: e.message}); }
};

exports.getApacheSite = async (req, res) => {
    try {
        const content = fs.readFileSync(path.join(APACHE_SITES_PATH, req.params.filename), 'utf8');
        res.json({ content });
    } catch (e) { res.status(500).json({message: e.message}); }
};