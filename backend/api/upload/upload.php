<?php
header("Content-Type: application/json");
error_reporting(0);
ini_set('display_errors', 0);

include(__DIR__ . '/../../config/db.php');
require __DIR__ . '/../../config/jwt.php';

$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;
if (!$authHeader) {
    echo json_encode(["status" => "error", "error" => "Missing token"]);
    exit;
}

$token = str_replace("Bearer ", "", $authHeader);
$payload = verify_jwt($token);
if (!$payload) {
    echo json_encode(["status" => "error", "error" => "Invalid token"]);
    exit;
}

$userId = $payload['id'] ?? null;
if (!$userId) {
    echo json_encode(["status" => "error", "error" => "Invalid user"]);
    exit;
}

if (!isset($_FILES['file'])) {
    echo json_encode(["status" => "error", "error" => "No file uploaded"]);
    exit;
}

// Use absolute path mapped in docker-compose
$uploadDir = '/var/www/uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

$originalName = basename($_FILES['file']['name']);
$ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

$allowedTypes = ['pdf', 'docx', 'pptx', 'txt', 'jpg', 'jpeg', 'png'];
if (!in_array($ext, $allowedTypes)) {
    echo json_encode(["status" => "error", "error" => "Invalid file type"]);
    exit;
}

// Unique filename to avoid collisions
$filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
$targetFile = $uploadDir . $filename;

if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
    $stmt = $conn->prepare("INSERT INTO files (filename, uploaded_by) VALUES (?, ?)");
    $stmt->bind_param("si", $filename, $userId);
    $stmt->execute();
    echo json_encode(["status" => "success", "file" => $filename, "url" => "/uploads/" . $filename]);
} else {
    echo json_encode(["status" => "error", "error" => "Upload failed. Check directory permissions."]);
}