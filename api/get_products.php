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
//////////////////////// Handle the 'get-all-products' action ///////////////////////
////////////////////////////////////////////////////////////////////////////////////
if ($action == 'get-all-products') {

    $sql = "SELECT 
            p.*, 
            mc.main_ctg_name, 
            mc.main_ctg_slug, 
            sc.sub_ctg_name, 
            sc.sub_ctg_slug 
        FROM 
            product_info p
        JOIN 
            main_category mc ON p.main_ctg_id = mc.main_ctg_id
        JOIN 
            sub_category sc ON p.sub_ctg_id = sc.sub_ctg_id
    ";

    $result = mysqli_query($conn, $sql);

    $response = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $response[] = array(
            "id" => $row['product_id'],

            "img" => $site_link.'img/' . $row['product_img1'],

            // If the image is null, set it to empty string
            "img2" => $row['product_img2'] ? $site_link . 'img/' . $row['product_img2'] : "",
            "img3" => $row['product_img3'] ? $site_link . 'img/' . $row['product_img3'] : "",
            "img4" => $row['product_img4'] ? $site_link . 'img/' . $row['product_img4'] : "",

            "title" => $row['product_title'],

            "category" => $row['main_ctg_name'],

            "category_slug" => $row['main_ctg_slug'],

            "sub_category" => $row['sub_ctg_name'],

            "sub_category_slug" => $row['sub_ctg_slug'],

            "purchase_price" => $row['product_purchase_price'],

            "regular_price" => $row['product_regular_price'],

            "selling_price" => $row['product_price'],

            "avilable_stock" => $row['available_stock'],

            "product_code" => $row['product_code'],

            "product_keyword" => $row['product_keyword'],

            "short_description" => $row['product_short_description'],

            "long_description" => $row['product_description'],

            "product_type" => $row['product_type'],

            "product_slug" => $row['product_slug'],

            "link" => '/product/'.$row['product_slug'],

            "created_at" => $row['created_at']

        );
    }

    echo json_encode($response);
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