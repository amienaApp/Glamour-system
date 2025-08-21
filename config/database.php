<?php
/**
 * Database Configuration
 * Enhanced file-based storage system with MongoDB-like interface
 */

class Database {
    private static $instance = null;
    private $dataPath;
    private $collections = [];

    private function __construct() {
        $this->dataPath = __DIR__ . '/../data/collections/';
        if (!file_exists($this->dataPath)) {
            mkdir($this->dataPath, 0777, true);
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
            $this->collections[$name] = new Collection($name, $this->dataPath);
        }
        return $this->collections[$name];
    }

    public function listCollections() {
        $collections = [];
        if (is_dir($this->dataPath)) {
            $files = scandir($this->dataPath);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'json') {
                    $collections[] = pathinfo($file, PATHINFO_FILENAME);
                }
            }
        }
        return $collections;
    }

    public function dropCollection($name) {
        $filePath = $this->dataPath . $name . '.json';
        if (file_exists($filePath)) {
            unlink($filePath);
            unset($this->collections[$name]);
            return true;
        }
        return false;
    }

    public function getDataPath() {
        return $this->dataPath;
    }
}

class Collection {
    private $name;
    private $filePath;
    private $data = [];
    private $indexes = [];

    public function __construct($name, $dataPath) {
        $this->name = $name;
        $this->filePath = $dataPath . $name . '.json';
        $this->loadData();
    }

    private function loadData() {
        if (file_exists($this->filePath)) {
            $content = file_get_contents($this->filePath);
            $this->data = json_decode($content, true) ?: [];
        } else {
            $this->data = [];
            $this->saveData();
        }
    }

    private function saveData() {
        $backupPath = $this->filePath . '.backup.' . date('Y-m-d-H-i-s');
        if (file_exists($this->filePath)) {
            copy($this->filePath, $backupPath);
        }
        file_put_contents($this->filePath, json_encode($this->data, JSON_PRETTY_PRINT));
    }

    public function find($filter = [], $options = []) {
        $results = $this->data;
        
        // Apply filters
        if (!empty($filter)) {
            $results = $this->applyFilters($results, $filter);
        }

        // Apply sorting
        if (isset($options['sort'])) {
            $results = $this->applySort($results, $options['sort']);
        }

        // Apply pagination
        if (isset($options['skip'])) {
            $results = array_slice($results, $options['skip']);
        }
        if (isset($options['limit'])) {
            $results = array_slice($results, 0, $options['limit']);
        }

        return new Cursor($results);
    }

    public function findOne($filter = []) {
        $results = $this->find($filter, ['limit' => 1]);
        $data = iterator_to_array($results);
        return !empty($data) ? $data[0] : null;
    }

    public function insertOne($document) {
        $document['_id'] = $this->generateId();
        $document['createdAt'] = date('Y-m-d H:i:s');
        $document['updatedAt'] = date('Y-m-d H:i:s');
        
        $this->data[] = $document;
        $this->saveData();
        
        return new InsertOneResult($document['_id']);
    }

    public function updateOne($filter, $update, $options = []) {
        $modifiedCount = 0;
        
        foreach ($this->data as &$document) {
            if ($this->matchesFilter($document, $filter)) {
                if (isset($update['$set'])) {
                    $document = array_merge($document, $update['$set']);
                }
                if (isset($update['$push'])) {
                    foreach ($update['$push'] as $field => $value) {
                        if (!isset($document[$field])) {
                            $document[$field] = [];
                        }
                        $document[$field][] = $value;
                    }
                }
                if (isset($update['$pull'])) {
                    foreach ($update['$pull'] as $field => $value) {
                        if (isset($document[$field]) && is_array($document[$field])) {
                            $document[$field] = array_filter($document[$field], function($item) use ($value) {
                                return $item !== $value;
                            });
                        }
                    }
                }
                $document['updatedAt'] = date('Y-m-d H:i:s');
                $modifiedCount++;
                break; // Only update first match
            }
        }
        
        if ($modifiedCount > 0) {
            $this->saveData();
        }
        
        return new UpdateResult($modifiedCount);
    }

