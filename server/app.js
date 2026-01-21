const express = require('express');
const cors = require('cors');
const { AVATAR_ROOT, PAYMENT_PROOF_PATH } = require('./config/paths');
const routes = require('./routes');

const createApp = () => {
    const app = express();

    // 1. Core Middleware
    app.use(cors());
    app.use(express.json({ limit: '50mb' }));
    app.use(express.urlencoded({ extended: true, limit: '50mb' }));

    // 2. Static File Serving
    console.log(`[Static] Serving Avatars from: ${AVATAR_ROOT}`);
    app.use('/avatars', express.static(AVATAR_ROOT));
    
    console.log(`[Static] Serving Payment Proofs from: ${PAYMENT_PROOF_PATH}`);
    // Serve Payment Proofs (Protected path ideally, but public for this MVP)
    app.use('/uploads/proofs', express.static(PAYMENT_PROOF_PATH));

    // 3. Handle favicon.ico to prevent 404 errors
    app.get('/favicon.ico', (req, res) => res.status(204).end());

    // 4. API Routes
    app.use('/api', routes);

    // 5. Global Error Handler (Optional but recommended)
    app.use((err, req, res, next) => {
        console.error('[App Error]', err.stack);
        res.status(500).json({ 
            message: 'Internal Server Error', 
            error: process.env.NODE_ENV === 'development' ? err.message : undefined 
        });
    });

    // 6. 404 Handler
    app.use((req, res) => {
        res.status(404).json({ message: `Route ${req.method} ${req.url} not found` });
    });

    return app;
};

module.exports = createApp;