require('./loadEnv');
const db = require('./db');

(async () => {
    try {
        console.log('=== REPAIRING DATABASE PERMISSIONS ===\n');

        // 1. Get all users with MySQL credentials
        const [users] = await db.execute(`
            SELECT id, username, mysql_username 
            FROM users 
            WHERE mysql_username IS NOT NULL
        `);

        console.log(`Found ${users.length} users with MySQL accounts.`);

        for (const user of users) {
            console.log(`\nProcessing user: ${user.username} (${user.mysql_username})`);

            // 2. Get all sites with databases for this user
            const [sites] = await db.execute(`
                SELECT subdomain, hasDatabase 
                FROM sites 
                WHERE user_id = ? AND hasDatabase = TRUE
             `, [user.id]);

            if (sites.length === 0) {
                console.log('  No project databases found for this user.');
                continue;
            }

            console.log(`  Found ${sites.length} project databases. Fixing permissions...`);

            for (const site of sites) {
                const dbName = `db_${site.subdomain.toLowerCase().replace(/[^a-z0-9]/g, '')}`;

                try {
                    // Check if DB actually exists first to avoid errors
                    const [dbExists] = await db.execute(
                        `SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = ?`,
                        [dbName]
                    );

                    if (dbExists.length === 0) {
                        console.log(`  ⚠️ Database ${dbName} marked in sites but NOT FOUND in MySQL. Skipping.`);
                        continue;
                    }

                    // 3. Grant privileges
                    // We grant for 'localhost' which is standard for local phpMyAdmin
                    await db.execute(
                        `GRANT ALL PRIVILEGES ON \`${dbName}\`.* TO '${user.mysql_username}'@'localhost'`
                    );

                    console.log(`  ✅ Granted privileges on ${dbName}`);

                } catch (err) {
                    console.error(`  ❌ Failed to grant privileges on ${dbName}:`, err.message);
                }
            }
        }

        // 4. Flush privileges to ensure changes take effect
        await db.execute('FLUSH PRIVILEGES');
        console.log('\n✅ Privileges flushed. Repair complete!');

        // 5. Verification: Dump grants for one user if exists
        if (users.length > 0) {
            const sampleUser = users[0];
            console.log(`\nVerifying grants for ${sampleUser.mysql_username}:`);
            const [grants] = await db.execute(`SHOW GRANTS FOR '${sampleUser.mysql_username}'@'localhost'`);
            grants.forEach(g => console.log('  ' + Object.values(g)[0]));
        }

        process.exit(0);

    } catch (err) {
        console.error('❌ Fatal Error:', err.message);
        process.exit(1);
    }
})();
