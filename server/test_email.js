require('./loadEnv').loadEnv();
const nodemailer = require('nodemailer');

(async () => {
    console.log('Testing SMTP connection...');
    console.log('SMTP_USER:', process.env.SMTP_USER);
    console.log('SMTP_PASSWORD:', process.env.SMTP_PASSWORD ? '***' + process.env.SMTP_PASSWORD.slice(-4) : 'NOT SET');

    const transporter = nodemailer.createTransport({
        service: 'gmail',
        auth: {
            user: process.env.SMTP_USER,
            pass: process.env.SMTP_PASSWORD
        }
    });

    try {
        console.log('\nVerifying SMTP connection...');
        await transporter.verify();
        console.log('✅ SMTP connection successful!');

        console.log('\nSending test email...');
        const info = await transporter.sendMail({
            from: `"KolabPanel Test" <${process.env.SMTP_USER}>`,
            to: process.env.SMTP_USER, // Send to self for testing
            subject: 'Test Email from KolabPanel',
            text: 'If you receive this, SMTP is working!',
            html: '<b>If you receive this, SMTP is working!</b>'
        });

        console.log('✅ Email sent successfully!');
        console.log('Message ID:', info.messageId);
        console.log('Response:', info.response);

    } catch (error) {
        console.error('❌ SMTP Error:', error.message);
        console.error('Full error:', error);
    }

    process.exit(0);
})();
