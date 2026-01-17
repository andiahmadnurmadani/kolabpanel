require('./loadEnv');
const db = require('./db');

(async () => {
    try {
        console.log('=== CHECKING SITES TABLE SCHEMA ===\n');

        const [columns] = await db.execute(`
            SELECT COLUMN_NAME 
            FROM information_schema.COLUMNS 
            WHERE TABLE_SCHEMA = 'kolabpanel' AND TABLE_NAME = 'sites'
        `);

        console.log('Columns in sites table:');
        columns.forEach(col => console.log(`  - ${col.COLUMN_NAME}`));

        // Also check if there's a user_id or similar

        process.exit(0);
    } catch (err) {
        console.error('❌ Error:', err.message);
        process.exit(1);
    }
})();
