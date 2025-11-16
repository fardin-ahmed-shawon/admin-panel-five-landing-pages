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
//////////////////////////// Handle the 'get-banners' action ///////////////////////////
////////////////////////////////////////////////////////////////////////////////////
if ($action == 'get-banners') {

    // Query the website_info table for banners
    $sql = "SELECT banner_one, banner_two FROM website_info WHERE id = 1";
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
            "message" => "No banners found"
        ]);
        exit();
    }

    // Prepare response in array format
    $banners = [];

    if (!empty($row['banner_one'])) {
        $banners[] = [
            "id" => 1,
            "img" => $site_link . 'Admin/' . $row['banner_one']
        ];
    }

    if (!empty($row['banner_two'])) {
        $banners[] = [
            "id" => 2,
            "img" => $site_link . 'Admin/' . $row['banner_two']
        ];
    }

    echo json_encode(
        $banners
    );
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