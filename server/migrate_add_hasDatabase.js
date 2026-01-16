require('./loadEnv');
const db = require('./db');

(async () => {
    try {
        console.log('Adding hasDatabase column to sites table...\n');

        // Add hasDatabase column
        await db.execute(`
            ALTER TABLE sites 
            ADD COLUMN hasDatabase BOOLEAN DEFAULT FALSE
        `);

        console.log('✅ Column hasDatabase added successfully!');

        // Check if any databases exist and update sites accordingly
        console.log('\nChecking for existing project databases...');

        const [databases] = await db.execute(`
            SELECT SCHEMA_NAME 
            FROM information_schema.SCHEMATA 
            WHERE SCHEMA_NAME LIKE 'db_%'
        `);

        if (databases.length > 0) {
            console.log(`Found ${databases.length} project databases:`);
            databases.forEach(db => console.log(`  - ${db.SCHEMA_NAME}`));

            // Update sites that have databases
            for (const dbRow of databases) {
                const dbName = dbRow.SCHEMA_NAME;
                const subdomain = dbName.replace('db_', '').replace(/_/g, '');

                await db.execute(`
                    UPDATE sites 
                    SET hasDatabase = TRUE 
                    WHERE subdomain = ?
                `, [subdomain]);

                console.log(`  ✅ Updated site with subdomain: ${subdomain}`);
            }
        } else {
            console.log('No project databases found.');
        }

        console.log('\n✅ Migration completed successfully!');
        process.exit(0);

    } catch (err) {
        console.error('❌ Migration failed:', err.message);
        process.exit(1);
    }
})();
