<?php
session_start();
if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../index.php");
    exit();
}
?>

<h1>Welcome, <?php echo $_SESSION['name']; ?> (Teacher)</h1>

<?php include('../upload/upload_form.php'); ?>
<br>
<a href="../files/view_files.php">📂 View All Files</a>
<br><br>
<a href="../logout.php">Logout</a>
