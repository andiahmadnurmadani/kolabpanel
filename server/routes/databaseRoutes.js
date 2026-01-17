const express = require('express');
const router = express.Router();
const databaseController = require('../controllers/databaseController');

// All routes here are prefixed with /api/database (from index.js)
// and have authenticateToken middleware applied (from index.js)

// List all tables
router.get('/:siteId/tables', databaseController.listTables);

// Get table structure
router.get('/:siteId/tables/:tableName/structure', databaseController.getTableStructure);

// Get table data
router.get('/:siteId/tables/:tableName/data', databaseController.getTableData);

// Execute query
router.post('/:siteId/query', databaseController.executeQuery);

// Import/Export
router.get('/:siteId/export', databaseController.exportDatabase);
router.post('/:siteId/import', databaseController.importDatabase);

module.exports = router;
