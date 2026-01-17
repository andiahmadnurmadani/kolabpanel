const http = require('http');
require('./loadEnv').loadEnv();
const db = require('./db');
const jwt = require('jsonwebtoken');

const SECRET_KEY = process.env.JWT_SECRET;
const API_PORT = process.env.PORT || 5000;

async function testDatabaseCreation() {
    console.log("=== TESTING DATABASE CREATION FLOW ===");

    // 1. Find a test user and site WITHOUT a database
    const [rows] = await db.execute(`
        SELECT u.id as user_id, u.role, s.id as site_id, s.subdomain, s.name, s.has_database 
        FROM users u 
        JOIN sites s ON u.id = s.user_id 
        WHERE s.has_database = 0 
        LIMIT 1
    `);

    if (rows.length === 0) {
        console.error("❌ No suitable site WITHOUT database found for testing.");
        // Try with one that HAS database, but we'll try to drop it first? No, too risky for user data.
        // Let's create a fake site for testing?
        console.log("⚠️ Creating a temporary test site...");
        // This is complex, let's just create a mock request logic first.
        process.exit(1);
    }

    const target = rows[0];
    console.log(`✅ Found target: User ${target.user_id}, Site ${target.site_id} (${target.subdomain})`);

    // 2. Generate Token
    const token = jwt.sign({ id: target.user_id, role: target.role }, SECRET_KEY, { expiresIn: '1h' });

    // 3. Make PUT Request to create DB
    const requestData = JSON.stringify({ hasDatabase: true });

    const options = {
        hostname: '127.0.0.1',
        port: API_PORT,
        path: `/api/sites/${target.site_id}`,
        method: 'PUT',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'Content-Length': requestData.length
        }
    };

    console.log(`Sending PUT /api/sites/${target.site_id} with hasDatabase: true`);

    const req = http.request(options, (res) => {
        let body = '';
        res.on('data', chunk => body += chunk);
        res.on('end', () => {
            console.log(`Response Status: ${res.statusCode}`);
            console.log(`Response Body: ${body}`);
            if (res.statusCode === 200) {
                console.log("✅ API reports success");
            } else {
                console.error("❌ API failed");
            }
        });
    });

    req.on('error', (e) => console.error(`❌ Request Error: ${e.message}`));
    req.write(requestData);
    req.end();
}

testDatabaseCreation();
