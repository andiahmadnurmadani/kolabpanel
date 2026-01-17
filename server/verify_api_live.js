const http = require('http');
const jwt = require('jsonwebtoken');
require('./loadEnv').loadEnv(); // Ensure env is loaded
const db = require('./db');

const SECRET_KEY = process.env.JWT_SECRET;

async function runTest() {
    try {
        console.log("=== STARTING LIVE API VERIFICATION ===");

        // 1. Find a test user and site
        const [rows] = await db.execute(`
            SELECT u.id as user_id, u.role, s.id as site_id, s.subdomain 
            FROM users u 
            JOIN sites s ON u.id = s.user_id 
            WHERE s.has_database = 1 
            LIMIT 1
        `);

        if (rows.length === 0) {
            console.error("❌ No suitable site with database found for testing.");
            process.exit(1);
        }

        const { user_id, role, site_id, subdomain } = rows[0];
        console.log(`✅ Found test target: User ${user_id}, Site ${site_id} (${subdomain})`);

        // 2. Generate Token
        const token = jwt.sign({ id: user_id, role: role }, SECRET_KEY, { expiresIn: '1h' });
        console.log("✅ Generated Test JWT Token");

        // Helper for HTTP requests
        const request = (method, path, body = null) => {
            return new Promise((resolve, reject) => {
                const options = {
                    hostname: '127.0.0.1',
                    port: process.env.PORT || 5000,
                    path: path,
                    method: method,
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    }
                };

                const req = http.request(options, (res) => {
                    let data = '';
                    res.on('data', (chunk) => data += chunk);
                    res.on('end', () => {
                        try {
                            const json = JSON.parse(data);
                            resolve({ status: res.statusCode, body: json });
                        } catch (e) {
                            resolve({ status: res.statusCode, body: data });
                        }
                    });
                });

                req.on('error', reject);

                if (body) {
                    req.write(JSON.stringify(body));
                }
                req.end();
            });
        };

        // 2.5 Test Auth Token Validity
        console.log("\n--- Testing Token Validity (/api/auth/me) ---");
        const authRes = await request('GET', '/api/auth/me');
        if (authRes.status === 200) {
            console.log("✅ Token Valid. User ID from API:", authRes.body.id);
            if (String(authRes.body.id) !== String(user_id)) {
                console.error(`❌ User ID Mismatch! DB: ${user_id}, API: ${authRes.body.id}`);
            }
        } else {
            console.error(`❌ Token Invalid: ${authRes.status}`, authRes.body);
            process.exit(1);
        }

        // 3. Test: List Tables
        console.log(`\n--- Testing GET /tables (Site: ${site_id}, User: ${user_id}) ---`);
        const listRes = await request('GET', `/api/database/${site_id}/tables`);
        if (listRes.status === 200) {
            console.log(`✅ Success: Found ${listRes.body.length} tables`);
            // console.log(listRes.body);
        } else {
            console.error(`❌ Failed: ${listRes.status}`, listRes.body);
        }

        const tableName = 'test_api_verify_' + Date.now();

        // 4. Test: Create Table
        console.log(`\n--- Testing Create Table (${tableName}) ---`);
        const createQuery = `CREATE TABLE ${tableName} (id INT PRIMARY KEY, val VARCHAR(50))`;
        const createRes = await request('POST', `/api/database/${site_id}/query`, { query: createQuery });
        if (createRes.status === 200) {
            console.log("✅ Table Created");
        } else {
            console.error(`❌ Failed to create table: ${createRes.status}`, createRes.body);
            // If failed, basic crud likely blocked, abort
            process.exit(1);
        }

        // 5. Test: Insert Data
        console.log("\n--- Testing Insert Data ---");
        const insertQuery = `INSERT INTO ${tableName} VALUES (1, 'Hello World')`;
        const insertRes = await request('POST', `/api/database/${site_id}/query`, { query: insertQuery });
        if (insertRes.status === 200) {
            console.log("✅ Data Inserted");
        } else {
            console.error(`❌ Failed to insert: ${insertRes.status}`, insertRes.body);
        }

        // 6. Test: Get Data
        console.log("\n--- Testing Get Data ---");
        const getRes = await request('GET', `/api/database/${site_id}/tables/${tableName}/data`);
        if (getRes.status === 200 && getRes.body.data && getRes.body.data.length > 0) {
            console.log("✅ Data Retrieved:", getRes.body.data[0]);
            if (getRes.body.data[0].val === 'Hello World') {
                console.log("✅ Data Content Verified");
            } else {
                console.error("❌ Data Mismatch");
            }
        } else {
            console.error(`❌ Failed to get data: ${getRes.status}`, getRes.body);
        }

        // 7. Test: Drop Table
        console.log("\n--- Testing Drop Table ---");
        const dropQuery = `DROP TABLE ${tableName}`;
        const dropRes = await request('POST', `/api/database/${site_id}/query`, { query: dropQuery });
        if (dropRes.status === 200) {
            console.log("✅ Table Dropped");
        } else {
            console.error(`❌ Failed to drop table: ${dropRes.status}`, dropRes.body);
        }

        console.log("\n=== VERIFICATION COMPLETE ===");
        process.exit(0);

    } catch (err) {
        console.error("Test Error:", err);
        process.exit(1);
    } finally {
        // Close DB pool logic usually keeps process alive, but db.end() might be needed if I knew how to call it on pool 
        // In this simple script process.exit handles it
    }
}

runTest();
