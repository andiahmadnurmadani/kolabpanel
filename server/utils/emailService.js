const nodemailer = require('nodemailer');

let transporter = null;

function getTransporter() {
    if (!transporter) {
        transporter = nodemailer.createTransport({
            service: 'gmail',
            auth: {
                user: process.env.SMTP_USER,
                pass: process.env.SMTP_PASSWORD
            }
        });
    }
    return transporter;
}

async function sendVerificationEmail(email, username, token) {
    const verificationUrl = `${process.env.APP_URL}/verify-email?token=${token}`;

    const mailOptions = {
        from: `"KolabPanel" <${process.env.SMTP_USER}>`,
        to: email,
        subject: 'Verify Your KolabPanel Account',
        html: `
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; border-radius: 10px 10px 0 0; text-align: center;">
                    <h1 style="color: white; margin: 0; font-size: 28px;">KolabPanel</h1>
                </div>
                
                <div style="background: white; padding: 30px; border: 1px solid #e2e8f0; border-top: none; border-radius: 0 0 10px 10px;">
                    <h2 style="color: #4F46E5; margin-top: 0;">Welcome to KolabPanel!</h2>
                    <p style="color: #334155; font-size: 16px;">Hi <strong>${username}</strong>,</p>
                    <p style="color: #334155; font-size: 16px;">Thank you for registering with KolabPanel. Please verify your email address by clicking the button below:</p>
                    
                    <div style="text-align: center; margin: 30px 0;">
                        <a href="${verificationUrl}" 
                           style="background-color: #4F46E5; color: white; padding: 14px 40px; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: bold; font-size: 16px;">
                            Verify Email Address
                        </a>
                    </div>
                    
                    <p style="color: #64748b; font-size: 14px; margin-top: 30px;">
                        Or copy and paste this link into your browser:<br>
                        <a href="${verificationUrl}" style="color: #4F46E5; word-break: break-all;">${verificationUrl}</a>
                    </p>
                    
                    <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin-top: 20px; border-radius: 4px;">
                        <p style="margin: 0; color: #92400e; font-size: 14px;">
                            ⏰ <strong>Important:</strong> This verification link will expire in 24 hours.
                        </p>
                    </div>
                    
                    <p style="color: #94a3b8; font-size: 12px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
                        If you didn't create this account, please ignore this email.
                    </p>
                </div>
            </div>
        `
    };

    await getTransporter().sendMail(mailOptions);
}

async function sendMySQLCredentials(email, username, mysqlUser, mysqlPassword, mysqlDb) {
    const mailOptions = {
        from: `"KolabPanel" <${process.env.SMTP_USER}>`,
        to: email,
        subject: '🎉 Your phpMyAdmin Credentials - KolabPanel',
        html: `
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
                <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 30px; border-radius: 10px 10px 0 0; text-align: center;">
                    <h1 style="color: white; margin: 0; font-size: 28px;">🎉 Account Activated!</h1>
                </div>
                
                <div style="background: white; padding: 30px; border: 1px solid #e2e8f0; border-top: none; border-radius: 0 0 10px 10px;">
                    <p style="color: #334155; font-size: 16px;">Hi <strong>${username}</strong>,</p>
                    <p style="color: #334155; font-size: 16px;">Congratulations! Your email has been verified and your account is now active.</p>
                    
                    <div style="background: #f8fafc; border: 2px solid #4F46E5; border-radius: 8px; padding: 20px; margin: 25px 0;">
                        <h3 style="color: #4F46E5; margin-top: 0; margin-bottom: 15px;">📊 Your phpMyAdmin Credentials</h3>
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="padding: 8px 0; color: #64748b; font-weight: bold;">MySQL Username:</td>
                                <td style="padding: 8px 0; color: #1e293b; font-family: monospace;">${mysqlUser}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px 0; color: #64748b; font-weight: bold;">MySQL Password:</td>
                                <td style="padding: 8px 0;">
                                    <code style="background: white; padding: 6px 10px; border-radius: 4px; border: 1px solid #cbd5e1; color: #dc2626; font-weight: bold; display: inline-block;">${mysqlPassword}</code>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 8px 0; color: #64748b; font-weight: bold;">Database Name:</td>
                                <td style="padding: 8px 0; color: #1e293b; font-family: monospace;">${mysqlDb}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px 0; color: #64748b; font-weight: bold;">phpMyAdmin URL:</td>
                                <td style="padding: 8px 0;">
                                    <a href="${process.env.PHPMYADMIN_URL}" style="color: #4F46E5; text-decoration: none;">${process.env.PHPMYADMIN_URL}</a>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <div style="background: #fee2e2; border-left: 4px solid #dc2626; padding: 15px; margin: 20px 0; border-radius: 4px;">
                        <p style="margin: 0; color: #991b1b; font-size: 14px;">
                            <strong>⚠️ Security Notice:</strong> This is the ONLY time we will send your MySQL password. Please save these credentials in a secure location immediately.
                        </p>
                    </div>
                    
                    <div style="text-align: center; margin: 30px 0;">
                        <a href="${process.env.APP_URL}" 
                           style="background-color: #4F46E5; color: white; padding: 14px 40px; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: bold; font-size: 16px;">
                            Login to KolabPanel
                        </a>
                    </div>
                    
                    <p style="color: #334155; font-size: 14px; margin-top: 25px;">
                        You can now start deploying your sites and managing your databases through phpMyAdmin!
                    </p>
                    
                    <p style="color: #94a3b8; font-size: 12px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
                        Need help? Contact our support team or check our documentation.
                    </p>
                </div>
            </div>
        `
    };

    await getTransporter().sendMail(mailOptions);
}

module.exports = { sendVerificationEmail, sendMySQLCredentials };
