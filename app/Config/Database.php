<?php

namespace App\Config;

// Note: Autoloader should be loaded by entry points

class Database
{
    private static $instance = null;
    private $client;
    private $database;
    private $useFileStorage = false;
    private $dataDir;

    private function __construct()
    {
        $this->loadEnvironment();
        $this->dataDir = __DIR__ . '/../../data';
        
        // Check if MongoDB extension is available
        if (extension_loaded('mongodb')) {
            $this->connect();
        } else {
            $this->useFileStorage = true;
            $this->initializeFileStorage();
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function loadEnvironment()
    {
        $envFile = __DIR__ . '/../../.env';
        if (file_exists($envFile)) {
            try {
                $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
                $dotenv->load();
            } catch (\Exception $e) {
                error_log("Error loading .env file: " . $e->getMessage());
                // If .env file doesn't exist or is invalid, use defaults
                $_ENV['MONGODB_URI'] = $_ENV['MONGODB_URI'] ?? 'mongodb://localhost:27017';
                $_ENV['MONGODB_DATABASE'] = $_ENV['MONGODB_DATABASE'] ?? 'phitsol_dashboard';
            }
        } else {
            // Set defaults if .env file doesn't exist
            $_ENV['MONGODB_URI'] = $_ENV['MONGODB_URI'] ?? 'mongodb://localhost:27017';
            $_ENV['MONGODB_DATABASE'] = $_ENV['MONGODB_DATABASE'] ?? 'phitsol_dashboard';
        }
    }

    private function connect()
    {
        try {
            // Double-check if MongoDB extension is available
            if (!extension_loaded('mongodb')) {
                throw new \Exception("MongoDB extension not available");
            }
            
            $uri = $_ENV['MONGODB_URI'] ?? 'mongodb://localhost:27017';
            $databaseName = $_ENV['MONGODB_DATABASE'] ?? 'phitsol_dashboard';

            $this->client = new \MongoDB\Client($uri);
            $this->database = $this->client->selectDatabase($databaseName);

            // Test connection with timeout
            $this->database->command(['ping' => 1], ['maxTimeMS' => 5000]);
            
            error_log("MongoDB connection established successfully");
        } catch (\Exception $e) {
            error_log("MongoDB connection failed: " . $e->getMessage() . ". Falling back to file storage.");
            // Fallback to file storage if MongoDB connection fails
            $this->useFileStorage = true;
            $this->initializeFileStorage();
        }
    }

    private function initializeFileStorage()
    {
        if (!is_dir($this->dataDir)) {
            mkdir($this->dataDir, 0755, true);
        }
        
        // Create collections directory
        $collectionsDir = $this->dataDir . '/collections';
        if (!is_dir($collectionsDir)) {
            mkdir($collectionsDir, 0755, true);
        }
    }

    public function getDatabase()
    {
        if ($this->useFileStorage) {
            return new FileDatabase($this->dataDir);
        }
        return $this->database;
    }

    public function getCollection($collectionName)
    {
        if ($this->useFileStorage) {
            return new FileCollection($this->dataDir, $collectionName);
        }
        return $this->database->selectCollection($collectionName);
    }

    public function isUsingFileStorage()
    {
        return $this->useFileStorage;
    }

    public function close()
    {
        if ($this->client) {
            $this->client = null;
        }
    }
}

// File-based database implementation for fallback
class FileDatabase
{
    private $dataDir;

    public function __construct($dataDir)
    {
        $this->dataDir = $dataDir;
    }

    public function selectCollection($collectionName)
    {
        return new FileCollection($this->dataDir, $collectionName);
    }
}

class FileCollection
{
    private $dataDir;
    private $collectionName;
    private $dataFile;

    public function __construct($dataDir, $collectionName)
    {
        $this->dataDir = $dataDir;
        $this->collectionName = $collectionName;
        $this->dataFile = $dataDir . '/collections/' . $collectionName . '.json';
        $this->initializeFile();
    }

    private function initializeFile()
    {
        if (!file_exists($this->dataFile)) {
            file_put_contents($this->dataFile, json_encode([]));
        }
    }

    public function find($filter = [], $options = [])
    {
        $data = $this->loadData();
        $results = [];

        foreach ($data as $document) {
            if ($this->matchesFilter($document, $filter)) {
                $results[] = $document;
            }
        }

        // Apply sorting
        if (isset($options['sort'])) {
            $this->sortResults($results, $options['sort']);
        }

        return new FileCursor($results);
    }

    public function findOne($filter = [])
    {
        $data = $this->loadData();
        
        foreach ($data as $document) {
            if ($this->matchesFilter($document, $filter)) {
                return $document;
            }
        }
        
        return null;
    }

    public function insertOne($document)
    {
        $data = $this->loadData();
        
        // Generate ID if not provided
        if (!isset($document['_id'])) {
            $document['_id'] = $this->generateId();
        }
        
        $data[] = $document;
        $this->saveData($data);
        
        return new FileInsertResult($document['_id']);
    }

    public function updateOne($filter, $update)
    {
        $data = $this->loadData();
        $modified = 0;
        
        foreach ($data as &$document) {
            if ($this->matchesFilter($document, $filter)) {
                if (isset($update['$set'])) {
                    $document = array_merge($document, $update['$set']);
                }
                $modified = 1;
                break;
            }
        }
        
        if ($modified > 0) {
            $this->saveData($data);
        }
        
        return new FileUpdateResult($modified);
    }

    public function deleteOne($filter)
    {
        $data = $this->loadData();
        $deleted = 0;
        
        foreach ($data as $key => $document) {
            if ($this->matchesFilter($document, $filter)) {
                unset($data[$key]);
                $deleted = 1;
                break;
            }
        }
        
        if ($deleted > 0) {
            $this->saveData(array_values($data));
        }
        
        return new FileDeleteResult($deleted);
    }

    public function countDocuments($filter = [])
    {
        $data = $this->loadData();
        $count = 0;
        
        foreach ($data as $document) {
            if ($this->matchesFilter($document, $filter)) {
                $count++;
            }
        }
        
        return $count;
    }

    private function loadData()
    {
        if (!file_exists($this->dataFile)) {
            return [];
        }
        
        $content = file_get_contents($this->dataFile);
        return json_decode($content, true) ?: [];
    }

    private function saveData($data)
    {
        file_put_contents($this->dataFile, json_encode($data, JSON_PRETTY_PRINT));
    }

    private function matchesFilter($document, $filter)
    {
        foreach ($filter as $key => $value) {
            if ($key === '_id') {
                // Handle ObjectId comparison for both string and ObjectId types
                if (is_string($value)) {
                    if (!isset($document['_id']) || $document['_id'] !== $value) {
                        return false;
                    }
                } elseif (is_object($value) && method_exists($value, '__toString')) {
                    // Handle MongoDB ObjectId objects
                    if (!isset($document['_id']) || $document['_id'] !== (string) $value) {
                        return false;
                    }
                } else {
                    if (!isset($document['_id']) || $document['_id'] !== $value) {
                        return false;
                    }
                }
            } elseif (!isset($document[$key]) || $document[$key] !== $value) {
                return false;
            }
        }
        return true;
    }

    private function sortResults(&$results, $sort)
    {
        foreach ($sort as $field => $direction) {
            usort($results, function($a, $b) use ($field, $direction) {
                $aVal = $a[$field] ?? null;
                $bVal = $b[$field] ?? null;
                
                if ($direction === 1) {
                    return $aVal <=> $bVal;
                } else {
                    return $bVal <=> $aVal;
                }
            });
        }
    }

    private function generateId()
    {
        return uniqid() . '_' . time();
    }
}

// Helper classes for file-based operations
class FileCursor
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function toArray()
    {
        return $this->data;
    }
}

class FileInsertResult
{
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function getInsertedId()
    {
        return $this->id;
    }
}

class FileUpdateResult
{
    private $modifiedCount;

    public function __construct($modifiedCount)
    {
        $this->modifiedCount = $modifiedCount;
    }

    public function getModifiedCount()
    {
        return $this->modifiedCount;
    }
}

class FileDeleteResult
{
    private $deletedCount;

    public function __construct($deletedCount)
    {
        $this->deletedCount = $deletedCount;
    }

    public function getDeletedCount()
    {
        return $this->deletedCount;
    }
} 