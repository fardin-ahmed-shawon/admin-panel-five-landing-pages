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
//////////////////////////// Handle the 'get-discount' action ///////////////////////////
////////////////////////////////////////////////////////////////////////////////////
if ($action == 'get-discount') {
    $purchase_amount = (int)($_GET['purchase_amount'] ?? 0);

    // Fetch all discount tiers ordered by purchase_amount ASC
    $sql = "SELECT purchase_amount, discount_amount, free_shipping 
            FROM discount 
            ORDER BY purchase_amount ASC";
    $result = mysqli_query($conn, $sql);

    $discounts = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $discounts[] = $row;
    }

    $response = [
        "discount_amount" => 0,
        "free_shipping"   => 0,
        "extra_amount"    => 0
    ];

    if (!empty($discounts)) {
        $bestDiscount = null;
        $nextDiscount = null;

        foreach ($discounts as $idx => $d) {
            if ($purchase_amount >= $d['purchase_amount']) {
                // Candidate for best discount
                $bestDiscount = $d;
            } else {
                // First discount higher than purchase_amount = next step
                $nextDiscount = $d;
                break;
            }
        }

        if ($bestDiscount) {
            $response['discount_amount'] = (int)$bestDiscount['discount_amount'];
            $response['free_shipping']   = (int)$bestDiscount['free_shipping'];
        }

        if ($nextDiscount) {
            $response['extra_amount'] = (int)$nextDiscount['purchase_amount'] - $purchase_amount;
        } else {
            // Already at the highest discount tier
            $response['extra_amount'] = 0;
        }
    }

    echo json_encode($response);
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