<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);

include(__DIR__ . '/../../config/db.php');
require __DIR__ . '/../../config/jwt.php';

$headers = apache_request_headers();
if (!isset($headers['Authorization'])) {
    echo json_encode(["status"=>"error","error"=>"Missing token"]); exit;
}

$token = str_replace("Bearer ","",$headers['Authorization']);
$payload = verify_jwt($token);
if(!$payload) { echo json_encode(["status"=>"error","error"=>"Invalid token"]); exit; }

$userId = $payload['id'] ?? null;
if(!$userId) { echo json_encode(["status"=>"error","error"=>"Invalid user"]); exit; }

if(!isset($_FILES['file'])) { echo json_encode(["status"=>"error","error"=>"No file uploaded"]); exit; }

$uploadDir = __DIR__ . '/../../uploads/';
if(!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

$filename = basename($_FILES['file']['name']);
$targetFile = $uploadDir . $filename;

$allowedTypes = ['pdf','docx','pptx','txt','jpg','png'];
$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
if(!in_array($ext,$allowedTypes)) { echo json_encode(["status"=>"error","error"=>"Invalid file type"]); exit; }

if(move_uploaded_file($_FILES['file']['tmp_name'],$targetFile)){
    $stmt = $conn->prepare("INSERT INTO files (filename, uploaded_by) VALUES (?, ?)");
    $stmt->bind_param("si",$filename,$userId);
    $stmt->execute();
    echo json_encode(["status"=>"success","file"=>$filename,"url"=>"/uploads/".$filename]);
}else{
    echo json_encode(["status"=>"error","error"=>"Upload failed"]);
}