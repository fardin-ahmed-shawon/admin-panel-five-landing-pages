<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'Order Details'; // Set the page title
?>
<?php require 'header.php'; ?>

<?php

// Get invoice_no from URL
$invoice_no = $_GET['invoice_no'] ?? '';
if (!$invoice_no) {
    echo "<h4>Invalid invoice number.</h4>";
    exit();
}

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
// End

// Fetch order with payment details
$sql = "SELECT 
    o.*, 
    p.payment_status, 
    p.acc_number, 
    p.transaction_id, 
    p.payment_date,
    u.user_fName,
    u.user_lName,
    u.user_phone AS registered_phone,
    u.user_email AS registered_email,
    u.user_gender
FROM order_info o
LEFT JOIN payment_info p ON o.order_no = p.order_no
LEFT JOIN user_info u ON o.user_id = u.user_id
WHERE o.invoice_no = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $invoice_no);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<h4>No orders found for this invoice.</h4>";
    exit();
}

$orders = $result->fetch_all(MYSQLI_ASSOC);
$order = $orders[0]; // First row for summary
?>

<!--------------------------->
<!-- START MAIN AREA -->
<!--------------------------->
<div class="content-wrapper">
          <div class="page-header">
            <h3 class="page-title">
              <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-home"></i>
              </span> Order Details
            </h3>
          </div>

          <!-- Order Summary -->
          <div class="card mb-4">
            <div class="card-body p-5">
              <h2><strong>Order Summary (<?= htmlspecialchars($order['invoice_no']) ?>)</strong></h2>
              <br>
              <div class="row">
                <div class="col-md-6">
                  <p><strong>Order Date:</strong> <?= date('Y-m-d', strtotime($order['order_date'])) ?></p>

                  <p><strong>Invoice No:</strong> <?= htmlspecialchars($order['invoice_no']) ?></p>
                  
                  <p><strong>Order Amount:</strong> ৳ <?= calculate_order_amount($order['invoice_no']) ?></p>
                  <p><strong>Order Status:</strong> <b class="text-primary"><?= htmlspecialchars($order['order_status']) ?></b></p>
                  <p><strong>Parcel Status:</strong> <b class="text-info"><?= $parcel_status; ?></b></p>
                  <br>
                  <p><strong>Order Note:</strong> <b class="text-success"><?= $order['order_note'] ?></b></p>

                  <?php
                    if ($is_tracking_code_set == 1) {
                      ?>
                      <br>
                      <a href="https://steadfast.com.bd/t/<?= $tracking_code; ?>" class="d-inline btn btn-primary" target="_blank">
                          <i class="mdi mdi-map-marker-path text-xl"></i>
                          Track Your Parcel
                      </a>
                      <br>
                      <?php
                    }
                  ?>

                  <br>
                  <div class="p-3 mt-3" style="background:#e8f9ff; border-left: 4px solid #007bff;">
                    <p><strong>Shipping Name:</strong> <?= htmlspecialchars($order['user_full_name']) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($order['user_phone']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($order['user_email']) ?></p>
                    <p><strong>Address:</strong> <?= htmlspecialchars($order['user_address']) ?></p>
                    <p><strong>City:</strong> <?= htmlspecialchars($order['city_address']) ?></p>
                  </div>

                </div>
                <div class="col-md-6 align-self-end">
                    <div class="p-3 mt-3" style="background:#e8f9ff; border-left: 4px solid #007bff;">
                        <p><strong>Payment Method:</strong> <b class="text-info"><?= htmlspecialchars($order['payment_method']) ?></b></p>
                        <p><strong>Payment Status:</strong> <b class="text-info"><?= htmlspecialchars($order['payment_status'] ?? 'N/A') ?></b></p>
                        <p><strong>Account Number:</strong> <?= htmlspecialchars($order['acc_number'] ?? 'N/A') ?></p>
                        <p><strong>Transaction ID:</strong> <?= htmlspecialchars($order['transaction_id'] ?? 'N/A') ?></p>
                        <p><strong>Payment Date:</strong> <?= $order['payment_date'] ? date('Y-m-d', strtotime($order['payment_date'])) : 'N/A' ?></p>
                    </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Order Details Table -->
              <br>
              <h4><strong>Order Details</strong></h4>
              <div class="table-responsive">
                <table class="table table-bordered table-hover text-center">
                    <thead class="thead-dark">
                    <tr>
                        <th><b>Sr.</b></th>
                        <th><b>Product ID</b></th>
                        <th><b>Product</b></th>
                        <th><b>Quantity</b></th>
                        <th><b>Size</b></th>
                        <th><b>Unit Price</b></th>
                        <th><b>Total Price</b></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php 
                        $total = 0; 
                        foreach ($orders as $i => $row): 
                        $unit_price = $row['product_quantity'] > 0 ? $row['total_price'] / $row['product_quantity'] : 0;
                        $total += $row['total_price'];
                    ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($row['product_id']) ?></td>
                        <td><?= htmlspecialchars($row['product_title']) ?></td>
                        <td><?= $row['product_quantity'] ?></td>
                        <td><?= htmlspecialchars($row['product_size']) ?></td>
                        <td>৳ <?= number_format($unit_price) ?></td>
                        <td>৳ <?= number_format((float)$row['total_price'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="6" class="text-end"><strong>Total Amount:</strong></td>
                        <td><strong>৳ <?= number_format((float)$total, 2) ?></strong></td>
                    </tr>
                    <tr>
                        <td colspan="6" class="text-end"><strong>Discount Amount:</strong></td>
                        <td><strong>৳ <?= number_format((float)calculate_discount_amount($invoice_no), 2) ?></strong></td>
                    </tr>
                    <tr>
                        <td colspan="6" class="text-end"><strong>Shipping Charge:</strong></td>
                        <td><strong>৳ <?= number_format((float)find_shipping_charge($invoice_no), 2) ?></strong></td>
                    </tr>
                    <tr>
                        <td colspan="6" class="text-end"><strong>Final Amount:</strong></td>
                        <td>
                            <strong>৳ <?= number_format((float)$total + (float)find_shipping_charge($invoice_no) - (float)calculate_discount_amount($invoice_no), 2) ?></strong>
                        </td>
                    </tr>


                    </tbody>
                </table>
              </div>
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->

<?php require 'footer.php'; ?>