    public function deleteOne($filter) {
        $deletedCount = 0;
        
        foreach ($this->data as $key => $document) {
            if ($this->matchesFilter($document, $filter)) {
                unset($this->data[$key]);
                $deletedCount++;
                break; // Only delete first match
            }
        }
        
        if ($deletedCount > 0) {
            $this->data = array_values($this->data); // Reindex array
            $this->saveData();
        }
        
        return new DeleteResult($deletedCount);
    }

    public function countDocuments($filter = []) {
        $results = $this->find($filter);
        return count(iterator_to_array($results));
    }

    public function distinct($field) {
        $values = [];
        foreach ($this->data as $document) {
            if (isset($document[$field])) {
                $value = $document[$field];
                if (is_array($value)) {
                    foreach ($value as $item) {
                        if (!in_array($item, $values)) {
                            $values[] = $item;
                        }
                    }
                } else {
                    if (!in_array($value, $values)) {
                        $values[] = $value;
                    }
                }
            }
        }
        return $values;
    }

    private function generateId() {
        return uniqid() . substr(md5(microtime()), 0, 8);
    }

    private function applyFilters($data, $filter) {
        return array_filter($data, function($document) use ($filter) {
            return $this->matchesFilter($document, $filter);
        });
    }

    private function matchesFilter($document, $filter) {
        foreach ($filter as $field => $value) {
            if ($field === '$text' && isset($value['$search'])) {
                // Simple text search
                $searchTerm = strtolower($value['$search']);
                $found = false;
                foreach ($document as $docField => $docValue) {
                    if (is_string($docValue) && stripos($docValue, $searchTerm) !== false) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) return false;
            } elseif (is_array($value) && isset($value['$in'])) {
                // $in operator
                if (!isset($document[$field]) || !in_array($document[$field], $value['$in'])) {
                    return false;
                }
            } elseif (is_array($value) && (isset($value['$gte']) || isset($value['$lte']))) {
                // Range operators
                if (!isset($document[$field])) return false;
                $docValue = $document[$field];
                if (isset($value['$gte']) && $docValue < $value['$gte']) return false;
                if (isset($value['$lte']) && $docValue > $value['$lte']) return false;
            } else {
                // Exact match
                if (!isset($document[$field]) || $document[$field] !== $value) {
                    return false;
                }
            }
        }
        return true;
    }

    private function applySort($data, $sort) {
        usort($data, function($a, $b) use ($sort) {
            foreach ($sort as $field => $direction) {
                $aVal = $a[$field] ?? null;
                $bVal = $b[$field] ?? null;
                
                if ($aVal === $bVal) continue;
                
                $result = $aVal <=> $bVal;
                return $direction === -1 ? -$result : $result;
            }
            return 0;
        });
        return $data;
    }
}

class Cursor implements Iterator {
    private $data;
    private $position = 0;

    public function __construct($data) {
        $this->data = array_values($data);
    }

    public function current() {
        return $this->data[$this->position];
    }

    public function key() {
        return $this->position;
    }

    public function next() {
        $this->position++;
    }

    public function rewind() {
        $this->position = 0;
    }

    public function valid() {
        return isset($this->data[$this->position]);
    }
}

class InsertOneResult {
    private $insertedId;

    public function __construct($insertedId) {
        $this->insertedId = $insertedId;
    }

    public function getInsertedId() {
        return $this->insertedId;
    }
}

class UpdateResult {
    private $modifiedCount;

    public function __construct($modifiedCount) {
        $this->modifiedCount = $modifiedCount;
    }

    public function getModifiedCount() {
        return $this->modifiedCount;
    }
}

class DeleteResult {
    private $deletedCount;

    public function __construct($deletedCount) {
        $this->deletedCount = $deletedCount;
    }

    public function getDeletedCount() {
        return $this->deletedCount;
    }
}
?>
