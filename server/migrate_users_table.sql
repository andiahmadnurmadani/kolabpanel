-- Update existing users table to add new columns
-- Run each ALTER TABLE separately, ignore errors if column exists

ALTER TABLE users ADD COLUMN email_verified BOOLEAN DEFAULT FALSE;
ALTER TABLE users ADD COLUMN verification_token VARCHAR(255);
ALTER TABLE users ADD COLUMN verification_expires DATETIME;
ALTER TABLE users ADD COLUMN mysql_username VARCHAR(255);
ALTER TABLE users ADD COLUMN mysql_password VARCHAR(255);
ALTER TABLE users ADD COLUMN mysql_database VARCHAR(255);

-- Update demo users to be verified
UPDATE users SET email_verified = TRUE WHERE username IN ('demo_user', 'sys_admin');
