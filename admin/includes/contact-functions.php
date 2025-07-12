<?php
class ContactManager {
    private $db;
    
    public function __construct() {
        global $database;
        $this->db = $database;
    }
    
    public function getAllContactInfo() {
        try {
            return $this->db->query("SELECT * FROM contact_info ORDER BY type, sort_order")->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    public function getContactInfoByType($type) {
        try {
            return $this->db->query("SELECT * FROM contact_info WHERE type = ? ORDER BY sort_order", [$type])->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    public function getContactInfo($id) {
        try {
            return $this->db->query("SELECT * FROM contact_info WHERE id = ?", [$id])->fetch();
        } catch (Exception $e) {
            return null;
        }
    }
    
    public function createContactInfo($data) {
        try {
            $sql = "INSERT INTO contact_info (type, label, value, is_active, sort_order, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
            
            $params = [
                $data['type'],
                $data['label'],
                $data['value'],
                isset($data['is_active']) ? 1 : 0,
                $data['sort_order'] ?? 0
            ];
            
            $this->db->query($sql, $params);
            return $this->db->lastInsertId();
        } catch (Exception $e) {
            throw new Exception('Failed to create contact info: ' . $e->getMessage());
        }
    }
    
    public function updateContactInfo($id, $data) {
        try {
            $sql = "UPDATE contact_info SET type = ?, label = ?, value = ?, is_active = ?, sort_order = ?, updated_at = CURRENT_TIMESTAMP 
                    WHERE id = ?";
            
            $params = [
                $data['type'],
                $data['label'],
                $data['value'],
                isset($data['is_active']) ? 1 : 0,
                $data['sort_order'] ?? 0,
                $id
            ];
            
            $this->db->query($sql, $params);
            return true;
        } catch (Exception $e) {
            throw new Exception('Failed to update contact info: ' . $e->getMessage());
        }
    }
    
    public function deleteContactInfo($id) {
        try {
            $this->db->query("DELETE FROM contact_info WHERE id = ?", [$id]);
            return true;
        } catch (Exception $e) {
            throw new Exception('Failed to delete contact info: ' . $e->getMessage());
        }
    }
    
    public function validateContactData($data) {
        $errors = [];
        
        if (empty($data['type'])) {
            $errors[] = 'Type is required';
        }
        
        if (empty($data['label'])) {
            $errors[] = 'Label is required';
        }
        
        if (empty($data['value'])) {
            $errors[] = 'Value is required';
        }
        
        // Validate specific types
        if (!empty($data['type'])) {
            switch ($data['type']) {
                case 'email':
                    if (!filter_var($data['value'], FILTER_VALIDATE_EMAIL)) {
                        $errors[] = 'Please enter a valid email address';
                    }
                    break;
                case 'phone':
                    if (!preg_match('/^[0-9+\-\s\(\)]+$/', $data['value'])) {
                        $errors[] = 'Please enter a valid phone number';
                    }
                    break;
                case 'social':
                    if (!empty($data['value']) && $data['value'] !== '#' && !filter_var($data['value'], FILTER_VALIDATE_URL)) {
                        $errors[] = 'Please enter a valid URL for social media links';
                    }
                    break;
            }
        }
        
        return $errors;
    }
    
    public function getActiveContactByType($type) {
        try {
            return $this->db->query("SELECT * FROM contact_info WHERE type = ? AND is_active = 1 ORDER BY sort_order", [$type])->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    public function getContactTypeCounts() {
        try {
            $result = $this->db->query("
                SELECT type, COUNT(*) as count, SUM(is_active) as active_count 
                FROM contact_info 
                GROUP BY type
            ")->fetchAll();
            
            $counts = [];
            foreach ($result as $row) {
                $counts[$row['type']] = [
                    'total' => $row['count'],
                    'active' => $row['active_count']
                ];
            }
            return $counts;
        } catch (Exception $e) {
            return [];
        }
    }
}
?> 