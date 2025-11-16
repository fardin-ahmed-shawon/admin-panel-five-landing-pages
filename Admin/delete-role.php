<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
// database connection
include('database/dbConnection.php');

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $role_id = $_GET['id'];

    // Prepare the delete statement
    $sql = "DELETE FROM roles WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $role_id);

    if ($stmt->execute()) {
        // Redirect to view roles page with success message
        header("Location: view-roles.php?message=Role deleted successfully.");
        exit();
    } else {
        // Redirect to view roles page with error message
        header("Location: view-roles.php?error=Failed to delete role.");
        exit();
    }
} else {
    // Redirect to view roles page with error message
    header("Location: view-roles.php?error=Invalid role ID.");
    exit();
}
?>