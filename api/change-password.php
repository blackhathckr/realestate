<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../admin/includes/auth.php';

// Ensure admin is logged in
if (!$auth->isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Ensure it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    $data = $_POST;
}

// Validate input
if (!isset($data['currentPassword']) || !isset($data['newPassword']) || !isset($data['confirmPassword'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// Validate new password
if (strlen($data['newPassword']) < 6) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'New password must be at least 6 characters long']);
    exit;
}

// Check if passwords match
if ($data['newPassword'] !== $data['confirmPassword']) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'New passwords do not match']);
    exit;
}

// Change password
$result = $auth->changePassword($data['currentPassword'], $data['newPassword']);
echo json_encode($result);
?> 