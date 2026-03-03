<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
error_reporting(0);
ini_set('display_errors', 0);

include(__DIR__ . '/../../config/db.php');
require __DIR__ . '/../../config/jwt.php';

// AWS SDK
require __DIR__ . '/../../vendor/autoload.php'; // composer autoload
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

// Configure AWS S3
$s3 = new S3Client([
    'version' => 'latest',
    'region'  => 'us-east-1', // replace with your bucket region
]);

$bucket = 'safeshare-uploads123'; // replace with your S3 bucket name

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

$originalName = basename($_FILES['file']['name']);
$ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

$allowedTypes = ['pdf', 'docx', 'pptx', 'txt', 'jpg', 'jpeg', 'png'];
if (!in_array($ext, $allowedTypes)) {
    echo json_encode(["status" => "error", "error" => "Invalid file type"]);
    exit;
}

// Unique filename to avoid collisions
$filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);

try {
    // Upload to S3
    $result = $s3->putObject([
        'Bucket' => $bucket,
        'Key'    => $filename,
        'SourceFile' => $_FILES['file']['tmp_name'],
        'ACL'    => 'private' // or 'public-read' if you want direct access
    ]);

    // Save file record in RDS
    $stmt = $conn->prepare("INSERT INTO files (filename, uploaded_by) VALUES (?, ?)");
    $stmt->bind_param("si", $filename, $userId);
    $stmt->execute();

    // S3 URL (optional — for frontend use)
    $fileUrl = $result['ObjectURL'];

    echo json_encode(["status" => "success", "file" => $filename, "url" => $fileUrl]);

} catch (AwsException $e) {
    echo json_encode(["status" => "error", "error" => $e->getMessage()]);
}