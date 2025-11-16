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
//////////////////////////// Handle the 'get-web-info' action ///////////////////////////
////////////////////////////////////////////////////////////////////////////////////
if ($action == 'get-web-info') {

    // Query the website_info table for the record with id = 1
    $sql = "SELECT * FROM website_info WHERE id = 1";
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
        "name" => $row['name'],
        "logo" => $site_link . 'Admin/' .$row['logo'],
        "logo_size" => $row['logo_size'],
        "fav" => $row['fav'],
        "address" => $row['address'],
        "inside_location" => $row['inside_location'],
        "inside_delivery_charge" => (int)$row['inside_delivery_charge'],
        "outside_delivery_charge" => (int)$row['outside_delivery_charge'],
        "phone" => $row['phone'],
        "wp_api_num" => $row['wp_api_num'],
        "messenger_username" => $row['messenger_username'],
        "acc_num" => $row['acc_num'],
        "email" => $row['email'],
        "fb_link" => $row['fb_link'],
        "insta_link" => $row['insta_link'],
        "twitter_link" => $row['twitter_link'],
        "yt_link" => $row['yt_link'],
        "location" => $row['location'],
        "vdo_location" => $row['vdo_location'],
        "banner_one" =>  $site_link . 'Admin/' . $row['banner_one'],
        "banner_two" => $site_link . 'Admin/' . $row['banner_two'],
        "shop_banner" => $site_link . 'Admin/' . $row['shop_banner'],
        "about_banner" => $site_link . 'Admin/' . $row['about_banner'],
        "contact_banner" => $site_link . 'Admin/' . $row['contact_banner'],
        "faq_banner" => $site_link . 'Admin/' . $row['faq_banner'],
        "term_banner" => $site_link . 'Admin/' . $row['term_banner'],
        "privacy_banner" => $site_link . 'Admin/' . $row['privacy_banner'],
        "shipping_banner" => $site_link . 'Admin/' . $row['shipping_banner'],
        "top_banner_ad_content" => $row['top_banner_ad_content']
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