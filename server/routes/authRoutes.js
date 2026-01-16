const bcrypt = require('bcrypt');
const crypto = require('crypto');
const { sendVerificationEmail, sendMySQLCredentials } = require('../utils/emailService');
const { createMySQLUser } = require('../utils/mysqlUserManager');

module.exports = function (app, db, SECRET_KEY) {

    // Register endpoint
    app.post('/api/auth/register', async (req, res) => {
        const { username, email, password } = req.body;

        // Validation
        if (!username || !email || !password) {
            return res.status(400).json({ message: 'All fields are required' });
        }

        if (password.length < 6) {
            return res.status(400).json({ message: 'Password must be at least 6 characters' });
        }

        // Email format validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            return res.status(400).json({ message: 'Invalid email format' });
        }

        try {
            // Check if user exists
            const [existing] = await db.execute(
                'SELECT id FROM users WHERE username = ? OR email = ?',
                [username, email]
            );

            if (existing.length > 0) {
                return res.status(409).json({ message: 'Username or email already exists' });
            }

            // Hash password
            const hashedPassword = await bcrypt.hash(password, 10);

            // Generate verification token
            const verificationToken = crypto.randomBytes(32).toString('hex');
            const verificationExpires = new Date(Date.now() + 24 * 60 * 60 * 1000); // 24 hours

            // Create user (NOT verified yet, status PENDING)
            const userId = `u_${Date.now()}`;
            await db.execute(
                `INSERT INTO users (id, username, password, email, role, plan, avatar, status, email_verified, verification_token, verification_expires) 
                 VALUES (?, ?, ?, ?, 'USER', 'Basic', ?, 'PENDING', FALSE, ?, ?)`,
                [
                    userId,
                    username,
                    hashedPassword,
                    email,
                    `https://ui-avatars.com/api/?name=${encodeURIComponent(username)}&background=random`,
                    verificationToken,
                    verificationExpires
                ]
            );

            console.log(`[Register] User created: ${username}, sending verification email...`);

            // Send verification email
            await sendVerificationEmail(email, username, verificationToken);

            console.log(`[Register] Verification email sent to: ${email}`);

            res.status(201).json({
                message: 'Registration successful! Please check your email to verify your account.',
                email: email
            });
        } catch (err) {
            console.error('[Register] Error:', err);
            res.status(500).json({ message: 'Registration failed: ' + err.message });
        }
    });

    // Email verification endpoint
    app.get('/api/auth/verify-email', async (req, res) => {
        const { token } = req.query;

        if (!token) {
            return res.status(400).json({ message: 'Verification token required' });
        }

        try {
            // Find user by token
            const [users] = await db.execute(
                'SELECT * FROM users WHERE verification_token = ? AND verification_expires > NOW()',
                [token]
            );

            if (users.length === 0) {
                return res.status(400).json({ message: 'Invalid or expired verification token' });
            }

            const user = users[0];

            console.log(`[Verify] Creating MySQL user for: ${user.username}`);

            // Create MySQL user and database
            const { mysqlUser, mysqlPassword, mysqlDb } = await createMySQLUser(user.username);

            console.log(`[Verify] MySQL user created: ${mysqlUser}`);

            // Update user: verify email, activate account, save MySQL credentials
            // Store MySQL password as plain text (protected by app login) so it can be displayed in dashboard
            await db.execute(
                `UPDATE users 
                 SET email_verified = TRUE, 
                     status = 'ACTIVE', 
                     verification_token = NULL, 
                     verification_expires = NULL,
                     mysql_username = ?,
                     mysql_password = ?,
                     mysql_database = ?
                 WHERE id = ?`,
                [mysqlUser, mysqlPassword, mysqlDb, user.id]
            );

            console.log(`[Verify] User activated: ${user.username}`);

            // Send MySQL credentials via email
            await sendMySQLCredentials(user.email, user.username, mysqlUser, mysqlPassword, mysqlDb);

            console.log(`[Verify] MySQL credentials sent to: ${user.email}`);

            res.json({
                success: true,
                message: 'Email verified successfully! Your MySQL credentials have been sent to your email.',
                mysqlCredentials: {
                    username: mysqlUser,
                    password: mysqlPassword,
                    database: mysqlDb
                }
            });
        } catch (err) {
            console.error('[Verify] Error:', err);
            res.status(500).json({ message: 'Verification failed: ' + err.message });
        }
    });
};
