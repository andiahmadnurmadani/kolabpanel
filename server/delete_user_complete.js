require('./loadEnv').loadEnv();
const db = require('./db');
const { deleteMySQLUser } = require('./utils/mysqlUserManager');
const fs = require('fs').promises;
const path = require('path');

async function deleteUserCompletely(username) {
    try {
        console.log(`\n🗑️  Deleting user: ${username}\n`);

        // 1. Get user data first
        const [users] = await db.execute(
            'SELECT id, username FROM users WHERE username = ?',
            [username]
        );

        if (users.length === 0) {
            console.log('❌ User not found in database');
            return;
        }

        const user = users[0];

        // 2. Delete MySQL user and database
        console.log('📊 Deleting MySQL user and database...');
        try {
            await deleteMySQLUser(username);
            console.log('✅ MySQL user and database deleted');
        } catch (err) {
            console.log('⚠️  MySQL deletion failed (might not exist):', err.message);
        }

        // 3. Delete user folder
        const userFolder = path.join(__dirname, '..', 'userdata', username);
        console.log(`📁 Deleting user folder: ${userFolder}`);
        try {
            await fs.rm(userFolder, { recursive: true, force: true });
            console.log('✅ User folder deleted');
        } catch (err) {
            console.log('⚠️  Folder deletion failed (might not exist):', err.message);
        }

        // 4. Delete from users table
        console.log('🗄️  Deleting from users table...');
        await db.execute('DELETE FROM users WHERE username = ?', [username]);
        console.log('✅ User deleted from database');

        console.log(`\n✅ User "${username}" completely deleted!\n`);

    } catch (err) {
        console.error('\n❌ Error:', err.message);
        throw err;
    }
}

// Main
(async () => {
    const username = process.argv[2] || 'faisal';

    try {
        await deleteUserCompletely(username);
        await db.end();
        process.exit(0);
    } catch (err) {
        console.error('Fatal error:', err);
        process.exit(1);
    }
})();
