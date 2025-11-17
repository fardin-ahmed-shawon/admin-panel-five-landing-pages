<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'Steadfast Entry'; // Set the page title
?>
<?php require 'header.php'; ?>

<?php
// Get invoice no
$invoice_no = $_GET['invoice_no'];


// Fetch API info from database
$sql = "SELECT * FROM steadfast_info";
$result = mysqli_query($conn, $sql);
$row = mysqli_num_rows($result);
if ($row > 0) {
    $data = mysqli_fetch_assoc($result);

    $api_url = $data['api_url'];
    $api_key = $data['api_key'];
    $secret_key = $data['secret_key'];

} else {

    $api_url = '';
    $api_key = '';
    $secret_key = '';

}
// END Fetch API info

// Fetch order details according to the invoice no
$sql = "SELECT * FROM order_info WHERE invoice_no = '$invoice_no'";
$result = mysqli_query($conn, $sql);
$row = mysqli_num_rows($result);

$total_order_amount = 0;

if ($row > 0) {
    while($data = mysqli_fetch_assoc($result)) {
        $total_order_amount += $data['total_price'];
        $payment_method = $data['payment_method'];

        $user_full_name = $data['user_full_name'];
        $user_phone = $data['user_phone'];
        $user_address = $data['user_address'];
    }

    // Add shipping charge with total order amount
    $total_order_amount += find_shipping_charge($invoice_no);
    // Add Discount amount deduction
    $total_order_amount -= calculate_discount_amount($invoice_no);
}

if ($payment_method != 'Cash On Delivery') {
    $total_order_amount = 0;
}

// Set Order Data
$invoice = $invoice_no;
$recipient_name = $user_full_name;
$recipient_phone = $user_phone; 
$recipient_address = $user_address;

// If you receive the order amount then set -> cod_amount = 0
$cod_amount = $total_order_amount;

// Send Order data to steadfast
$data = [
    "invoice" => $invoice,
    "recipient_name" => $recipient_name,
    "recipient_phone" => $recipient_phone,
    "recipient_address" => $recipient_address,
    "cod_amount" => $cod_amount,
    "note" => "Please Deliver Fast",
    "item_description" => "Product",
    "delivery_type" => 0
];

// Steadfast Entry
$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Api-Key: ' . $api_key,
    'Secret-Key: ' . $secret_key,
    'Content-Type: application/json'
]);

curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($response === false) {
    echo 'Curl error: ' . curl_error($ch);
} else {
    $result = json_decode($response, true);
    if ($httpcode === 200 && isset($result['consignment'])) {

        // Update tracking_code in your database table
        $tracking_code = $result['consignment']['tracking_code'];

        $sql = "INSERT INTO parcel_info (invoice_no, tracking_code)
        VALUES ('$invoice_no', '$tracking_code')";

        $result = mysqli_query($conn, $sql);


    } else {
        echo "Error:\n";
        print_r($result);
    }
}
curl_close($ch);
// End
?>

<!--------------------------->
<!-- START MAIN AREA -->
<!--------------------------->
<div class="content-wrapper">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="card shadow-lg border border-green-200 rounded-xl p-6 bg-white text-center">
                        <div class="card-body">
                            <div class="flex justify-center mb-4">
                                <i style="font-size: 70px" class="mdi mdi-check-circle-outline text-success text-6xl"></i>
                            </div>
                            <h1 class="text-2xl font-semibold mb-2 px-3">
                                Successfully Consignment Created To Steadfast
                            </h1><br>
                            <h2 class="text-lg text-gray-700 flex items-center justify-center gap-2 mb-4">
                                <i class="mdi mdi-truck-fast-outline text-gray-600 text-xl"></i>
                                Tracking Code: <span class="font-mono text-info"><?= $tracking_code; ?></span>
                            </h2>
                            <a href="https://steadfast.com.bd/t/<?= $tracking_code; ?>" class="btn btn-primary p-3 w-50 my-5">
                                <i class="mdi mdi-map-marker-path text-xl"></i>
                                Track Your Parcel
                            </a>

                            <a href="courier.php" class="btn btn-dark p-3 w-50 mb-5"> 
                                <span class="mdi mdi-keyboard-backspace"></span> Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->

<?php require 'footer.php'; ?>