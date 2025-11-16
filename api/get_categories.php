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
//////////////////////// Handle the 'get-all-categories' action ///////////////////////
////////////////////////////////////////////////////////////////////////////////////
if ($action == 'get-all-categories') {

    // Fetch all main categories
    $sql = "SELECT * FROM main_category";
    $result = mysqli_query($conn, $sql);

    $categories = [];

    while ($main = mysqli_fetch_assoc($result)) {

        // Fetch subcategories for each main category
        $sub_sql = "SELECT * FROM sub_category WHERE main_ctg_name = '".mysqli_real_escape_string($conn, $main['main_ctg_name'])."'";
        $sub_result = mysqli_query($conn, $sub_sql);

        $sub_categories = [];
        while ($sub = mysqli_fetch_assoc($sub_result)) {
            $sub_categories[] = [
                "id" => $sub['sub_ctg_id'],
                "title" => $sub['sub_ctg_name'],
                "slug" => $sub['sub_ctg_slug'],
                "link" => "/sub-category/" . $sub['sub_ctg_slug']
            ];
        }

        $categories[] = [
            "id" => $main['main_ctg_id'],
            "img" => $site_link . "img/" . $main['main_ctg_img'], // use uploaded image
            "title" => $main['main_ctg_name'],
            "description" => $main['main_ctg_des'],
            "slug" => $main['main_ctg_slug'],
            "link" => "/category/" . $main['main_ctg_slug'],
            "sub_category" => $sub_categories
        ];
    }

    echo json_encode($categories);
    exit();
}
//////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////// END /////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////



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