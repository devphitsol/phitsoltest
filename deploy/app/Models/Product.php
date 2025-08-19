<?php

namespace App\Models;

use MongoDB\Client;
use MongoDB\Collection;

class Product
{
    private $collection;
    private $db;

    public function __construct()
    {
        $db = \App\Config\Database::getInstance();
        $this->collection = $db->getCollection('products');
        
        // Create indexes for better performance
        $this->createIndexes();
    }
    
    /**
     * Create database indexes for better query performance
     */
    private function createIndexes()
    {
        try {
            // Index for company_id queries
            $this->collection->createIndex(['company_id' => 1]);
            
            // Index for name-based searches
            $this->collection->createIndex(['name' => 'text']);
            
            // Index for status-based queries
            $this->collection->createIndex(['status' => 1]);
            
            // Index for date-based queries
            $this->collection->createIndex(['created_at' => -1]);
            
            // Compound index for company + status queries
            $this->collection->createIndex(['company_id' => 1, 'status' => 1]);
            
        } catch (\Exception $e) {
            // Index creation might fail if indexes already exist, which is fine
            error_log("Product index creation note: " . $e->getMessage());
        }
    }

    public function getAll($page = 1, $limit = 10, $search = '', $companyId = null)
    {
        $filter = [];
        
        if (!empty($search)) {
            $filter = [
                '$or' => [
                    ['name' => ['$regex' => $search, '$options' => 'i']],
                    ['short_description' => ['$regex' => $search, '$options' => 'i']],
                    ['category' => ['$regex' => $search, '$options' => 'i']]
                ]
            ];
        }

        // Add company filter if companyId is provided
        if ($companyId) {
            $filter['company_id'] = $companyId;
            
        }

        

        $skip = ($page - 1) * $limit;
        
        $products = $this->collection->find($filter, [
            'sort' => ['created_at' => -1],
            'skip' => $skip,
            'limit' => $limit
        ])->toArray();

        // Convert ObjectId to string for each product
        foreach ($products as &$product) {
            if (isset($product['_id'])) {
                $product['_id'] = (string) $product['_id'];
            }
        }

        $total = $this->collection->countDocuments($filter);

        

        return [
            'products' => $products,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'totalPages' => ceil($total / $limit)
        ];
    }

    public function getById($id)
    {
        return $this->collection->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
    }

    public function create($data)
    {
        
        
        $productData = [
            'name' => $data['name'],
            'short_description' => $data['short_description'] ?? '',
            'description' => $data['description'] ?? '',
            'category' => $data['category'] ?? '',
            'price' => (float)($data['price'] ?? 0),
            'sku' => $data['sku'] ?? '',
            'stock_quantity' => (int)($data['stock_quantity'] ?? 0),
            'status' => $data['status'] ?? 'active',
            'images' => $data['images'] ?? [],
            'image_url' => $data['image_url'] ?? '', // Keep for backward compatibility
            'main_image' => $data['main_image'] ?? '', // Add main_image field
            'company_id' => $data['company_id'] ?? null, // Add company_id
            'created_at' => new \MongoDB\BSON\UTCDateTime(),
            'updated_at' => new \MongoDB\BSON\UTCDateTime()
        ];

        

        try {
            
            $result = $this->collection->insertOne($productData);
            return $result->getInsertedId();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function update($id, $data)
    {
        $updateData = [
            'name' => $data['name'],
            'short_description' => $data['short_description'] ?? '',
            'description' => $data['description'] ?? '',
            'category' => $data['category'] ?? '',
            'price' => (float)($data['price'] ?? 0),
            'sku' => $data['sku'] ?? '',
            'stock_quantity' => (int)($data['stock_quantity'] ?? 0),
            'status' => $data['status'] ?? 'active',
            'images' => $data['images'] ?? [],
            'image_url' => $data['image_url'] ?? '', // Keep for backward compatibility
            'main_image' => $data['main_image'] ?? '', // Add main_image field
            'updated_at' => new \MongoDB\BSON\UTCDateTime()
        ];

        $result = $this->collection->updateOne(
            ['_id' => new \MongoDB\BSON\ObjectId($id)],
            ['$set' => $updateData]
        );

        return $result->getModifiedCount() > 0;
    }

    public function delete($id)
    {
        $result = $this->collection->deleteOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
        return $result->getDeletedCount() > 0;
    }

    public function toggleStatus($id)
    {
        $product = $this->getById($id);
        if (!$product) {
            return false;
        }

        $newStatus = $product['status'] === 'active' ? 'inactive' : 'active';
        
        $result = $this->collection->updateOne(
            ['_id' => new \MongoDB\BSON\ObjectId($id)],
            ['$set' => [
                'status' => $newStatus,
                'updated_at' => new \MongoDB\BSON\UTCDateTime()
            ]]
        );

        return $result->getModifiedCount() > 0;
    }

    public function getCount($companyId = null)
    {
        $filter = [];
        if ($companyId) {
            $filter['company_id'] = $companyId;
        }
        return $this->collection->countDocuments($filter);
    }

    public function getActiveCount($companyId = null)
    {
        $filter = ['status' => 'active'];
        if ($companyId) {
            $filter['company_id'] = $companyId;
        }
        return $this->collection->countDocuments($filter);
    }

    public function getInactiveCount($companyId = null)
    {
        $filter = ['status' => 'inactive'];
        if ($companyId) {
            $filter['company_id'] = $companyId;
        }
        return $this->collection->countDocuments($filter);
    }

    public function getCategories()
    {
        return $this->collection->distinct('category');
    }

    public function getByCategory($category)
    {
        return $this->collection->find(['category' => $category])->toArray();
    }

    public function search($query)
    {
        $filter = [
            '$or' => [
                ['name' => ['$regex' => $query, '$options' => 'i']],
                ['short_description' => ['$regex' => $query, '$options' => 'i']],
                ['category' => ['$regex' => $query, '$options' => 'i']],
                ['sku' => ['$regex' => $query, '$options' => 'i']]
            ]
        ];

        return $this->collection->find($filter)->toArray();
    }
} 