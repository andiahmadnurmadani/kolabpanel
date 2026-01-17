const express = require('express');
const router = express.Router();
const db = require('../db');
const { createProjectDatabase, dropProjectDatabase } = require('../utils/mysqlUserManager');

/**
 * PUT /api/sites/:id
 * Update site - handles database creation/deletion
 */
router.put('/:id', async (req, res) => {
    const { id } = req.params;
    const { hasDatabase } = req.body;

    try {
        // Get site info
        const [sites] = await db.execute('SELECT * FROM sites WHERE id = ?', [id]);
        if (sites.length === 0) {
            return res.status(404).json({ message: 'Site not found' });
        }

        const site = sites[0];

        // If creating database
        if (hasDatabase === true && !site.has_database) {
            // Get user's MySQL credentials
            const [users] = await db.execute(
                'SELECT mysql_username FROM users WHERE id = ?',
                [site.user_id]
            );

            if (users.length === 0 || !users[0].mysql_username) {
                return res.status(400).json({
                    message: 'User MySQL account not found. Please verify your email first.'
                });
            }

            const mysqlUsername = users[0].mysql_username;

            // Create real MySQL database
            const dbName = await createProjectDatabase(mysqlUsername, site.subdomain);
            console.log(`[Sites API] Created database ${dbName} for site ${site.name}`);
        }

        // If dropping database
        if (hasDatabase === false && site.has_database) {
            await dropProjectDatabase(site.subdomain);
            console.log(`[Sites API] Dropped database for site ${site.name}`);
        }

        // Update site record
        await db.execute(
            'UPDATE sites SET has_database = ? WHERE id = ?',
            [hasDatabase, id]
        );

        // Return updated site
        const [updated] = await db.execute('SELECT * FROM sites WHERE id = ?', [id]);
        res.json(updated[0]);

    } catch (err) {
        console.error('[Sites API] Error updating site:', err);
        res.status(500).json({ message: err.message });
    }
});

module.exports = router;
