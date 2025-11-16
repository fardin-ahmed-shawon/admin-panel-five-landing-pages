<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Database connection
include('database/dbConnection.php');

// Get the invoice number from the query string
if (!isset($_GET['invoice_no']) || empty($_GET['invoice_no'])) {
    die("Invalid request.");
}
$invoice_no = $_GET['invoice_no'];

// Update query to set order_visibility to 'Hide' in order_info table
$sql_order = "UPDATE order_info SET order_visibility = 'Hide' WHERE invoice_no = ?";
$stmt_order = $conn->prepare($sql_order);
$stmt_order->bind_param("s", $invoice_no);
$stmt_order->execute();

// Update query to set payment_visibility to 'Hide' in payment_info table
$sql_payment = "UPDATE payment_info SET order_visibility = 'Hide' WHERE invoice_no = ?";
$stmt_payment = $conn->prepare($sql_payment);
$stmt_payment->bind_param("s", $invoice_no);
$stmt_payment->execute();

if ($stmt_order->affected_rows > 0 || $stmt_payment->affected_rows > 0) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
} else {
    echo "Error: Order not found or already hidden.";
}

// Close statements and connection
$stmt_order->close();
$stmt_payment->close();
$conn->close();
?>