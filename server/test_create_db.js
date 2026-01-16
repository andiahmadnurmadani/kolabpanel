require('./loadEnv');
const db = require('./db');
const { createProjectDatabase } = require('./utils/mysqlUserManager');

(async () => {
    try {
        console.log('=== TESTING DATABASE CREATION ===\n');

        // Get a test user's MySQL credentials
        const [users] = await db.execute(`
            SELECT id, username, mysql_username, mysql_password 
            FROM users 
            WHERE mysql_username IS NOT NULL 
            LIMIT 1
        `);

        if (users.length === 0) {
            console.log('❌ No users with MySQL credentials found!');
            process.exit(1);
        }

        const user = users[0];
        console.log('Test User:', user.username);
        console.log('MySQL Username:', user.mysql_username);
        console.log('MySQL Password:', user.mysql_password);
        console.log('');

        // Test subdomain
        const testSubdomain = 'testproject123';
        const expectedDbName = `db_${testSubdomain}`;

        console.log(`Creating database for subdomain: ${testSubdomain}`);
        console.log(`Expected database name: ${expectedDbName}\n`);

        // Try to create the database
        console.log('Calling createProjectDatabase...');
        await createProjectDatabase(user.mysql_username, testSubdomain);
        console.log('✅ createProjectDatabase completed without error\n');

        // Verify the database exists
        console.log('Checking if database exists in MySQL...');
        const [databases] = await db.execute(`
            SELECT SCHEMA_NAME 
            FROM information_schema.SCHEMATA 
            WHERE SCHEMA_NAME = ?
        `, [expectedDbName]);

        if (databases.length > 0) {
            console.log(`✅ Database ${expectedDbName} EXISTS in MySQL!\n`);
        } else {
            console.log(`❌ Database ${expectedDbName} NOT FOUND in MySQL!\n`);
        }

        // Check user privileges
        console.log('Checking user privileges on the database...');
        const [grants] = await db.execute(`
            SHOW GRANTS FOR '${user.mysql_username}'@'localhost'
        `);

        console.log('User grants:');
        grants.forEach(grant => {
            console.log(`  - ${Object.values(grant)[0]}`);
        });

        console.log('\n=== INSTRUCTIONS TO CHECK IN PHPMYADMIN ===');
        console.log('1. Open phpMyAdmin');
        console.log('2. Login with:');
        console.log(`   Username: ${user.mysql_username}`);
        console.log(`   Password: ${user.mysql_password}`);
        console.log(`3. Look for database: ${expectedDbName}`);
        console.log('4. If you don\'t see it, check if you\'re logged in as root instead');

        process.exit(0);

    } catch (err) {
        console.error('❌ Error:', err.message);
        console.error('Stack:', err.stack);
        process.exit(1);
    }
})();
