<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// database connection
include('database/dbConnection.php');

$order_no = $_GET['o_n'];

// Delete query
$sql = "DELETE FROM order_info WHERE order_no = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_no);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
} else {
    echo "Error deleting order.";
}

$stmt->close();
$conn->close();
?>