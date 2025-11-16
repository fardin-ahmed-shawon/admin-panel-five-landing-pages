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
//////////////////////////// Handle the 'get-sliders' action ///////////////////////////
////////////////////////////////////////////////////////////////////////////////////
if ($action == 'get-sliders') {

    // Query the sliders table for all records
    $sql = "SELECT * FROM slider";
    $result = mysqli_query($conn, $sql);

    if (!$result || mysqli_num_rows($result) === 0) {
        echo json_encode([]); // return empty array if no sliders
        exit();
    }

    // Fetch all records
    $sliders = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $sliders[] = [
            "id" => (int)$row['slider_id'],
            "img" => $site_link . 'api/' . $row['slider_img']
        ];
    }

    // Output only the array
    echo json_encode($sliders);
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