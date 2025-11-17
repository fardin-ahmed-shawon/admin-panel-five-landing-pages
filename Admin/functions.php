<?php 
//*******************************************/
/**** Here's all the necessary function ****/
//****************************************/


// **************** Product Information ****************** //
function get_product_info($product_id = '', $style = '') {
    global $conn;
    
    $sql = "SELECT p.*, m.main_ctg_name, s.sub_ctg_name
        FROM product_info p
        JOIN main_category m ON p.main_ctg_id = m.main_ctg_id
        JOIN sub_category s ON p.sub_ctg_id = s.sub_ctg_id
        WHERE p.product_id = $product_id
    ";

    $result = $conn->query($sql);

    $output = '';

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $product_html = $style;

            $product_html = str_replace('#ID#', $row['product_id'], $product_html);
            $product_html = str_replace('#NAME#', $row['product_title'], $product_html);
            $product_html = str_replace('#PURCHASE_PRICE#', $row['product_purchase_price'], $product_html);
            $product_html = str_replace('#REGULAR_PRICE#', $row['product_regular_price'], $product_html);
            $product_html = str_replace('#SELLING_PRICE#', $row['product_price'], $product_html);
            $product_html = str_replace('#MAIN_CATEGORY#', $row['main_ctg_name'], $product_html);
            $product_html = str_replace('#SUB_CATEGORY#', $row['sub_ctg_name'], $product_html);
            $product_html = str_replace('#STOCK#', $row['available_stock'], $product_html);
            $product_html = str_replace('#KEYWORD#', $row['product_keyword'], $product_html);
            $product_html = str_replace('#CODE#', $row['product_code'], $product_html);
            $product_html = str_replace('#SHORT_DESC#', $row['product_short_description'], $product_html);
            $product_html = str_replace('#LONG_DESC#', $row['product_description'], $product_html);
            $product_html = str_replace('#SLUG#', $row['product_slug'], $product_html);

            $output .= $product_html;
        }
    }
    
    return $output;
}


// **************** SMS API ****************** //
function send_sms_to_customer($phone = '', $sms = '') {
    // Define the API URL and parameters
    $url = "http://sms.bdwebs.com/api/v2/SendSMS";
    $params = [
        'SenderId' => '8809617621950',
        'Is_Unicode' => 'false',
        'Is_Flash' => 'false',
        'DataCoding' => '0',
        'Message' => $sms,
        'MobileNumbers' => $phone,
        'ApiKey' => 'dVceTVVSpaRX8V2oenWfV5XTmVYD6x6T+HZcfEfrHTo=',
        'ClientId' => 'b34fe4d4-a8f8-4555-842d-97e91c5b838f'
    ];

    // Build the query string
    $queryString = http_build_query($params);

    // Initialize cURL session
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url . '?' . $queryString);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute the request
    $response = curl_exec($ch);

    // Check for errors
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return "cURL Error: $error_msg";
    }

    // Close the cURL session
    curl_close($ch);

    // Return the response
    //return $response;
}


// ******* Steadfast parcel status ********* //
function track_parcel($tracking_code) {
    global $conn;

    // Fetch API info from database
        $sql = "SELECT * FROM steadfast_info";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_num_rows($result);
        if ($row > 0) {
            $data = mysqli_fetch_assoc($result);

            $api_key = $data['api_key'];
            $secret_key = $data['secret_key'];

        } else {

            $api_key = '';
            $secret_key = '';

        }
        // END Fetch API info


        $url = "https://portal.packzy.com/api/v1/status_by_trackingcode/$tracking_code";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Api-Key: ' . $api_key,
            'Secret-Key: ' . $secret_key
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        return $data['delivery_status'] ?? 'Something wrong!';
}


// ******* Steadfast parcel tracking code & status ********* //
function get_parcel_tracking_code_and_status($invoice_no, $conn) {
    // Fetch courier tracking_code & get parcel status
    $sql2 = "SELECT tracking_code FROM parcel_info WHERE invoice_no   = '$invoice_no'";
    $result2 = $conn->query($sql2);
    $row2 = $result2->num_rows;

    if ($row2 > 0) {
        $data = $result2->fetch_assoc();

        $is_tracking_code_set = 1;
        
        $tracking_code = $data['tracking_code'];
        $parcel_status = track_parcel($tracking_code);

    } else {

        $is_tracking_code_set = 0;
        $parcel_status = 'Not Added';
                            
    }
}


