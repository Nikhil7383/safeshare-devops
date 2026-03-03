<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

error_reporting(0);
ini_set('display_errors', 0);

include(__DIR__ . '/../../config/db.php');
require __DIR__ . '/../../config/jwt.php';

$data = json_decode(file_get_contents("php://input"), true);

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if(!$email || !$password){
    echo json_encode(["status"=>"error","error"=>"Missing credentials"]);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
$stmt->bind_param("s",$email);
$stmt->execute();

$res = $stmt->get_result();
$user = $res->fetch_assoc();

if($user && password_verify($password,$user['password'])){
    $token = generate_jwt([
        "id"=>$user['id'],
        "role"=>$user['role']
    ]);

    echo json_encode([
        "status"=>"success",
        "token"=>$token,
        "user"=>[
            "id"=>$user['id'],
            "name"=>$user['name'],
            "role"=>$user['role']
        ]
    ]);
}else{
    echo json_encode(["status"=>"error","error"=>"Invalid credentials"]);
}