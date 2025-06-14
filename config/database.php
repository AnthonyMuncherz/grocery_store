<?php
/**
 * Database Configuration
 * SQLite3 configuration for the Grocery Store
 * xampp/htdocs/grocery_store/database/grocery_store.db
 */

// Suppress error display for production (but still log errors)
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

define('DB_PATH', __DIR__ . '/../database/grocery_store.db');
define('DB_BACKUP_PATH', __DIR__ . '/../database/backups/');

/**
 * Get database connection
 * @return SQLite3
 */
function getDbConnection() {
    try {
        $db = new SQLite3(DB_PATH);
        $db->enableExceptions(true);
        return $db;
    } catch (Exception $e) {
        error_log("Database connection error: " . $e->getMessage());
        throw new Exception("Database connection failed. Please try again later.");
    }
}

// Set pragmas for better performance and security
function initializeDatabase() {
    $db = getDbConnection();
    $db->exec('PRAGMA foreign_keys = ON');
    $db->exec('PRAGMA journal_mode = WAL');
    $db->exec('PRAGMA synchronous = NORMAL');
    return $db;
} 