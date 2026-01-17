const mysql = require('mysql2/promise');
require('./loadEnv').loadEnv();

async function syncPasswords() {
    console.log('=== SYNCING MYSQL PASSWORDS ===');
    const db = require('./db');

    // Connect as Root
    const rootConn = await mysql.createConnection({
        host: process.env.DB_HOST,
        user: process.env.DB_USER,
        password: process.env.DB_PASSWORD
    });

    try {
        const [users] = await db.execute('SELECT mysql_username, mysql_password FROM users WHERE mysql_username IS NOT NULL');

        for (const user of users) {
            if (!user.mysql_username || !user.mysql_password) continue;
            console.log(`Syncing password for ${user.mysql_username}...`);

            try {
                // Check if user exists
                const [exists] = await rootConn.execute(`SELECT User FROM mysql.user WHERE User = ? AND Host = 'localhost'`, [user.mysql_username]);
                if (exists.length > 0) {
                    await rootConn.execute(`ALTER USER '${user.mysql_username}'@'localhost' IDENTIFIED BY '${user.mysql_password}'`);
                    console.log(`✅ Password updated for ${user.mysql_username}`);
                } else {
                    console.warn(`⚠️ User ${user.mysql_username} does not exist in MySQL. Creating...`);
                    await rootConn.execute(`CREATE USER '${user.mysql_username}'@'localhost' IDENTIFIED BY '${user.mysql_password}'`);
                    console.log(`✅ User created: ${user.mysql_username}`);
                }
            } catch (err) {
                console.error(`❌ Failed to sync ${user.mysql_username}:`, err.message);
            }
        }
    } catch (err) {
        console.error('Script Error:', err);
    } finally {
        await rootConn.end();
        process.exit(0);
    }
}

syncPasswords();