// ******* Title to Slug Conversion ********* //
function make_title_to_slug($title) {
    // Convert to lowercase
    $slug = strtolower($title);
    // Remove special characters
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
    // Replace whitespace and underscores with hyphens
    $slug = preg_replace('/[\s_]+/', '-', $slug);
    // Remove multiple hyphens
    $slug = preg_replace('/-+/', '-', $slug);
    // Trim hyphens from ends
    $slug = trim($slug, '-');
    return $slug;
}


// ******* Calculate Order Amount ********* //
function calculate_order_amount($invoice_no = '') {
    global $conn;

    if ($invoice_no != '') {
        $sql = "SELECT SUM(total_price) AS total_amount FROM order_info WHERE invoice_no = '$invoice_no'";
    } else {
        $sql = "SELECT SUM(total_price) AS total_amount FROM order_info";
    }

    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    return $row['total_amount'];
}


// ******* Count Order List ********* //
function count_orders($status = '') {
    global $conn; 

    if ($status == 'Pending') {

        $sql = "SELECT COUNT(order_no) AS total_orders FROM order_info WHERE order_visibility='Show' AND order_status='Pending'";

    } else if ($status == 'Active') {

        $sql = "SELECT COUNT(order_no) AS total_orders FROM order_info WHERE order_visibility='Show' AND order_status !='Pending'";

    } else if ($status == 'Completed') {

        $sql = "SELECT COUNT(order_no) AS total_orders FROM order_info WHERE order_visibility='Show' AND order_status ='Completed'";

    } else if ($status == 'Cancelled') {

        $sql = "SELECT COUNT(order_no) AS total_orders FROM order_info WHERE order_visibility='Show' AND order_status ='Canceled'";

    } else if ($status == 'All') {

        $sql = "SELECT COUNT(order_no) AS total_orders FROM order_info WHERE order_visibility='Show'";

    } else {

        $sql = "SELECT COUNT(order_no) AS total_orders FROM order_info WHERE order_visibility='Show'";
    }
    

    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    return $row['total'];
}


// ******* Get Order List ********* //
function get_order_list($style = '', $status= '', $limit = '') {
    global $conn;

    if ($status == 'Pending') {

        $sql = "SELECT * FROM order_info WHERE order_visibility='Show' AND order_status='Pending' ORDER BY order_no DESC";

    } else if ($status == 'Active') {

        $sql = "SELECT * FROM order_info WHERE order_visibility='Show' AND order_status !='Pending' ORDER BY order_no DESC";

    } else if ($status == 'Completed') {

        $sql = "SELECT * FROM order_info WHERE order_visibility='Show' AND order_status ='Completed' ORDER BY order_no DESC";

    } else if ($status == 'Cancelled') {

        $sql = "SELECT * FROM order_info WHERE order_visibility='Show' AND order_status ='Canceled' ORDER BY order_no DESC";

    } else if ($status == 'All') {

        $sql = "SELECT * FROM order_info WHERE order_visibility='Show' ORDER BY order_no DESC";

    } else {

        $sql = "SELECT * FROM order_info WHERE order_visibility='Show' ORDER BY order_no DESC";

    }

    if ($limit != '') {
        $sql .= " LIMIT $limit";
    }

    $result = $conn->query($sql);

    $output = '';

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $order_html = $style;

            $order_html = str_replace('#ORDER_NO#', $row['order_no'], $order_html);
            $order_html = str_replace('#INVOICE_NO#', $row['invoice_no'], $order_html);
            $order_html = str_replace('#USER_ID#', $row['user_id'], $order_html);

            $order_html = str_replace('#NAME#', $row['user_full_name'], $order_html);
            $order_html = str_replace('#PHONE#', $row['user_phone'], $order_html);
            $order_html = str_replace('#EMAIL#', $row['user_email'], $order_html);
            $order_html = str_replace('#ADDRESS#', $row['user_address'], $order_html);
            $order_html = str_replace('#CITY#', $row['city_address'], $order_html);
            

            $order_html = str_replace('#QTY#', $row['product_quantity'], $order_html);
            $order_html = str_replace('#TOTAL_PRICE#', $row['total_price'], $order_html);
            $order_html = str_replace('#PAYMENT_METHOD#', $row['payment_method'], $order_html);


            $order_html = str_replace('#PRODUCT#', $row['product_title'], $order_html);
            $order_html = str_replace('#SIZE#', $row['product_size'], $order_html);


            $order_html = str_replace('#ORDER_DATE#', $row['order_date'], $order_html);
            $order_html = str_replace('#STATUS#', $row['order_status'], $order_html);
            $order_html = str_replace('#VISIBILITY#', $row['order_visibility'], $order_html);


            $output .= $order_html;
        }
    }

    return $output;
}

