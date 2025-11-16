<?php
session_start();
require_once './config.php';


// Set a custom error handler to return JSON for errors
set_exception_handler(function ($exception) {
    $response = array(
        "success" => false,
        "message" => $exception->getMessage()
    );
    echo json_encode($response);
    exit();
});


// Receive the action type
$action = $_GET['action'] ?? '';

// Check if the 'action' parameter is set in the URL
if ($action == '') {
    $response = array(
        "success" => false,
        "message" => "No action specified!"
    );
    echo json_encode($response);
    exit();
}



//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////// Handle the 'add_order_discount' action ///////////////////////////
////////////////////////////////////////////////////////////////////////////////////
if ($action == 'add_order_discount') {

    $invoice_no = $_POST['invoice_no'] ?? '';
    $total_order_amount = $_POST['total_order_amount'] ?? 0;
    $total_discount_amount = $_POST['total_discount_amount'] ?? 0;
    $free_shipping = $_POST['free_shipping'] ?? 0;

    // Validate required fields
    if (empty($invoice_no) || $total_order_amount === '' || $total_discount_amount === '' || $free_shipping === '') {
        echo json_encode(["success" => false, "message" => "All fields are required!"]);
        exit();
    }

    $sql = "INSERT INTO order_discount_list (
        invoice_no, total_order_amount, total_discount_amount, free_shipping
    ) VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
        exit();
    }

    $stmt->bind_param(
        "sddi",
        $invoice_no,
        $total_order_amount,
        $total_discount_amount,
        $free_shipping
    );

    if (!$stmt->execute()) {
        echo json_encode(["success" => false, "message" => "Execute failed: " . $stmt->error]);
        exit();
    }

    echo json_encode(["success" => true, "message" => "Order Discount Added Successfully!"]);
    exit();
}
////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////// END ///////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////



// Handle wrong/invalid action
else {
    $response = array(
        "success" => false,
        "message" => "Invalid action specified!"
    );
    echo json_encode($response);
    exit();
}

?>