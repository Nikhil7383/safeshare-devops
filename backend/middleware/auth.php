<?php
require_once __DIR__ . '/../config/jwt.php';

function authenticate() {
    $headers = getallheaders();

    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(["error" => "Missing token"]);
        exit;
    }

    $token = str_replace("Bearer ", "", $headers['Authorization']);

    return verifyJWT($token);
}