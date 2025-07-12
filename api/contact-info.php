<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../admin/config/database.php';
require_once '../admin/includes/contact-functions.php';

try {
    $contactManager = new ContactManager();
    
    // Get query parameters
    $type = isset($_GET['type']) ? $_GET['type'] : null;
    $active_only = isset($_GET['active']) ? (bool)$_GET['active'] : true;
    
    // Build SQL query
    $sql = "SELECT * FROM contact_info WHERE 1=1";
    $params = [];
    
    if ($type) {
        $sql .= " AND type = ?";
        $params[] = $type;
    }
    
    if ($active_only) {
        $sql .= " AND is_active = 1";
    }
    
    $sql .= " ORDER BY type, sort_order";
    
    $stmt = $database->query($sql, $params);
    $contacts = $stmt->fetchAll();
    
    // Group by type for easier frontend consumption
    $result = [];
    foreach ($contacts as $contact) {
        $result[$contact['type']][] = $contact;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $result,
        'count' => count($contacts)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?> 