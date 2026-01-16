const fetch = require('node-fetch');

(async () => {
    try {
        console.log('Testing login API...\n');

        const response = await fetch('http://localhost:5000/api/auth/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                username: 'demo_user',
                password: 'password'
            })
        });

        console.log('Status:', response.status);
        console.log('Status Text:', response.statusText);

        const data = await response.json();
        console.log('\nResponse:', JSON.stringify(data, null, 2));

    } catch (err) {
        console.error('Error:', err.message);
    }
})();
