const fs = require('fs');
const path = require('path');

function loadEnv() {
    const envPath = path.join(__dirname, '.env');

    if (!fs.existsSync(envPath)) {
        console.warn('[loadEnv] .env file not found, using default environment variables');
        return;
    }

    const envContent = fs.readFileSync(envPath, 'utf8');
    const lines = envContent.split('\n');

    lines.forEach(line => {
        // Skip empty lines and comments
        line = line.trim();
        if (!line || line.startsWith('#')) return;

        // Parse KEY=VALUE
        const match = line.match(/^([^=]+)=(.*)$/);
        if (match) {
            const key = match[1].trim();
            let value = match[2].trim();

            // Remove quotes if present
            if ((value.startsWith('"') && value.endsWith('"')) ||
                (value.startsWith("'") && value.endsWith("'"))) {
                value = value.slice(1, -1);
            }

            // Set environment variable (override existing)
            process.env[key] = value;
        }
    });

    console.log('[loadEnv] Environment variables loaded from .env');
}

module.exports = { loadEnv };
