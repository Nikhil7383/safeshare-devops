<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
    }
if (!isset($_SESSION['userid']) || !in_array($_SESSION['role'], ['student', 'teacher'])) {
    header("Location: ../index.php");
    exit();
}
?>

<h2>Upload a File</h2>
<form action="../upload/handle_upload.php" method="POST" enctype="multipart/form-data">
    <input type="file" name="file" required>
    <button type="submit">Upload</button>
</form>
