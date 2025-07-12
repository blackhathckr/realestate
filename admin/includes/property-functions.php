<?php
class PropertyManager {
    private $db;
    private $upload_dir;
    
    public function __construct() {
        global $database;
        $this->db = $database;
        $this->upload_dir = __DIR__ . '/../../properties/';
        
        // Create upload directory if it doesn't exist
        if (!is_dir($this->upload_dir)) {
            mkdir($this->upload_dir, 0755, true);
        }
    }
    
    public function getAllProperties() {
        try {
            return $this->db->query("SELECT * FROM properties ORDER BY created_at DESC")->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    public function getProperty($id) {
        try {
            return $this->db->query("SELECT * FROM properties WHERE id = ?", [$id])->fetch();
        } catch (Exception $e) {
            return null;
        }
    }
    
    public function createProperty($data, $image_file = null) {
        try {
            $image_path = null;
            
            // Handle image upload
            if ($image_file && $image_file['error'] === UPLOAD_ERR_OK) {
                $image_path = $this->uploadImage($image_file);
                if (!$image_path) {
                    throw new Exception('Failed to upload image');
                }
            }
            
            $sql = "INSERT INTO properties (title, category, image, cost, location, status, description, bedrooms, bathrooms, area, features, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
            
            $params = [
                $data['title'],
                $data['category'],
                $image_path,
                $data['cost'],
                $data['location'],
                $data['status'],
                $data['description'] ?? '',
                $data['bedrooms'] ?? null,
                $data['bathrooms'] ?? null,
                $data['area'] ?? '',
                $data['features'] ?? ''
            ];
            
            $this->db->query($sql, $params);
            return $this->db->lastInsertId();
        } catch (Exception $e) {
            throw new Exception('Failed to create property: ' . $e->getMessage());
        }
    }
    
    public function updateProperty($id, $data, $image_file = null) {
        try {
            $current_property = $this->getProperty($id);
            if (!$current_property) {
                throw new Exception('Property not found');
            }
            
            $image_path = $current_property['image'];
            
            // Handle image upload
            if ($image_file && $image_file['error'] === UPLOAD_ERR_OK) {
                // Delete old image if it exists
                if ($image_path && file_exists($this->upload_dir . $image_path)) {
                    unlink($this->upload_dir . $image_path);
                }
                
                $image_path = $this->uploadImage($image_file);
                if (!$image_path) {
                    throw new Exception('Failed to upload new image');
                }
            }
            
            $sql = "UPDATE properties SET title = ?, category = ?, image = ?, cost = ?, location = ?, status = ?, 
                    description = ?, bedrooms = ?, bathrooms = ?, area = ?, features = ?, updated_at = CURRENT_TIMESTAMP 
                    WHERE id = ?";
            
            $params = [
                $data['title'],
                $data['category'],
                $image_path,
                $data['cost'],
                $data['location'],
                $data['status'],
                $data['description'] ?? '',
                $data['bedrooms'] ?? null,
                $data['bathrooms'] ?? null,
                $data['area'] ?? '',
                $data['features'] ?? '',
                $id
            ];
            
            $this->db->query($sql, $params);
            return true;
        } catch (Exception $e) {
            throw new Exception('Failed to update property: ' . $e->getMessage());
        }
    }
    
    public function deleteProperty($id) {
        try {
            $property = $this->getProperty($id);
            if (!$property) {
                throw new Exception('Property not found');
            }
            
            // Delete image file if it exists
            if ($property['image'] && file_exists($this->upload_dir . $property['image'])) {
                unlink($this->upload_dir . $property['image']);
            }
            
            $this->db->query("DELETE FROM properties WHERE id = ?", [$id]);
            return true;
        } catch (Exception $e) {
            throw new Exception('Failed to delete property: ' . $e->getMessage());
        }
    }
    
    private function uploadImage($file) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        // Validate file type
        if (!in_array($file['type'], $allowed_types)) {
            throw new Exception('Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed.');
        }
        
        // Validate file size
        if ($file['size'] > $max_size) {
            throw new Exception('File size too large. Maximum 5MB allowed.');
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('property_') . '.' . $extension;
        $filepath = $this->upload_dir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return $filename;
        } else {
            throw new Exception('Failed to move uploaded file.');
        }
    }
    
    public function getImageUrl($filename) {
        if (!$filename) return null;
        return '../properties/' . $filename;
    }
    
    public function validatePropertyData($data) {
        $errors = [];
        
        if (empty($data['title'])) {
            $errors[] = 'Title is required';
        }
        
        if (empty($data['category'])) {
            $errors[] = 'Category is required';
        }
        
        if (empty($data['cost'])) {
            $errors[] = 'Cost is required';
        }
        
        if (empty($data['location'])) {
            $errors[] = 'Location is required';
        }
        
        if (empty($data['status'])) {
            $errors[] = 'Status is required';
        }
        
        return $errors;
    }
}
?> 