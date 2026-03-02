<?php
header("Content-Type: application/json");
include(__DIR__ . '/../../config/db.php');
require __DIR__ . '/../../config/jwt.php';

// Validate token
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;
if (!$authHeader) {
    http_response_code(401);
    echo json_encode(["status" => "error", "error" => "Missing token"]);
    exit;
}
$token = str_replace("Bearer ", "", $authHeader);
$payload = verify_jwt($token);
if (!$payload) {
    http_response_code(401);
    echo json_encode(["status" => "error", "error" => "Invalid token"]);
    exit;
}

// JOIN with users so dashboards get uploader name
$result = $conn->query("
    SELECT f.id, f.filename, f.uploaded_at, u.name, u.id AS user_id
    FROM files f
    JOIN users u ON f.uploaded_by = u.id
    ORDER BY f.id DESC
");

$files = [];
while ($row = $result->fetch_assoc()) {
    $row['url'] = "/uploads/" . $row['filename'];
    $files[] = $row;
}

echo json_encode(["status" => "success", "files" => $files]);