<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['delete'])) {
    $fileId = intval($_GET['delete']);

    // Get filename
    $stmt = $conn->prepare("SELECT filename FROM files WHERE id = ?");
    $stmt->bind_param("i", $fileId);
    $stmt->execute();
    $stmt->bind_result($filename);
    
    if ($stmt->fetch()) {
        $filePath = "../uploads/" . $filename;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    $stmt->close(); // ✅ FREE the first statement before next query

    // Delete from database
    $deleteStmt = $conn->prepare("DELETE FROM files WHERE id = ?");
    $deleteStmt->bind_param("i", $fileId);
    $deleteStmt->execute();
    $deleteStmt->close(); // ✅ optional but good practice

    header("Location: admin_manage_files.php");
    exit();
}

// Display file list
$sql = "SELECT files.id, files.filename, files.uploaded_at, users.name AS uploader_name, users.role
        FROM files
        JOIN users ON files.uploaded_by = users.id
        ORDER BY files.uploaded_at DESC";

$result = $conn->query($sql);

echo "<h2>📄 All Uploaded Files</h2>";
echo "<table border='1'>
        <tr>
            <th>Filename</th>
            <th>Uploader</th>
            <th>Role</th>
            <th>Uploaded At</th>
            <th>Download</th>
            <th>Delete</th>
        </tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['filename']}</td>
            <td>{$row['uploader_name']}</td>
            <td>{$row['role']}</td>
            <td>{$row['uploaded_at']}</td>
            <td><a href='../uploads/{$row['filename']}' download>Download</a></td>
            <td><a href='admin_manage_files.php?delete={$row['id']}' onclick='return confirm(\"Delete this file?\")'>❌ Delete</a></td>
        </tr>";
}
echo "</table>";
?>
<br>
<a href="admin_dashboard.php">⬅ Back to Dashboard</a>
