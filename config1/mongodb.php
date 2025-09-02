<?php
/**
 * MongoDB Configuration
 * Real MongoDB database connection and operations
 */

class MongoDB {
    private static $instance = null;
    private $client;
    private $database;
    private $collections = [];

    private function __construct() {
        try {
            // Ensure vendor autoload is included
            if (!class_exists('MongoDB\Client')) {
                require_once __DIR__ . '/../vendor/autoload.php';
            }
            


            // MongoDB connection string - update with your actual MongoDB connection
            $connectionString = 'mongodb+srv://fmoha187_db_user:amina1144@cluster0.dnw6lj0.mongodb.net/glamour_system';

            // MongoDB connection string - connecting to friend's computer
            $connectionString = 'mongodb+srv://fmoha187_db_user:amina1144@cluster0.dnw6lj0.mongodb.net/glamour_system';

            $databaseName = 'glamour_system';
            
            // Create MongoDB client
            $this->client = new \MongoDB\Client($connectionString);
            $this->database = $this->client->selectDatabase($databaseName);
            
        } catch (Exception $e) {
            throw new Exception("MongoDB connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getCollection($name) {
        if (!isset($this->collections[$name])) {
            $this->collections[$name] = $this->database->selectCollection($name);
        }
        return $this->collections[$name];
    }

    public function getDatabase() {
        return $this->database;
    }

    public function getClient() {
        return $this->client;
    }

    public function listCollections() {
        return $this->database->listCollections();
    }

    public function dropCollection($name) {
        return $this->database->dropCollection($name);
    }

    public function createCollection($name, $options = []) {
        return $this->database->createCollection($name, $options);
    }

    public function getDatabaseName() {
        return $this->database->getDatabaseName();
    }



    // Database health check
    public function isConnected() {
        try {
            $this->database->command(['ping' => 1]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // Get database statistics
    public function getStats() {
        try {
            $stats = $this->database->command(['dbStats' => 1])->toArray()[0];
            return $stats;
        } catch (Exception $e) {
            return null;
        }
    }
}

// Alias for backward compatibility
class Database extends MongoDB {
    // This allows existing code to continue working
}
?>
