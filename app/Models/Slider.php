<?php

namespace App\Models;

use App\Config\Database;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class Slider
{
    private $collection;
    private const MAX_SLIDES = 10;

    public function __construct()
    {
        $db = Database::getInstance();
        $this->collection = $db->getCollection('sliders');
    }

    /**
     * Get all slides
     */
    public function getAll()
    {
        try {
            $cursor = $this->collection->find(
                [],
                ['sort' => ['order' => 1]]
            );
            return $cursor->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get active slides only
     */
    public function getActive()
    {
        try {
            $cursor = $this->collection->find(
                ['status' => 'active'],
                ['sort' => ['order' => 1]]
            );
            return $cursor->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get slide by ID
     */
    public function getById($id)
    {
        try {
            // Convert string ID to ObjectId if needed
            if (is_string($id)) {
                $id = new ObjectId($id);
            }
            
            $slide = $this->collection->findOne(['_id' => $id]);
            return $slide ? (array) $slide : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Create new slide
     */
    public function create($data)
    {
        try {
            // Check if we've reached the maximum number of slides
            $totalSlides = $this->collection->countDocuments([]);
            if ($totalSlides >= self::MAX_SLIDES) {
                throw new \Exception("Maximum number of slides (" . self::MAX_SLIDES . ") reached");
            }

            // Set default values
            $data['created_at'] = $this->getCurrentDateTime();
            $data['updated_at'] = $this->getCurrentDateTime();
            $data['status'] = $data['status'] ?? 'active';
            $data['order'] = $data['order'] ?? ($totalSlides + 1);

            $result = $this->collection->insertOne($data);
            return $result->getInsertedId();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Update slide
     */
    public function update($id, $data)
    {
        try {
            // Convert string ID to ObjectId if needed
            if (is_string($id)) {
                $id = new ObjectId($id);
            }
            
            $data['updated_at'] = $this->getCurrentDateTime();
            
            $result = $this->collection->updateOne(
                ['_id' => $id],
                ['$set' => $data]
            );
            
            return $result->getModifiedCount() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Delete slide
     */
    public function delete($id)
    {
        try {
            // Convert string ID to ObjectId if needed
            if (is_string($id)) {
                $id = new ObjectId($id);
            }
            
            $result = $this->collection->deleteOne(['_id' => $id]);
            return $result->getDeletedCount() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Update slide order
     */
    public function updateOrder($slideOrders)
    {
        try {
            foreach ($slideOrders as $slideId => $order) {
                // Convert string ID to ObjectId if needed
                if (is_string($slideId)) {
                    $slideId = new ObjectId($slideId);
                }
                
                $this->collection->updateOne(
                    ['_id' => $slideId],
                    [
                        '$set' => [
                            'order' => (int) $order,
                            'updated_at' => $this->getCurrentDateTime()
                        ]
                    ]
                );
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Toggle slide status
     */
    public function toggleStatus($id)
    {
        try {
            // Convert string ID to ObjectId if needed
            if (is_string($id)) {
                $id = new ObjectId($id);
            }
            
            $slide = $this->getById($id);
            if (!$slide) {
                return false;
            }

            $newStatus = $slide['status'] === 'active' ? 'inactive' : 'active';
            
            $result = $this->collection->updateOne(
                ['_id' => $id],
                [
                    '$set' => [
                        'status' => $newStatus,
                        'updated_at' => $this->getCurrentDateTime()
                    ]
                ]
            );
            
            return $result->getModifiedCount() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get slide count
     */
    public function getCount()
    {
        try {
            return $this->collection->countDocuments([]);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get maximum slides allowed
     */
    public function getMaxSlides()
    {
        return self::MAX_SLIDES;
    }

    /**
     * Validate slide data
     */
    public function validate($data)
    {
        $errors = [];

        if (empty($data['title'])) {
            $errors[] = 'Title is required';
        }

        if (empty($data['description'])) {
            $errors[] = 'Description is required';
        }

        // Image is only required for new slides, not for updates
        if (isset($data['require_image']) && $data['require_image'] && empty($data['image'])) {
            $errors[] = 'Image is required';
        }

        return $errors;
    }

    /**
     * Get current date time in appropriate format
     */
    private function getCurrentDateTime()
    {
        $db = Database::getInstance();
        if ($db->isUsingFileStorage()) {
            return date('Y-m-d H:i:s');
        } else {
            // For MongoDB, use UTCDateTime
            if (extension_loaded('mongodb')) {
                return new UTCDateTime();
            } else {
                return date('Y-m-d H:i:s');
            }
        }
    }
} 