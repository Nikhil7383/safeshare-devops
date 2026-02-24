<?php
session_start();
if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}
?>

<h1>Welcome, <?php echo $_SESSION['name']; ?> (Admin)</h1>

<ul>
    <li><a href="admin_manage_files.php">📂 Manage Uploaded Files</a></li>
    <li><a href="admin_manage_users.php">👥 Manage Users</a></li>
    <li><a href="../files/view_files.php">👁️ View All Files</a></li>
    <li><a href="../logout.php">Logout</a></li>
</ul>
