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
//////////////////////////// Handle the 'get-coupons' action ///////////////////////////
////////////////////////////////////////////////////////////////////////////////////
if ($action == 'get-all-coupons') {

    // Fetch all coupons
    $sql = "SELECT * FROM coupon";
    $result = mysqli_query($conn, $sql);

    $coupons = [];

    while ($coupon = mysqli_fetch_assoc($result)) {

        $coupons[] = [
            "success" => true,
            "id" => $coupon['id'],
            "coupon_name" => $coupon['coupon_name'],
            "coupon_code" => $coupon['coupon_code'],
            "coupon_discount" => $coupon['coupon_discount']
        ];
    }

    echo json_encode($coupons);
    exit();
}
////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////// END ///////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////



//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////// Handle the 'get-coupon-discount' action ///////////////////////////
////////////////////////////////////////////////////////////////////////////////////
else if ($action == 'get-coupon-discount') {

    // Check if the 'coupon_code' parameter is set in the URL
    $coupon_code = $_GET['coupon_code'] ?? '';

    if ($coupon_code == '') {

        $response = array(
            "success" => false,
            "message" => "No coupon code specified!"
        );

        echo json_encode($response);
        exit();
    }

    // Fetch all coupons
    $sql = "SELECT * FROM coupon WHERE coupon_code = '$coupon_code'";
    $result = mysqli_query($conn, $sql);

    $coupons = [];

    while ($coupon = mysqli_fetch_assoc($result)) {

        $coupons[] = [
            "success" => true,
            "id" => $coupon['id'],
            "coupon_name" => $coupon['coupon_name'],
            "coupon_discount" => $coupon['coupon_discount']
        ];
    }

    echo json_encode($coupons);
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