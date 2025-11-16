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
//////////////////////////// Handle the 'get-footer-content' action ///////////////////////////
////////////////////////////////////////////////////////////////////////////////////
if ($action == 'get-footer-content') {

    // Query the footer_info table for the record with id = 1
    $sql = "SELECT * FROM footer_info WHERE id = 1";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        echo json_encode([
            "success" => false,
            "message" => "Database query failed: " . mysqli_error($conn)
        ]);
        exit();
    }

    // Fetch the single record
    $row = mysqli_fetch_assoc($result);

    if (!$row) {
        echo json_encode([
            "success" => false,
            "message" => "No website info found"
        ]);
        exit();
    }

    // Prepare response
    $response = [
        "id" => (int)$row['id'],
        "about_us" => $row['about_us'],
        "contact_us" => $row['contact_us'],
        "faq" => $row['faq'],
        "terms_of_use" => $row['terms_of_use'],
        "privacy_policy" => $row['privacy_policy'],
        "shipping_delivery" => $row['shipping_delivery']
    ];

    echo json_encode([
        "success" => true,
        "data" => $response
    ]);
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