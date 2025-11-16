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
//////////////////////////// Handle the 'order-tracking' action ///////////////////////////
////////////////////////////////////////////////////////////////////////////////////
if ($action == 'order-tracking') {
    // Get invoice_no from POST
    $invoice_no = $_POST['invoice_no'] ?? '';

    if (empty($invoice_no)) {
        echo json_encode([
            "success" => false,
            "message" => "Invoice number is required!"
        ]);
        exit();
    }

    // Sanitize
    $invoice_no = $conn->real_escape_string($invoice_no);

    // Query
    $sql = "SELECT order_no, invoice_no, order_date, user_address, product_title, product_quantity, total_price, payment_method, order_status 
            FROM order_info 
            WHERE invoice_no = '$invoice_no'
            ORDER BY order_date DESC";

    $result = $conn->query($sql);

    if (!$result) {
        echo json_encode([
            "success" => false,
            "message" => "Database query failed: " . $conn->error
        ]);
        exit();
    }

    if ($result->num_rows > 0) {
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = [
                "order_no"       => $row['order_no'],
                "invoice_no"     => $row['invoice_no'],
                "order_date"     => date("F j, Y", strtotime($row['order_date'])),
                "user_address"   => $row['user_address'],
                "product_title"  => $row['product_title'],
                "product_quantity"=> $row['product_quantity'],
                "total_price"    => $row['total_price'],
                "payment_method" => $row['payment_method'],
                "order_status"   => $row['order_status'],
            ];
        }

        echo json_encode([
            "success" => true,
            "data" => $orders
        ]);
        exit();
    } else {
        echo json_encode([
            "success" => false,
            "message" => "No orders found for this invoice number."
        ]);
        exit();
    }
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