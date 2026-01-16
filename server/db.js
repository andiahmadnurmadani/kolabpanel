const mysql = require('mysql2/promise');

// Create connection pool
const pool = mysql.createPool({
    host: process.env.DB_HOST || 'localhost',
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASSWORD || '',
    database: process.env.DB_NAME || 'kolabpanel',
    waitForConnections: true,
    connectionLimit: 10,
    queueLimit: 0
});

// Test connection
pool.getConnection()
    .then(connection => {
        console.log('[db] Database connected successfully');
        connection.release();
    })
    .catch(err => {
        console.error('[db] Database connection failed:', err.message);
        console.error('[db] Please make sure MySQL is running and credentials in .env are correct');
    });

module.exports = pool;
