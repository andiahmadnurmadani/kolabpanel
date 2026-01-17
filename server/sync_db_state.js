require('./loadEnv');
const db = require('./db');
const { createProjectDatabase } = require('./utils/mysqlUserManager');

(async () => {
    try {
        console.log('=== DATABASE SYNCHRONIZATION STARTED ===\n');

        // 1. Get all sites from the application database
        const [sites] = await db.execute('SELECT id, name, subdomain, hasDatabase, user_id FROM sites');
        console.log(`Found ${sites.length} sites in the application.`);

        // 2. Get all project databases from MySQL
        const [mysqlDbs] = await db.execute(`
            SELECT SCHEMA_NAME 
            FROM information_schema.SCHEMATA 
            WHERE SCHEMA_NAME LIKE 'db_%'
        `);
        // Map db name to true for quick lookup
        const existingMysqlDbs = new Set(mysqlDbs.map(d => d.SCHEMA_NAME));
        console.log(`Found ${mysqlDbs.length} project databases in MySQL.`);

        // 3. Sync: Sites that SHOULD have a DB
        for (const site of sites) {
            const expectedDbName = `db_${site.subdomain.toLowerCase().replace(/[^a-z0-9]/g, '')}`;

            if (site.hasDatabase) {
                // Check if it exists in MySQL
                if (!existingMysqlDbs.has(expectedDbName)) {
                    console.log(`\n[FIXING] Site '${site.name}' hasDatabase=TRUE but MySQL DB '${expectedDbName}' is MISSING.`);

                    // Needs creation
                    try {
                        // Get user for this site
                        const [users] = await db.execute('SELECT mysql_username FROM users WHERE id = ?', [site.user_id]);
                        if (users.length > 0 && users[0].mysql_username) {
                            console.log(`   Creating database for user ${users[0].mysql_username}...`);
                            await createProjectDatabase(users[0].mysql_username, site.subdomain);
                            console.log('   ✅ Database created and synced.');
                        } else {
                            console.log('   ⚠️ Cannot create DB: User has no MySQL username.');
                        }
                    } catch (err) {
                        console.error(`   ❌ Failed to create DB: ${err.message}`);
                    }
                } else {
                    // exists, good.
                    // console.log(`[OK] Site '${site.name}' matches MySQL DB '${expectedDbName}'`);
                }
            } else {
                // site.hasDatabase is FALSE
                // Check if there is an orphaned DB that matches this subdomain
                if (existingMysqlDbs.has(expectedDbName)) {
                    console.log(`\n[FOUND] Site '${site.name}' hasDatabase=FALSE but MySQL DB '${expectedDbName}' EXISTS.`);

                    // We should probably update the site to reflect reality rather than deleting data
                    await db.execute('UPDATE sites SET hasDatabase = TRUE WHERE id = ?', [site.id]);
                    console.log('   ✅ Updated site.hasDatabase to TRUE to match reality.');
                }
            }
        }

        // 4. Check for completely orphaned databases (no site matches)
        // (Optional, maybe just list them for now)
        for (const dbRow of mysqlDbs) {
            const dbName = dbRow.SCHEMA_NAME;
            // Clean logic to extract subdomain is tricky if we don't know exact mapping, 
            // but we can try to find if ANY site maps to this.

            // This is harder to do in reverse without exact subdomain, 
            // but we can iterate sites again to see if any claimed this db.
            let claimed = false;
            for (const site of sites) {
                const expected = `db_${site.subdomain.toLowerCase().replace(/[^a-z0-9]/g, '')}`;
                if (expected === dbName) {
                    claimed = true;
                    break;
                }
            }

            if (!claimed) {
                console.log(`\n[WARNING] Orphaned Database Found: '${dbName}' (No site claims this).`);
                // We won't delete it automatically, just warn.
            }
        }

        // 5. Final Permission Fix (Run the repair logic we built earlier implicitly)
        // Just to be safe, let's flush.
        await db.execute('FLUSH PRIVILEGES');

        console.log('\n=== SYNCHRONIZATION COMPLETE ===');
        process.exit(0);

    } catch (err) {
        console.error('❌ Sync Error:', err.message);
        process.exit(1);
    }
})();
