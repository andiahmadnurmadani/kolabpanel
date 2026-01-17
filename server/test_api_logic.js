require('./loadEnv');
const db = require('./db');

(async () => {
    try {
        console.log('=== TESTING API LOGIC ===\n');

        // 1. Get a random site
        const [sites] = await db.execute('SELECT * FROM sites LIMIT 1');
        if (sites.length === 0) {
            console.log('No sites found to test.');
            process.exit(0);
        }

        const site = sites[0];
        console.log('Test Site:', site.name, `(ID: ${site.id})`);
        console.log('Site keys:', Object.keys(site).join(', '));

        // 2. Test the query that was failing
        console.log(`\nQuerying user for site.user_id: ${site.user_id}`);

        const [users] = await db.execute(
            'SELECT mysql_username FROM users WHERE id = ?',
            [site.user_id]
        );

        if (users.length > 0) {
            console.log(`✅ Success! Found user: ${users[0].mysql_username}`);
        } else {
            console.log('❌ Failed! User not found (but this might be expected if user deleted)');
        }

        process.exit(0);
    } catch (err) {
        console.error('❌ Error:', err.message);
        process.exit(1);
    }
})();
