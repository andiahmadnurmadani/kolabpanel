require('./loadEnv').loadEnv();
const db = require('./db');

(async () => {
    try {
        const [users] = await db.execute(
            'SELECT username, email, email_verified, status, mysql_username, mysql_database FROM users WHERE username = ?',
            ['faisal']
        );

        if (users.length === 0) {
            console.log('❌ User not found');
        } else {
            console.log('✅ User found:');
            console.log(JSON.stringify(users[0], null, 2));
        }

        await db.end();
        process.exit(0);
    } catch (err) {
        console.error('Error:', err.message);
        process.exit(1);
    }
})();
