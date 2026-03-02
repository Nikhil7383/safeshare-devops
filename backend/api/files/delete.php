<?php
require_once '../../config/db.php';
require_once '../../middleware/auth.php';

$user = authenticate(); // validates JWT

if ($user['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["error" => "Admin only"]);
    exit;
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(["error" => "File ID required"]);
    exit;
}

$fileId = intval($_GET['id']);

/* Get filename */
$stmt = $conn->prepare("SELECT filename FROM files WHERE id = ?");
$stmt->bind_param("i", $fileId);
$stmt->execute();
$stmt->bind_result($filename);

if (!$stmt->fetch()) {
    http_response_code(404);
    echo json_encode(["error" => "File not found"]);
    exit;
}
$stmt->close();

$filePath = "../../uploads/" . $filename;
if (file_exists($filePath)) {
    unlink($filePath);
}

/* Delete DB record */
$delete = $conn->prepare("DELETE FROM files WHERE id = ?");
$delete->bind_param("i", $fileId);
$delete->execute();

echo json_encode([
    "status" => "success",
    "deleted_file" => $filename
]);