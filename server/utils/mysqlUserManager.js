const mysql = require('mysql2/promise');
const crypto = require('crypto');

function generateSecurePassword(length = 16) {
    // Character set for password generation
    const lowercase = 'abcdefghijklmnopqrstuvwxyz';
    const uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const numbers = '0123456789';
    const special = '!@#$%^&*';
    const allChars = lowercase + uppercase + numbers + special;

    let password = '';
    const randomBytes = crypto.randomBytes(length);

    // Ensure at least one character from each category
    password += lowercase[crypto.randomInt(lowercase.length)];
    password += uppercase[crypto.randomInt(uppercase.length)];
    password += numbers[crypto.randomInt(numbers.length)];
    password += special[crypto.randomInt(special.length)];

    // Fill the rest randomly
    for (let i = 4; i < length; i++) {
        password += allChars[randomBytes[i] % allChars.length];
    }

    // Shuffle the password
    return password.split('').sort(() => Math.random() - 0.5).join('');
}

async function createMySQLUser(username) {
    const mysqlUser = `kp_${username}`;
    const mysqlDb = `kp_${username}_db`;
    const mysqlPassword = generateSecurePassword(16);

    const rootConn = await mysql.createConnection({
        host: process.env.DB_HOST,
        user: process.env.DB_USER,
        password: process.env.DB_PASSWORD
    });

    try {
        console.log(`[MySQL] Creating user: ${mysqlUser}`);

        // Drop user if exists (for re-registration scenarios)
        await rootConn.execute(
            `DROP USER IF EXISTS '${mysqlUser}'@'localhost'`
        ).catch(() => { });

        // Create MySQL user with random password
        // Note: MySQL doesn't support ? placeholder for IDENTIFIED BY, must use direct string
        await rootConn.execute(
            `CREATE USER '${mysqlUser}'@'localhost' IDENTIFIED BY '${mysqlPassword.replace(/'/g, "''")}'`
        );

        console.log(`[MySQL] User created: ${mysqlUser}`);

        // Create database for user
        await rootConn.execute(
            `CREATE DATABASE IF NOT EXISTS \`${mysqlDb}\``
        );

        console.log(`[MySQL] Database created: ${mysqlDb}`);

        // Grant all privileges on their database only
        await rootConn.execute(
            `GRANT ALL PRIVILEGES ON \`${mysqlDb}\`.* TO '${mysqlUser}'@'localhost'`
        );

        // Also grant basic privileges for phpMyAdmin to work
        await rootConn.execute(
            `GRANT SELECT ON mysql.db TO '${mysqlUser}'@'localhost'`
        );

        await rootConn.execute('FLUSH PRIVILEGES');

        console.log(`[MySQL] Privileges granted for ${mysqlUser}`);

        return { mysqlUser, mysqlPassword, mysqlDb };
    } catch (error) {
        console.error('[MySQL] Error creating user:', error);
        throw new Error(`Failed to create MySQL user: ${error.message}`);
    } finally {
        await rootConn.end();
    }
}

async function deleteMySQLUser(username) {
    const mysqlUser = `kp_${username}`;
    const mysqlDb = `kp_${username}_db`;

    const rootConn = await mysql.createConnection({
        host: process.env.DB_HOST,
        user: process.env.DB_USER,
        password: process.env.DB_PASSWORD
    });

    try {
        // Drop database
        await rootConn.execute(`DROP DATABASE IF EXISTS \`${mysqlDb}\``);

        // Drop user
        await rootConn.execute(`DROP USER IF EXISTS '${mysqlUser}'@'localhost'`);

        await rootConn.execute('FLUSH PRIVILEGES');

        console.log(`[MySQL] Deleted user and database for: ${username}`);
    } catch (error) {
        console.error('[MySQL] Error deleting user:', error);
        throw error;
    } finally {
        await rootConn.end();
    }
}

/**
 * Create a database for a user's project
 */
async function createProjectDatabase(mysqlUsername, subdomain) {
    const dbName = `db_${subdomain.toLowerCase().replace(/[^a-z0-9]/g, '')}`;

    const rootConn = await mysql.createConnection({
        host: process.env.DB_HOST,
        user: process.env.DB_USER,
        password: process.env.DB_PASSWORD
    });

    try {
        console.log(`[MySQL] Creating project database: ${dbName} for user: ${mysqlUsername}`);
        await rootConn.execute(`CREATE DATABASE IF NOT EXISTS \`${dbName}\``);
        await rootConn.execute(`GRANT ALL PRIVILEGES ON \`${dbName}\`.* TO '${mysqlUsername}'@'localhost'`);
        await rootConn.execute('FLUSH PRIVILEGES');
        console.log(`[MySQL] Database created and privileges granted: ${dbName}`);
        return dbName;
    } catch (error) {
        console.error('[MySQL] Error creating project database:', error);
        throw new Error(`Failed to create project database: ${error.message}`);
    } finally {
        await rootConn.end();
    }
}

/**
 * Drop a project database
 */
async function dropProjectDatabase(subdomain) {
    const dbName = `db_${subdomain.toLowerCase().replace(/[^a-z0-9]/g, '')}`;

    const rootConn = await mysql.createConnection({
        host: process.env.DB_HOST,
        user: process.env.DB_USER,
        password: process.env.DB_PASSWORD
    });

    try {
        console.log(`[MySQL] Dropping project database: ${dbName}`);
        await rootConn.execute(`DROP DATABASE IF EXISTS \`${dbName}\``);
        console.log(`[MySQL] Database dropped: ${dbName}`);
    } catch (error) {
        console.error('[MySQL] Error dropping project database:', error);
        throw new Error(`Failed to drop project database: ${error.message}`);
    } finally {
        await rootConn.end();
    }
}

module.exports = { createMySQLUser, deleteMySQLUser, createProjectDatabase, dropProjectDatabase };
