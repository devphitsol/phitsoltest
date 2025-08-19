<?php

namespace App\Controllers;

// Use the Slider model with proper namespace
use App\Models\Slider;

class SliderController
{
    private $sliderModel;

    public function __construct()
    {
        $this->sliderModel = new Slider();
    }

    /**
     * Display slider management page
     */
    public function index()
    {
        $slides = $this->sliderModel->getAll();
        $slideCount = $this->sliderModel->getCount();
        $maxSlides = $this->sliderModel->getMaxSlides();
        
        return [
            'slides' => $slides,
            'slideCount' => $slideCount,
            'maxSlides' => $maxSlides
        ];
    }

    /**
     * Handle create slide form submission
     */
    public function create()
    {
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->validateSlideData($_POST);
            
            if (empty($data['errors'])) {
                // Handle image upload
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $uploadResult = $this->handleImageUpload($_FILES['image']);
                    if ($uploadResult['success']) {
                        $data['data']['image'] = $uploadResult['filename'];
                    } else {
                        $data['errors'][] = $uploadResult['error'];
                    }
                }

                if (empty($data['errors'])) {
                    $slideId = $this->sliderModel->create($data['data']);
                    if ($slideId) {
                        $_SESSION['success'] = 'Slide created successfully!';
                        header('Location: index.php?action=slider');
                        exit;
                    } else {
                        $_SESSION['error'] = 'Failed to create slide.';
                    }
                }
            }
            
            if (!empty($data['errors'])) {
                $_SESSION['error'] = implode(', ', $data['errors']);
            }
        }
        
        return [
            'slideCount' => $this->sliderModel->getCount(),
            'maxSlides' => $this->sliderModel->getMaxSlides()
        ];
    }

    /**
     * Handle edit slide form submission
     */
    public function edit($id)
    {
        $slide = $this->sliderModel->getById($id);
        
        if (!$slide) {
            $_SESSION['error'] = 'Slide not found.';
            header('Location: index.php?action=slider');
            exit;
        }

        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->validateSlideData($_POST);
            
            if (empty($data['errors'])) {
                // Handle image upload if new image is provided
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $uploadResult = $this->handleImageUpload($_FILES['image']);
                    if ($uploadResult['success']) {
                        // Delete old image
                        if (!empty($slide['image'])) {
                            $this->deleteImage($slide['image']);
                        }
                        $data['data']['image'] = $uploadResult['filename'];
                    } else {
                        $data['errors'][] = $uploadResult['error'];
                    }
                }
                
                // Handle image removal if requested
                if (isset($_POST['remove_image']) && $_POST['remove_image'] == '1') {
                    if (!empty($slide['image'])) {
                        $this->deleteImage($slide['image']);
                    }
                    $data['data']['image'] = '';
                }

                if (empty($data['errors'])) {
                    if ($this->sliderModel->update($id, $data['data'])) {
                        $_SESSION['success'] = 'Slide updated successfully!';
                        header('Location: index.php?action=slider');
                        exit;
                    } else {
                        $_SESSION['error'] = 'Failed to update slide.';
                    }
                }
            }
            
            if (!empty($data['errors'])) {
                $_SESSION['error'] = implode(', ', $data['errors']);
            }
        }
        
        return ['slide' => $slide];
    }

    /**
     * Delete slide
     */
    public function delete($id)
    {
        $slide = $this->sliderModel->getById($id);
        
        if (!$slide) {
            $_SESSION['error'] = 'Slide not found.';
            header('Location: index.php?action=slider');
            exit;
        }

        // Delete image file
        if (!empty($slide['image'])) {
            $this->deleteImage($slide['image']);
        }

        if ($this->sliderModel->delete($id)) {
            $_SESSION['success'] = 'Slide deleted successfully!';
        } else {
            $_SESSION['error'] = 'Failed to delete slide.';
        }
        
        header('Location: index.php?action=slider');
        exit;
    }

    /**
     * Toggle slide status
     */
    public function toggleStatus($id)
    {
        if ($this->sliderModel->toggleStatus($id)) {
            $_SESSION['success'] = 'Slide status updated successfully!';
        } else {
            $_SESSION['error'] = 'Failed to update slide status.';
        }
        
        header('Location: index.php?action=slider');
        exit;
    }

    /**
     * Reorder slides
     */
    public function reorder()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $order = $input['order'] ?? [];

        if (empty($order)) {
            echo json_encode(['success' => false, 'error' => 'No order data provided']);
            return;
        }

        // Convert array to expected format
        $slideOrders = [];
        foreach ($order as $index => $slideId) {
            $slideOrders[$slideId] = $index + 1;
        }

        $success = $this->sliderModel->updateOrder($slideOrders);
        echo json_encode(['success' => $success]);
    }

    /**
     * Get all slides for layout
     */
    public function getSlides()
    {
        return $this->sliderModel->getAll();
    }

    /**
     * Get slide count for layout
     */
    public function getSlideCount()
    {
        return $this->sliderModel->getCount();
    }

    /**
     * Get max slides for layout
     */
    public function getMaxSlides()
    {
        return $this->sliderModel->getMaxSlides();
    }

    /**
     * Get slide by ID for layout
     */
    public function getSlideById($id)
    {
        return $this->sliderModel->getById($id);
    }

    /**
     * API: Get slider by ID
     */
    public function getById($id)
    {
        return $this->sliderModel->getById($id);
    }

    /**
     * API: Get active sliders
     */
    public function getActive()
    {
        return $this->sliderModel->getActive();
    }

    /**
     * Validate slide data
     */
    private function validateSlideData($data)
    {
        $errors = [];
        $validatedData = [];

        // Validate title
        if (empty($data['title'])) {
            $errors[] = 'Title is required';
        } else {
            $validatedData['title'] = trim($data['title']);
        }

        // Validate description
        if (empty($data['description'])) {
            $errors[] = 'Description is required';
        } else {
            $validatedData['description'] = trim($data['description']);
        }

        // Optional fields
        $validatedData['button_text'] = trim($data['button_text'] ?? '');
        $validatedData['button_url'] = trim($data['button_url'] ?? '');
        $validatedData['order'] = (int)($data['order'] ?? 1);
        $validatedData['status'] = $data['status'] ?? 'active';

        return [
            'data' => $validatedData,
            'errors' => $errors
        ];
    }

    /**
     * Handle image upload
     */
    private function handleImageUpload($file)
    {
        $uploadDir = __DIR__ . '/../../public/uploads/slider/';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileInfo = pathinfo($file['name']);
        $extension = strtolower($fileInfo['extension']);

        // Validate file type
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($extension, $allowedTypes)) {
            return ['success' => false, 'error' => 'Invalid file type. Please upload JPG, PNG, GIF, or WebP images only.'];
        }

        // Validate file size (5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            return ['success' => false, 'error' => 'File size must be less than 5MB.'];
        }

        $filename = uniqid() . '.' . $extension;
        $uploadPath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return ['success' => true, 'filename' => $filename];
        } else {
            return ['success' => false, 'error' => 'Failed to upload image.'];
        }
    }

    /**
     * Delete image file
     */
    private function deleteImage($filename)
    {
        $imagePath = __DIR__ . '/../../public/uploads/slider/' . $filename;
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
} 