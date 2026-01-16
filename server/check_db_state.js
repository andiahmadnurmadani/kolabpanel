require('./loadEnv');
const db = require('./db');

(async () => {
    try {
        console.log('=== CHECKING CURRENT DATABASE STATE ===\n');

        // List all databases
        console.log('All databases in MySQL:');
        const [allDbs] = await db.execute(`
            SELECT SCHEMA_NAME 
            FROM information_schema.SCHEMATA 
            ORDER BY SCHEMA_NAME
        `);

        allDbs.forEach(dbRow => {
            console.log(`  - ${dbRow.SCHEMA_NAME}`);
        });

        console.log('\n=== PROJECT DATABASES (db_*) ===');
        const [projectDbs] = await db.execute(`
            SELECT SCHEMA_NAME 
            FROM information_schema.SCHEMATA 
            WHERE SCHEMA_NAME LIKE 'db_%'
            ORDER BY SCHEMA_NAME
        `);

        if (projectDbs.length > 0) {
            projectDbs.forEach(dbRow => {
                console.log(`  ✅ ${dbRow.SCHEMA_NAME}`);
            });
        } else {
            console.log('  (No project databases found)');
        }

        console.log('\n=== USERS WITH MYSQL CREDENTIALS ===');
        const [users] = await db.execute(`
            SELECT username, mysql_username, mysql_database 
            FROM users 
            WHERE mysql_username IS NOT NULL
        `);

        if (users.length > 0) {
            users.forEach(user => {
                console.log(`  User: ${user.username}`);
                console.log(`    MySQL User: ${user.mysql_username}`);
                console.log(`    MySQL DB: ${user.mysql_database}`);
                console.log('');
            });
        } else {
            console.log('  (No users with MySQL credentials)');
        }

        console.log('\n=== SITES WITH DATABASES ===');
        const [sites] = await db.execute(`
            SELECT id, subdomain, hasDatabase 
            FROM sites 
            ORDER BY id
        `);

        if (sites.length > 0) {
            sites.forEach(site => {
                const dbStatus = site.hasDatabase ? '✅ HAS DB' : '❌ NO DB';
                console.log(`  ${site.subdomain} - ${dbStatus}`);
            });
        } else {
            console.log('  (No sites found)');
        }

        process.exit(0);

    } catch (err) {
        console.error('❌ Error:', err.message);
        process.exit(1);
    }
})();
