<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include('database/dbConnection.php');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate the ID
    if (isset($data['id']) && is_numeric($data['id'])) {
        $id = intval($data['id']);

        // Prepare the SQL statement to delete the discount
        $stmt = $conn->prepare("DELETE FROM discount WHERE id = ?");
        $stmt->bind_param("i", $id);

        // Execute the query
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Discount deleted successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete discount.']);
        }

        // Close the statement
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid discount ID.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>