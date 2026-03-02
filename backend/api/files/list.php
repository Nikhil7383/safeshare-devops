<?php
header("Content-Type: application/json");
include(__DIR__ . '/../../config/db.php');
require __DIR__ . '/../../config/jwt.php';

// Optional token validation
$headers = apache_request_headers();
$token = $headers['Authorization'] ?? null;
$userId = null;
if ($token) {
    $token = str_replace("Bearer ", "", $token);
    $payload = verify_jwt($token);
    if ($payload) $userId = $payload['id'];
}

// Fetch all files
$result = $conn->query("SELECT id, filename FROM files ORDER BY id DESC");
$files = [];
while ($row = $result->fetch_assoc()) {
    $row['url'] = "/uploads/" . $row['filename'];
    $files[] = $row;
}
echo json_encode(["status"=>"success","files"=>$files]);