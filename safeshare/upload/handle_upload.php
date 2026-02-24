<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['userid']) || !in_array($_SESSION['role'], ['student', 'teacher'])) {
    die("Unauthorized.");
}

$uploadDir = "../uploads/";
$filename = basename($_FILES['file']['name']);
$targetFile = $uploadDir . $filename;

// Check file type (optional security)
$allowedTypes = ['pdf', 'docx', 'pptx'];
$fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

if (!in_array($fileType, $allowedTypes)) {
    die("Only PDF, DOCX, PPTX files allowed.");
}

if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
    $stmt = $conn->prepare("INSERT INTO files (filename, uploaded_by) VALUES (?, ?)");
    $stmt->bind_param("si", $filename, $_SESSION['userid']);
    $stmt->execute();
    header("Location: ../dashboards/{$_SESSION['role']}_dashboard.php?success=1");
} else {
    echo "Upload failed.";
}
?>
