<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include('database/dbConnection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_id'])) {
    $category_id = intval($_POST['category_id']);
    
    $stmt = $conn->prepare("DELETE FROM expense_category WHERE category_id = ?");
    $stmt->bind_param("i", $category_id);

    if ($stmt->execute()) {
        header("Location: add-expense-category.php?msg=deleted");
    } else {
        echo "Error deleting category: " . $stmt->error;
    }
    $stmt->close();
} else {
    header("Location: add-expense-category.php");
}
?>
