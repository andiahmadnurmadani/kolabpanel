require('./loadEnv').loadEnv();
const db = require('./db');

(async () => {
    try {
        console.log('Deleting user: faisal');

        const [result] = await db.execute(
            'DELETE FROM users WHERE username = ?',
            ['faisal']
        );

        console.log('✅ User deleted successfully!');
        console.log('Rows affected:', result.affectedRows);

        await db.end();
        process.exit(0);
    } catch (err) {
        console.error('❌ Error:', err.message);
        process.exit(1);
    }
})();
