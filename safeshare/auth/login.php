<?php
session_start();
include('../config/db.php');

$email = $_POST['email'];
$password = $_POST['password'];

$result = $conn->query("SELECT * FROM users WHERE email='$email'");
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['userid'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['name'] = $user['name'];

    if ($user['role'] == 'student') {
        header("Location: ../dashboards/student_dashboard.php");
    } elseif ($user['role'] == 'teacher') {
        header("Location: ../dashboards/teacher_dashboard.php");
    } else {
        header("Location: ../dashboards/admin_dashboard.php");
    }
} else {
    echo "Invalid login.";
}
?>
