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
//////////////////////////// Handle the 'get-all-sizes' action ///////////////////////////
////////////////////////////////////////////////////////////////////////////////////
if ($action == 'get-all-sizes') {

    $sql = "SELECT 
                p.product_id,
                p.product_slug,
                p.available_stock,
                ps.id AS size_id,
                ps.size
            FROM product_size_list ps
            INNER JOIN product_info p ON ps.product_id = p.product_id
            ORDER BY p.product_id, ps.id";

    $result = mysqli_query($conn, $sql);

    $response = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $productId = $row['product_id'];

        // If product not added yet, create entry
        if (!isset($response[$productId])) {
            $response[$productId] = [
                "id" => $row['size_id'], // first size id reference
                "product_id" => $row['product_id'],
                "product_slug" => $row['product_slug'],
                "sizes" => []
            ];
        }

        // Add size info
        $response[$productId]["sizes"][] = [
            "id" => $row['size_id'],
            "label" => $row['size'],
            "stock" => (int)$row['available_stock'] // same stock for all sizes unless per-size stock added
        ];
    }

    // Re-index to normal array
    $response = array_values($response);

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