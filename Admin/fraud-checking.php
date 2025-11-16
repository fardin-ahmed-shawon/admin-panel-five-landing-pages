<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'Fraud Checking'; // Set the page title

// Initialize variables for table and cards
$api_data = [
    'Pathao' => ['total' => 0, 'success' => 0, 'cancel' => 0],
    'Steadfast' => ['total' => 0, 'success' => 0, 'cancel' => 0],
    'Redx' => ['total' => 0, 'success' => 0, 'cancel' => 0],
    'Paperfly' => ['total' => 0, 'success' => 0, 'cancel' => 0],
];
$total = $success = $cancel = 0;
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bd_phone'])) {
    $phone = trim($_POST['bd_phone']);
    // Validate Bangladesh phone number (starts with 01, 11 digits)
    if (preg_match('/^01[3-9]\d{8}$/', $phone)) {
        $api_key = "33967a1c5580cf44e52ae01fdab957dd";
        $url = "https://fraudchecker.link/api/v1/qc/";
        $data = ['phone' => $phone];

        $options = [
            'http' => [
                'header' => "Authorization: Bearer " . $api_key . "\r\n" .
                            "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
                'timeout' => 10
            ]
        ];

        $context = stream_context_create($options);
        $response = @file_get_contents($url, false, $context);

        if ($response !== false) {
            $result = json_decode($response, true);
            if ($result && isset($result['apis'])) {
                // Totals
                $total = isset($result['total_parcels']) ? (int)$result['total_parcels'] : 0;
                $success = isset($result['total_delivered']) ? (int)$result['total_delivered'] : 0;
                $cancel = isset($result['total_cancel']) ? (int)$result['total_cancel'] : 0;

                // Per-courier
                foreach ($api_data as $courier => &$info) {
                    $api_key_name = $courier === 'Paperfly' ? 'PaperFly' : $courier;
                    if (isset($result['apis'][$api_key_name])) {
                        $c = $result['apis'][$api_key_name];
                        $info['total'] = (int)$c['total_parcels'];
                        $info['success'] = (int)$c['total_delivered_parcels'];
                        $info['cancel'] = (int)$c['total_cancelled_parcels'];
                    }
                }
                unset($info);
            } else {
                $error_msg = "<span style='color:red;'>No data found for this phone number.</span>";
            }
        } else {
            $error_msg = "<span style='color:red;'>API Error: Unable to fetch data.</span>";
        }
    } else {
        $error_msg = "<span style='color:red;'>Invalid Bangladesh phone number.</span>";
    }
}
?>
<?php require 'header.php'; ?>

<!--------------------------->
<!-- START MAIN AREA -->
<!--------------------------->
<div class="content-wrapper">
    <div class="row">
        <div class="card col-md-6 mx-auto p-4" style="border-radius: 0;">
            <h1 class="text-center mb-3">Fraud Checker</h1>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label for="bd_phone"><b>Phone Number</b></label>
                        <div class="input-group">
                            <input class="form-control py-0" id="bd_phone" type="text" name="bd_phone" placeholder="e.g. 017XXXXXXXX" pattern="01[3-9][0-9]{8}" maxlength="11" required>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-dark">Search</button>
                            </div>
                        </div>
                    </div>
                </form>
                <?php if (!empty($error_msg)): ?>
                    <div class="alert alert-danger mt-3"><?php echo $error_msg; ?></div>
                <?php endif; ?>
                <div class="mt-4">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Courier</th>
                                    <th>Total</th>
                                    <th>Success</th>
                                    <th>Cancel</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Pathao</td>
                                    <td><?php echo $api_data['Pathao']['total']; ?></td>
                                    <td><?php echo $api_data['Pathao']['success']; ?></td>
                                    <td><?php echo $api_data['Pathao']['cancel']; ?></td>
                                </tr>
                                <tr>
                                    <td>Steadfast</td>
                                    <td><?php echo $api_data['Steadfast']['total']; ?></td>
                                    <td><?php echo $api_data['Steadfast']['success']; ?></td>
                                    <td><?php echo $api_data['Steadfast']['cancel']; ?></td>
                                </tr>
                                <tr>
                                    <td>Redx</td>
                                    <td><?php echo $api_data['Redx']['total']; ?></td>
                                    <td><?php echo $api_data['Redx']['success']; ?></td>
                                    <td><?php echo $api_data['Redx']['cancel']; ?></td>
                                </tr>
                                <tr>
                                    <td>Paperfly</td>
                                    <td><?php echo $api_data['Paperfly']['total']; ?></td>
                                    <td><?php echo $api_data['Paperfly']['success']; ?></td>
                                    <td><?php echo $api_data['Paperfly']['cancel']; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col-12 col-sm-4 mb-2">
                        <div class="card text-center bg-info" style="min-width: 100px;">
                            <div class="card-body p-3">
                                <h5 class="mb-1 text-white"><b>Total</b></h5>
                                <span class="h5 mb-0 text-white"><?php echo $total; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-4 mb-2">
                        <div class="card text-center bg-success" style="min-width: 100px;">
                            <div class="card-body p-3">
                                <h5 class="mb-1 text-white"><b>Success</b></h5>
                                <span class="h5 mb-0 text-white"><?php echo $success; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-4 mb-2">
                        <div class="card text-center bg-danger" style="min-width: 100px;">
                            <div class="card-body p-3">
                                <h5 class="mb-1 text-white"><b>Cancel</b></h5>
                                <span class="h5 mb-0 text-white"><?php echo $cancel; ?></span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->

<?php require 'footer.php'; ?>