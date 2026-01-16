require('./loadEnv').loadEnv();
const { sendMySQLCredentials } = require('./utils/emailService');

(async () => {
    try {
        console.log('Testing MySQL credentials email...\n');

        // Test data
        const email = 'rahmanfaisal653@gmail.com';
        const username = 'faisal';
        const mysqlUser = 'kp_faisal';
        const mysqlPassword = 'TestPassword123!'; // Dummy password for testing
        const mysqlDb = 'kp_faisal_db';

        console.log('Sending test email to:', email);
        console.log('MySQL User:', mysqlUser);
        console.log('MySQL DB:', mysqlDb);
        console.log('\nSending...');

        await sendMySQLCredentials(email, username, mysqlUser, mysqlPassword, mysqlDb);

        console.log('\n✅ Email sent successfully!');
        console.log('Check your inbox for MySQL credentials email.');

        process.exit(0);
    } catch (err) {
        console.error('\n❌ Error sending email:', err.message);
        console.error('Full error:', err);
        process.exit(1);
    }
})();
