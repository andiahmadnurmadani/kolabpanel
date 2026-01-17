# Database Synchronization Fix

## Goal
Ensure that MySQL databases created via KolabPanel are correctly visible and manageable in phpMyAdmin for the respective users.

## Problem
The user reports that databases created in the web interface are not appearing in phpMyAdmin. This suggests a permission issue where the `GRANT ALL PRIVILEGES` command might not be effectively applying, or the user is logging in with credentials that don't match the grants.

## Proposed Changes

### 1. Diagnostic & Repair Script
Create a script `server/repair_db_permissions.js` that:
- Iterates through all users who have `mysql_username`.
- Finds all sites belonging to those users that have `hasDatabase = true`.
- Re-runs the `GRANT ALL PRIVILEGES` command for each database using the correct `mysql_username`.
- Outputs the current grants for verification.

### 2. Backend Logic Verification
- Review `server/utils/mysqlUserManager.js` to ensure the `GRANT` statement covers the correct host (`localhost` vs `%`). currently it uses `localhost`.
- Ensure `FLUSH PRIVILEGES` is called effectively.

## Verification Plan

### Automated Verification
- Run `server/repair_db_permissions.js` and check for success output.
- The script will dump `SHOW GRANTS` which serves as proof of correct configuration.

### Manual Verification
1.  **Create Database**: Login to KolabPanel, go to Database, create a new DB for a project.
2.  **Backend Check**: Run `node server/check_db_state.js` to confirm the DB exists and the user has grants.
3.  **User Confirmation**: Ask the user to check phpMyAdmin again.
