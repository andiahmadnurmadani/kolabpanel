const axios = require('axios');

async function testImportRoute() {
    try {
        // Just checking if route exists (404 vs 400/401/500)
        // We expect 401 Unauthorized or 400 Bad Request if token missing/invalid body
        // If it's 404, then we failed.
        const res = await axios.post('http://localhost:5000/api/database/test_site/import', {}, {
            validateStatus: () => true
        });

        console.log(`Route Status: ${res.status}`);
        if (res.status === 404) {
            console.error("FAIL: Route still returning 404 Not Found");
        } else {
            console.log("SUCCESS: Route found (Status " + res.status + ")");
        }

    } catch (e) {
        console.error("Connection failed:", e.message);
    }
}

testImportRoute();