// ******* Find Customer Phone ********* //
function find_customer_phone($invoice_no = '') {
    global $conn;

    if ($invoice_no != '') {
        $sql = "SELECT user_phone FROM order_info WHERE invoice_no = '$invoice_no' LIMIT 1";
    } else {
        $sql = "SELECT user_phone FROM order_info";
    }

    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    return $row['user_phone'];
}

// ******* Find Order Date ********* //
function find_order_date($invoice_no = '') {
    global $conn;

    if ($invoice_no != '') {
        $sql = "SELECT DATE(order_date) AS order_date 
                FROM order_info 
                WHERE invoice_no = '$invoice_no' 
                LIMIT 1";
    } else {
        $sql = "SELECT DATE(order_date) AS order_date FROM order_info";
    }

    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    return $row['order_date'];
}

// ******* Calculate Discount Amount ********* //
function calculate_discount_amount($invoice_no = '') {
    global $conn;

    if ($invoice_no != '') {
        $sql = "SELECT COALESCE(SUM(total_discount_amount), 0) AS total_amount 
                FROM order_discount_list 
                WHERE invoice_no = '$invoice_no'";
    } else {
        $sql = "SELECT COALESCE(SUM(total_discount_amount), 0) AS total_amount 
                FROM order_discount_list";
    }

    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        return (float)$row['total_amount']; // returns 0.0 if no rows
    }

    return 0; 
}

// ******* Find Shipping Charge ********* //
function find_shipping_charge($invoice_no = '') {
    global $conn;

    // Fetch website information
    $websiteInfoQuery = "SELECT inside_location, inside_delivery_charge, outside_delivery_charge  FROM website_info WHERE id=1";
    $websiteInfoResult = mysqli_query($conn, $websiteInfoQuery);
    $websiteInfo = mysqli_fetch_assoc($websiteInfoResult);

    // Delivery Information
    $inside_location = $websiteInfo['inside_location'] ?? 'Dhaka';
    $inside_delivery_charge = $websiteInfo['inside_delivery_charge'] ?? '80';
    $outside_delivery_charge = $websiteInfo['outside_delivery_charge'] ?? '150';

    $shipping_cost = 0;

    // Check is shipping charge free or not
    if (is_shipping_charge_free($invoice_no) == 1) {
        return $shipping_cost;
    }

    // Fetch Order Info
    $orderInfoQuery = "SELECT city_address FROM order_info WHERE invoice_no = '$invoice_no' LIMIT 1";

    $orderInfoResult = mysqli_query($conn, $orderInfoQuery);
    $orderInfo = mysqli_fetch_assoc($orderInfoResult);


    if ($orderInfo['city_address'] == 'Inside Dhaka') {
        $shipping_cost = $inside_delivery_charge;
    } else if ($orderInfo['city_address'] == 'Outside Dhaka') {
        $shipping_cost = $outside_delivery_charge;
    }

    return $shipping_cost;
}


// ******* Fetch Sub Categories ********* //
function get_sub_categories($main_ctg_name = '', $style = '') {
    global $conn;

    $sql = "SELECT * FROM sub_category WHERE main_ctg_name = '$main_ctg_name' ORDER BY sub_ctg_id DESC";
    $result = $conn->query($sql);

    $output = '';

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $sub_ctg_html = $style;

            $sub_ctg_html = str_replace('#ID#', $row['sub_ctg_id'], $sub_ctg_html);
            $sub_ctg_html = str_replace('#NAME#', $row['sub_ctg_name'], $sub_ctg_html);
            $sub_ctg_html = str_replace('#SLUG#', $row['sub_ctg_slug'], $sub_ctg_html);

            $output .= $sub_ctg_html;
        }
    }

    return $output;
}

