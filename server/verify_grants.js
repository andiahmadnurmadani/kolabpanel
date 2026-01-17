require('./loadEnv');
const db = require('./db');

(async () => {
    try {
        console.log('=== VERIFYING DATABASE GRANTS ===\n');

        // Get the user again
        const [users] = await db.execute(`
            SELECT mysql_username 
            FROM users 
            WHERE mysql_username IS NOT NULL 
            LIMIT 1
        `);

        if (users.length === 0) {
            console.log('No users found.');
            process.exit(0);
        }

        const mysqlUser = users[0].mysql_username;
        console.log(`Checking grants for: ${mysqlUser}\n`);

        const [grants] = await db.execute(`SHOW GRANTS FOR '${mysqlUser}'@'localhost'`);

        grants.forEach(g => {
            const grantStr = Object.values(g)[0];
            console.log(grantStr);
        });

        process.exit(0);
    } catch (err) {
        console.error('❌ Error:', err.message);
        process.exit(1);
    }
})();
