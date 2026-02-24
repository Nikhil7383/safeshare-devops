<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Delete logic
if (isset($_GET['delete'])) {
    $userId = intval($_GET['delete']);

    // Delete user files first (optional)
    $stmt = $conn->prepare("SELECT filename FROM files WHERE uploaded_by = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($filename);
    while ($stmt->fetch()) {
        $filePath = "../uploads/" . $filename;
        if (file_exists($filePath)) unlink($filePath);
    }

    $conn->query("DELETE FROM files WHERE uploaded_by = $userId");
    $conn->query("DELETE FROM users WHERE id = $userId");

    header("Location: admin_manage_users.php");
    exit();
}

// Display all users
$sql = "SELECT id, name, email, role FROM users ORDER BY role, name";
$result = $conn->query($sql);

echo "<h2>👥 Registered Users</h2>";
echo "<table border='1'>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Delete</th>
        </tr>";

while ($row = $result->fetch_assoc()) {
    // Prevent admin from deleting self
    if ($row['id'] == $_SESSION['userid']) continue;

    echo "<tr>
            <td>{$row['name']}</td>
            <td>{$row['email']}</td>
            <td>{$row['role']}</td>
            <td><a href='admin_manage_users.php?delete={$row['id']}' onclick='return confirm(\"Delete this user and their files?\")'>❌ Delete</a></td>
        </tr>";
}
echo "</table>";
?>
<br>
<a href="admin_dashboard.php">⬅ Back to Dashboard</a>
