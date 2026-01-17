const { getUserDbConfig, runUserQuery } = require('../utils/userDatabaseConnection');

const databaseController = {
    // List all tables
    listTables: async (req, res) => {
        try {
            const { siteId } = req.params;
            const userId = req.user.id;

            const dbConfig = await getUserDbConfig(siteId, userId);

            const { results } = await runUserQuery(dbConfig,
                `SELECT 
                    TABLE_NAME as name, 
                    TABLE_ROWS as rows_count, 
                    round(((data_length + index_length) / 1024), 2) as size_kb,
                    ENGINE as engine,
                    TABLE_COLLATION as collation
                FROM information_schema.TABLES 
                WHERE TABLE_SCHEMA = ?`,
                [dbConfig.database]
            );

            const tables = results.map(row => ({
                name: row.name,
                rows: row.rows_count || 0,
                size: `${row.size_kb} KB`,
                engine: row.engine,
                collation: row.collation
            }));

            res.json(tables);
        } catch (err) {
            console.error('[DB List Tables Error]', err.message);
            res.status(err.message.includes('Access denied') ? 403 : 500).json({ message: err.message });
        }
    },

    // Get Table Structure
    getTableStructure: async (req, res) => {
        try {
            const { siteId, tableName } = req.params;
            const userId = req.user.id;
            const dbConfig = await getUserDbConfig(siteId, userId);
            const { results } = await runUserQuery(dbConfig, 'DESCRIBE ??', [tableName]);
            res.json(results);
        } catch (err) {
            console.error('[DB Structure Error]', err.message);
            res.status(500).json({ message: err.message });
        }
    },

    // Get Table Data
    getTableData: async (req, res) => {
        try {
            const { siteId, tableName } = req.params;
            const userId = req.user.id;
            const page = parseInt(req.query.page) || 1;
            const limit = parseInt(req.query.limit) || 50;
            const offset = (page - 1) * limit;

            const dbConfig = await getUserDbConfig(siteId, userId);

            const { results } = await runUserQuery(dbConfig,
                `SELECT * FROM ?? LIMIT ? OFFSET ?`,
                [tableName, String(limit), String(offset)]
            );

            const { results: countResults } = await runUserQuery(dbConfig,
                `SELECT COUNT(*) as total FROM ??`,
                [tableName]
            );

            res.json({
                data: results,
                pagination: {
                    page,
                    limit,
                    total: countResults[0].total
                }
            });
        } catch (err) {
            console.error('[DB Data Error]', err.message);
            res.status(500).json({ message: err.message });
        }
    },

    // Execute Arbitrary Query
    executeQuery: async (req, res) => {
        try {
            const { siteId } = req.params;
            const userId = req.user.id;
            const { query } = req.body;

            if (!query) return res.status(400).json({ message: 'Query is required' });

            if (/^\s*use\s+/i.test(query)) {
                return res.status(403).json({ message: 'USE command is not allowed' });
            }

            const dbConfig = await getUserDbConfig(siteId, userId);
            const { results } = await runUserQuery(dbConfig, query);

            res.json({ results });
        } catch (err) {
            console.error('[DB Query Error]', err.message);
            res.status(400).json({ message: err.message });
        }
    },

    // Export Database
    exportDatabase: async (req, res) => {
        try {
            const { siteId } = req.params;
            const userId = req.user.id;

            const dbConfig = await getUserDbConfig(siteId, userId);

            // Get all tables
            const { results: tables } = await runUserQuery(dbConfig, 'SHOW TABLES');
            const tableNames = tables.map(t => Object.values(t)[0]);

            let sqlDump = `-- KolabPanel SQL Dump\n-- Date: ${new Date().toISOString()}\n\n`;
            sqlDump += `SET FOREIGN_KEY_CHECKS=0;\nSET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";\n\n`;

            for (const table of tableNames) {
                // Structure
                const { results: createRes } = await runUserQuery(dbConfig, `SHOW CREATE TABLE \`${table}\``);
                const createSQL = createRes[0]['Create Table'];
                sqlDump += `-- Table structure for table \`${table}\`\n`;
                sqlDump += `DROP TABLE IF EXISTS \`${table}\`;\n`;
                sqlDump += `${createSQL};\n\n`;

                // Data
                const { results: rows } = await runUserQuery(dbConfig, `SELECT * FROM \`${table}\``);
                if (rows.length > 0) {
                    sqlDump += `-- Dumping data for table \`${table}\`\n`;
                    for (const row of rows) {
                        const values = Object.values(row).map(val => {
                            if (val === null) return 'NULL';
                            if (typeof val === 'number') return val;
                            return `'${String(val).replace(/\\/g, '\\\\').replace(/'/g, "\\'")}'`;
                        }).join(', ');
                        sqlDump += `INSERT INTO \`${table}\` VALUES (${values});\n`;
                    }
                    sqlDump += `\n`;
                }
            }

            sqlDump += `SET FOREIGN_KEY_CHECKS=1;\n`;

            res.setHeader('Content-Type', 'application/sql');
            res.setHeader('Content-Disposition', `attachment; filename="db_export_${siteId}.sql"`);
            res.send(sqlDump);

        } catch (err) {
            console.error('[DB Export Error]', err.message);
            res.status(500).send('Export failed: ' + err.message);
        }
    },

    // Import Database
    importDatabase: async (req, res) => {
        try {
            const { siteId } = req.params;
            const userId = req.user.id;
            const { sql } = req.body; // Expect raw SQL text in body

            console.log(`[Import] Starting import for site ${siteId}`);

            if (!sql) return res.status(400).json({ message: "No SQL provided" });

            const dbConfig = await getUserDbConfig(siteId, userId);

            // NEW: Use mysql2/promise directly to create a dedicated connection 
            // with multipleStatements: true. This bypasses the shared pool which might 
            // limit flags or stick to cached configs.
            const mysql = require('mysql2/promise');
            const connection = await mysql.createConnection({
                host: dbConfig.host,
                user: dbConfig.user,
                password: dbConfig.password,
                database: dbConfig.database,
                multipleStatements: true // CRITICAL for SQL dump execution
            });

            try {
                // Execute the full SQL dump
                await connection.query(sql);
                console.log(`[Import] Successfully executed SQL for ${siteId}`);
                res.json({ message: "Import successful" });
            } finally {
                await connection.end();
            }

        } catch (err) {
            console.error('[DB Import Error]', err.message);
            res.status(500).json({ message: "Import failed: " + err.message });
        }
    }
};

module.exports = databaseController;
