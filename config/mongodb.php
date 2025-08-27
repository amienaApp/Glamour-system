<?php
require_once __DIR__ . '/../vendor/autoload.php';

class MongoDB {
    private static $instance = null;
    private $client;
    private $database;

    private function __construct() {
        // MongoDB connection settings
        $uri = 'mongodb://localhost:27017';
        $databaseName = 'glamour_system';
        
        try {
            $this->client = new MongoDB\Client($uri);
            $this->database = $this->client->selectDatabase($databaseName);
        } catch (Exception $e) {
            // Fallback to default settings if connection fails
            error_log("MongoDB connection failed: " . $e->getMessage());
            $this->client = new MongoDB\Client('mongodb://127.0.0.1:27017');
            $this->database = $this->client->selectDatabase('glamour_system');
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getDatabase() {
        return $this->database;
    }

    public function getCollection($collectionName) {
        return $this->database->selectCollection($collectionName);
    }

    public function getClient() {
        return $this->client;
    }
}
