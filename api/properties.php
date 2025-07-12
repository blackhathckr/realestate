<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../admin/config/database.php';
require_once '../admin/includes/property-functions.php';

try {
    $propertyManager = new PropertyManager();
    
    // Get query parameters
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : null;
    $status = isset($_GET['status']) ? $_GET['status'] : null;
    $category = isset($_GET['category']) ? $_GET['category'] : null;
    
    // Build SQL query
    $sql = "SELECT * FROM properties WHERE 1=1";
    $params = [];
    
    if ($status) {
        $sql .= " AND status = ?";
        $params[] = $status;
    }
    
    if ($category) {
        $sql .= " AND category = ?";
        $params[] = $category;
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT ?";
        $params[] = $limit;
    }
    
    $stmt = $database->query($sql, $params);
    $properties = $stmt->fetchAll();
    
    // Process properties data
    $result = [];
    foreach ($properties as $property) {
        $property['image_url'] = $property['image'] ? './properties/' . $property['image'] : null;
        $result[] = $property;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $result,
        'count' => count($result)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?> 