// ******* Calculate Product Discount Percentage ********* //
function get_product_discount_percentage($regular_price = '', $selling_price = '') {
    // Defensive checks
    if (!is_numeric($regular_price) || !is_numeric($selling_price)) {
        return 0; // invalid input
    }

    if ($regular_price <= 0 || $selling_price < 0 || $selling_price >= $regular_price) {
        return 0; // no discount
    }

    // Calculate discount percentage
    $discount = (($regular_price - $selling_price) / $regular_price) * 100;

    return round($discount); // rounded percentage
}

// ******* Check Shipping Charge is free or not ********* //
function is_shipping_charge_free($invoice_no = '') {
    global $conn;
    $sql = "SELECT free_shipping FROM order_discount_list WHERE invoice_no = '$invoice_no'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['free_shipping'] ?? 0;
}

//////////////////////////////////////////////////////////////////////////////
/////////////////////// Start Pathao API Integrate ///////////////////////////
/////////////////////////////////////////////////////////////////////////////
// Issue An Access Token /////////////////////////
function get_access_token() {
    global $base_url, $client_id, $client_secret, $username, $password, $grant_type;

    $endpoint = "/aladdin/api/v1/issue-token";
    $api_url = $base_url . $endpoint;

    // Prepare request payload
    $payload = [
        "client_id" => $client_id,
        "client_secret" => $client_secret,
        "username" => $username,
        "password" => $password,
        "grant_type" => $grant_type
    ];

    // Initialize cURL
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    // Execute request
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        die("cURL Error: " . $error);
    }

    curl_close($ch);

    // Decode JSON response
    $data = json_decode($response, true);

    // Check response
    if ($http_code == 200 && isset($data['access_token'])) {
        return $data['access_token']; // You can also return full $data if needed
    } else {
        // Log or handle error
        error_log("Failed to get access token. Response: " . $response);
        return '';
    }
}
// END //////////////////////////////////////////////
////////////////////////////////////////////////////


//////////////////////////////////////////////////
// Create Consignment/Order /////////////////////
function create_pathao_consignment($invoice_no = '') {
    global $conn, $base_url, $store_id;

    if (empty($invoice_no)) {
        return "Invoice number missing!";
    }

    $endpoint = "/aladdin/api/v1/orders";
    $api_url = $base_url . $endpoint;

    // Get Access Token
    $access_token = get_access_token();
    if (empty($access_token)) {
        return "Access token not found!";
    }

    // Fetch Customer Information
    $sql = "SELECT user_full_name, user_phone, user_address, SUM(total_price) as total_price, payment_method 
            FROM order_info WHERE invoice_no = '$invoice_no'";
    $result = mysqli_query($conn, $sql);
    
    if (!$result || mysqli_num_rows($result) == 0) {
        return "Customer Information Not Found!";
    }

    $data = mysqli_fetch_assoc($result);
    $recipient_name = $data['user_full_name'];
    $recipient_phone = $data['user_phone'];
    $recipient_address = $data['user_address'];
    $payment_method = $data['payment_method'];
    $order_amount = $data['total_price'];
    // END

    // Add shipping charge with order amount
    $order_amount += find_shipping_charge($invoice_no);
    // Deduct Discount amount from order amount
    $order_amount -= calculate_discount_amount($invoice_no);

    // Determine amount to collect
    $amount_to_collect = ($payment_method == "Cash On Delivery") ? $order_amount : 0;

    // Build payload
    $payload = [
        "store_id" => $store_id,
        "merchant_order_id" => $invoice_no,      
        "recipient_name" => $recipient_name,
        "recipient_phone" => $recipient_phone,
        "recipient_address" => $recipient_address,
        "delivery_type" => "48",       // 48 = Normal delivery
        "item_type" => "2",            // 1 = Documents, 2 = Parcel
        "special_instruction" => "",    
        "item_quantity" => 1,
        "item_weight" => "0.5",        // Example: 0.5 kg
        "item_description" => "This is my item",
        "amount_to_collect" => $amount_to_collect
    ];

    // Initialize cURL
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $access_token
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    // Execute
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return "cURL Error: " . $error;
    }

    curl_close($ch);

    // Decode response
    $decoded = json_decode($response, true);

    if ($httpcode == 200 || $httpcode == 201) {
        // success response
        //return $decoded; 

        // Save the response to the application database
        return save_parcel_info_to_database($decoded);
    } else {
        return [
            "status" => "error",
            "http_code" => $httpcode,
            "response" => $decoded
        ];
    }
}
// END /////////////////////////////////////////////
///////////////////////////////////////////////////


