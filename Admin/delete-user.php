<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
// database connection
include('database/dbConnection.php');

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $admin_id = $_GET['id'];

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("DELETE FROM admin_info WHERE admin_id = ?");
    $stmt->bind_param("i", $admin_id);

    if ($stmt->execute()) {
        // Redirect to view-users.php with a success message
        header("Location: view-users.php?message=User deleted successfully.");
        exit();
    } else {
        // Redirect to view-users.php with an error message
        header("Location: view-users.php?message=Error deleting user.");
        exit();
    }

    $stmt->close();
} else {
    // Redirect to view-users.php if the ID is not valid
    header("Location: view-users.php?message=Invalid user ID.");
    exit();
}

require 'footer.php';
?>