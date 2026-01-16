require('./loadEnv').loadEnv();
const db = require('./db');
const { sendMySQLCredentials } = require('./utils/emailService');

(async () => {
    try {
        // Get user data
        const [users] = await db.execute(
            'SELECT username, email, mysql_username, mysql_password, mysql_database FROM users WHERE username = ?',
            ['faisal']
        );

        if (users.length === 0) {
            console.log('❌ User not found');
            process.exit(1);
        }

        const user = users[0];

        console.log('User found:');
        console.log('- Email:', user.email);
        console.log('- MySQL User:', user.mysql_username);
        console.log('- MySQL DB:', user.mysql_database);
        console.log('- MySQL Password (hashed):', user.mysql_password ? 'EXISTS (hashed)' : 'NOT SET');

        if (!user.mysql_username || !user.mysql_database) {
            console.log('\n❌ MySQL credentials not created yet!');
            process.exit(1);
        }

        console.log('\n⚠️  PROBLEM: MySQL password is hashed in database, cannot retrieve original password!');
        console.log('The original random password was only available during verification and should have been emailed.');
        console.log('\nOptions:');
        console.log('1. Check spam folder for MySQL credentials email');
        console.log('2. Reset MySQL password manually');
        console.log('3. Delete user and re-register');

        await db.end();
        process.exit(0);
    } catch (err) {
        console.error('Error:', err.message);
        process.exit(1);
    }
})();