/////////////////////////////////////////////////////////////////////////
// Save Consignment/Order Response to the database /////////////////////
function save_parcel_info_to_database($response = '') {
    global $conn;

    if (is_array($response) && isset($response['data'])) {
        // Extract the data from API response
        $consignment_id = $response['data']['consignment_id'] ?? '';
        $delivery_fee = $response['data']['delivery_fee'] ?? 0;
        $invoice_no = $response['data']['merchant_order_id'] ?? '';

        // Prepare SQL to insert into database
        $stmt = $conn->prepare("INSERT INTO pathao_parcel_info (invoice_no, consignment_id, delivery_fee) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $invoice_no, $consignment_id, $delivery_fee);

        if ($stmt->execute()) {
            echo "Parcel Successfully Created.";
        } else {
            echo "Error Creating Parcel: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "API Error: " . json_encode($response);
    }

}
/////////////////////////////////////////////////////////////////////////
// End /////////////////////////////////////////////////////////////////


////////////////////////////////////////////////////////
// Get Parcel/Consignment ID //////////////////////////
function get_consignment_id($invoice_no = '') {
    global $conn;

    // Fetch Consignment ID
    $sql = "SELECT consignment_id FROM pathao_parcel_info WHERE invoice_no = '$invoice_no'";
    $result = mysqli_query($conn, $sql);
    
    if (!$result || mysqli_num_rows($result) == 0) {
        return "Consignment Not Found!";
    }

    $data = mysqli_fetch_assoc($result);
    $consignment_id = $data['consignment_id'];

    return $consignment_id;
}
// END //////////////////////////////////////////////
////////////////////////////////////////////////////


////////////////////////////////////////////////////////
// Get Parcel Status //////////////////////////////////
function get_parcel_status($invoice_no = '') {
    global $base_url;

    if (empty($invoice_no)) {
        return "Invoice number missing!";
    }

    // Fetch Consignment ID
    $consignment_id = get_consignment_id($invoice_no);

    if ($consignment_id == "Consignment Not Found!") {
        return "Not Added!";
    }
    $base_url = "https://api-hermes.pathao.com";

    // API Endpoint
    $endpoint = "/aladdin/api/v1/orders/" . $consignment_id . "/info";
    $api_url = $base_url . $endpoint;

    // Get Access Token
    $access_token = get_access_token();
    if (empty($access_token)) {
        return "Access token not found!";
    }

    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $access_token
    ]);

    // Execute GET Request
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return "cURL Error: " . $error_msg;
    }

    curl_close($ch);

    // Decode JSON
    $decoded = json_decode($response, true);

    if ($http_code !== 200 || empty($decoded['data'])) {
        return [
            "status" => "error",
            "http_code" => $http_code,
            "response" => $decoded
        ];
    }

    // Extract useful info
    $info = $decoded['data'];
    $order_status = $info['order_status'] ?? '';

    return $order_status;
}
// END //////////////////////////////////////////////
////////////////////////////////////////////////////


///////////////////////////////////////////////
// Track Parcel //////////////////////////////
function get_track_parcel_url($invoice_no= '') {

    // Fetch Consignment ID
    $consignment_id = get_consignment_id($invoice_no);
    
    if ($consignment_id == "Consignment Not Found!") {
        return "Not Added!";
    }

    $url = 'https://merchant.pathao.com/tracking?consignment_id='.$consignment_id.'';

    return $url;
}
//////////////////////////////////////////
// End //////////////////////////////////

//////////////////////////////////////////////////////////////////////////////
/////////////////////// END Pathao API Integrate ////////////////////////////

?>