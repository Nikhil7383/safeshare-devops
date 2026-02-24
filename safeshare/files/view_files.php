<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['userid'])) {
    header("Location: ../index.php");
    exit();
}

echo "<h2>Uploaded Files</h2>";

$sql = "SELECT files.filename, files.uploaded_at, users.name AS uploader_name, users.role
        FROM files
        JOIN users ON files.uploaded_by = users.id
        ORDER BY files.uploaded_at DESC";

$result = $conn->query($sql);

echo "<table border='1'>
        <tr>
            <th>Filename</th>
            <th>Uploader</th>
            <th>Role</th>
            <th>Uploaded At</th>
            <th>Download</th>
        </tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['filename']}</td>
            <td>{$row['uploader_name']}</td>
            <td>{$row['role']}</td>
            <td>{$row['uploaded_at']}</td>
            <td><a href='../uploads/{$row['filename']}' download>Download</a></td>
        </tr>";
}

echo "</table>";
?>
