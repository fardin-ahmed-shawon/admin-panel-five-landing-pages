<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'Total Collection'; // Set the page title
?>
<?php require 'header.php'; ?>

<?php

// Handle search and sorting
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$payment_method = isset($_GET['payment_method']) ? $_GET['payment_method'] : '';

$query = "
    SELECT 
    o.order_no, 
    o.invoice_no, 
    o.total_price, 
    o.order_status,
    o.payment_method, 
    o.order_date
    FROM order_info o
    LEFT JOIN payment_info p ON o.order_no = p.order_no
    WHERE o.order_status = 'Completed'
";

if ($date_from && $date_to) {
    $query .= " AND o.order_date BETWEEN '$date_from' AND '$date_to'";
}

if ($payment_method) {
    $query .= " AND o.payment_method = '$payment_method'";
}

$result = mysqli_query($conn, $query);
$total_collections = 0;
?>

<style>
      .table-responsive {
          max-height: 500px; /* Fixed height for the table */
          overflow-y: auto;
      }
      table thead th {
          position: sticky;
          top: 0;
          background: #f8f9fa; /* Header background color */
          z-index: 1;
      }
      table tfoot td {
          position: sticky;
          bottom: 0;
          background: #f8f9fa; /* Footer background color */
          z-index: 1;
      }
</style>

<!--------------------------->
<!-- START MAIN AREA -->
<!--------------------------->
<div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title">
                    <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-finance"></i>
                    </span> Accounts
                </h3>
            </div>
            <div class="row">
                <h1>Total Collections</h1>
                <form method="GET" class="mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="date_from">Date From:</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" value="<?php echo $date_from; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="date_to">Date To:</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" value="<?php echo $date_to; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="payment_method">Payment Method:</label>
                            <select name="payment_method" id="payment_method" class="form-control">
                                <option value="">All</option>
                                <option value="Cash On Delivery" <?php echo $payment_method == 'Cash On Delivery' ? 'selected' : ''; ?>>Cash On Delivery</option>
                                <option value="bKash" <?php echo $payment_method == 'bKash' ? 'selected' : ''; ?>>bKash</option>
                                <option value="Nagad" <?php echo $payment_method == 'Nagad' ? 'selected' : ''; ?>>Nagad</option>
                                <option value="Rocket" <?php echo $payment_method == 'Rocket' ? 'selected' : ''; ?>>Rocket</option>
                                <option value="Upay" <?php echo $payment_method == 'Upay' ? 'selected' : ''; ?>>Upay</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary mt-4">Search</button>
                        </div>
                    </div>
                </form>
                <div class="col-lg-12 grid-margin stretch-card">
                    
                        <div class="table-responsive w-100">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th><b>Order No</b></th>
                                    <th><b>Invoice No</b></th>
                                    <th><b>Collection Amount (Tk)</b></th>
                                    <th><b>Payment Method</b></th>
                                    <th><b>Order Date & Time</b></th>
                                    <th><b>Action</b></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr>
                                                <td>{$row['order_no']}</td>
                                                <td>{$row['invoice_no']}</td>
                                                <td>" . number_format($row['total_price'], 2) . " Tk.</td>
                                                <td>{$row['payment_method']}</td>
                                                <td>{$row['order_date']}</td>
                                                
                                                <td>
                                                    <a href='order_details.php?invoice_no={$row['invoice_no']}'>
                                                      <button class='btn btn-info'>View Details <span class='mdi mdi-details'></span></button>
                                                    </a>
                                                  </td>
                                                
                                                </tr>
                                                
                                                ";
                                            $total_collections += $row['total_price'];
                                    }
                                } else {
                                    echo "<tr><td colspan='6'>No records found</td></tr>";
                                }
                                ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td></td>
                                        
                                        <td><b>Total Collections</b></td>
                                        <td><b><?php echo number_format($total_collections, 2); ?> Tk.</b></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    
                </div>
            </div>
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->

<?php require 'footer.php'; ?>