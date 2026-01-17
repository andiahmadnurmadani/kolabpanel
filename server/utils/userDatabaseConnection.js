const mysql = require('mysql2/promise');
const db = require('../db');

/**
 * Creates a MySQL connection config for a specific site/user.
 * @param {string} siteId - ID of the site
 * @param {string} userId - ID of the authenticated user
 * @returns {Promise<Object>} MySQL Connection Config
 */
const getUserDbConfig = async (siteId, userId) => {
    // Verify ownership and get credentials
    const [sites] = await db.execute(
        'SELECT * FROM sites WHERE id = ? AND user_id = ?',
        [siteId, userId]
    );

    if (sites.length === 0) {
        throw new Error('Access denied or site not found');
    }

    const site = sites[0];

    // Check using snake_case column name from DB
    if (!site.has_database) {
        throw new Error('This site does not have a database enabled');
    }

    // Find the MySQL user credentials (stored in users table)
    const [users] = await db.execute('SELECT mysql_username, mysql_password FROM users WHERE id = ?', [userId]);
    if (users.length === 0) {
        throw new Error('MySQL credentials not found');
    }

    const dbName = `db_${site.subdomain.toLowerCase().replace(/[^a-z0-9]/g, '')}`;

    return {
        host: process.env.DB_HOST || 'localhost',
        user: users[0].mysql_username,
        password: users[0].mysql_password,
        database: dbName
    };
};

/**
 * Executes a query on the user's database.
 * @param {Object} config - Connection config object
 * @param {string} query - SQL Query
 * @param {Array} params - Query Parameters
 * @returns {Promise<Object>} { results, fields }
 */
const runUserQuery = async (config, query, params = []) => {
    let connection;
    try {
        connection = await mysql.createConnection(config);
        const [results, fields] = await connection.execute(query, params);
        return { results, fields };
    } catch (err) {
        throw err;
    } finally {
        if (connection) await connection.end();
    }
};

module.exports = { getUserDbConfig, runUserQuery };
