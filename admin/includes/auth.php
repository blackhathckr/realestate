<?php
session_start();
require_once __DIR__ . '/../config/database.php';

class Auth {
    private $db;
    
    public function __construct() {
        global $database;
        $this->db = $database;
    }
    
    public function login($username, $password) {
        try {
            $stmt = $this->db->query("SELECT * FROM admin_users WHERE username = ?", [$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function logout() {
        session_unset();
        session_destroy();
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }
    
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit();
        }
    }
    
    public function getAdminUsername() {
        return isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : '';
    }

    public function changePassword($currentPassword, $newPassword) {
        try {
            // Get current user's data
            $stmt = $this->db->query(
                "SELECT * FROM admin_users WHERE id = ?", 
                [$_SESSION['admin_id']]
            );
            $user = $stmt->fetch();
            
            // Verify current password
            if (!$user || !password_verify($currentPassword, $user['password'])) {
                return ['success' => false, 'message' => 'Current password is incorrect'];
            }
            
            // Hash and update new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $this->db->query(
                "UPDATE admin_users SET password = ? WHERE id = ?",
                [$hashedPassword, $_SESSION['admin_id']]
            );
            
            return ['success' => true, 'message' => 'Password updated successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'An error occurred while updating password'];
        }
    }
}

$auth = new Auth();
?> 