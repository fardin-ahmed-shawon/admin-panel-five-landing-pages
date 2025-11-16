<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'Purchase History'; // Set the page title
?>
<?php require 'header.php'; ?>

<?php
// Fetch order history
$searchQuery = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchQuery = $conn->real_escape_string($_GET['search']);
    $sql = "SELECT * FROM order_info WHERE invoice_no LIKE '%$searchQuery%' ORDER BY order_no DESC";
} else {
    $sql = "SELECT * FROM order_info ORDER BY order_no DESC";
}

$result = $conn->query($sql);
?>

<!--------------------------->
<!-- START MAIN AREA -->
<!--------------------------->
<div class="content-wrapper">
            <div class="page-header">
              <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                  <i class="mdi mdi-home"></i>
                </span> Purchase History
              </h3>
            </div>
            <br>
            <div class="row">
              <h1>Purchase History</h1>
              <div style=" padding: 20px; border-radius: 10px; background-color: #f8f9fa; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
              
              
              <div class="search-row row">
                <div class="search-row dropdown" style="display: flex; align-items: center; position: relative;">
                    <form class="form-group" method="GET" action="purchaseHistory.php" style="display: flex; flex: 1;">
                        <input id="search" type="search" name="search" required="" placeholder="Search Invoice No" autocomplete="off" class="form-control" 
                            style="width: 100%; max-width: 500px; flex: 1; border: 1px solid #ccc; border-right: none; border-radius: 5px 0 0 5px; padding: 10px;" 
                            value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">

                        <button type="submit" style="background: black; border: none; padding: 10px 15px; border-radius: 0 5px 5px 0; cursor: pointer; color: white; font-size: 14px;">
                            Search
                        </button>
                    </form>
                </div>
            </div>

              <div style="max-height: 600px; overflow: auto;">
                <table class="table table-under-bordered">
                  <tbody>
                    <tr>
                      <th>Order No</th>
                      <th>User ID</th>
                      <th>Customer Name</th>
                      <th>Customer Phone</th>
                      <th>Customer Email</th>
                      <th>Shipping Address</th>
                      <th>Invoice No</th>
                      <th>Product ID</th>
                      <th>Quantity</th>
                      <th>Total</th>
                      <th>Order Date</th>
                      <th>Payment Method</th>
                      <th>Order Status</th>
                      <!-- <th>Action</th> -->
                    </tr>
                                    <?php
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>
                                                <td>{$row['order_no']}</td>
                                                <td>{$row['user_id']}</td>
                                                <td>{$row['user_full_name']}</td>
                                                <td>{$row['user_phone']}</td>
                                                <td>{$row['user_email']}</td>
                                                <td>{$row['user_address']}</td>
                                                <td>{$row['invoice_no']}</td>
                                                <td>{$row['product_id']}</td>
                                                <td>{$row['product_quantity']}</td>
                                                <td>{$row['total_price']} Tk</td>
                                                <td>{$row['order_date']}</td>
                                                <td>{$row['payment_method']}</td>
                                                <td class='order-status'>{$row['order_status']}</td>
                                                <!--
                                                <td><button class='btn btn-danger'>Delete</button></td>
                                                -->
                                            </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='12'>No orders found</td></tr>";
                                    }
                                    ?>
                </tbody>
               </table>
              </div>

              <!-- <br>
              <a href="#">
                <button class="btn btn-dark">Delete All History <span class="mdi mdi-delete"></span></button>
              </a> -->
              </div>
            </div>
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->

<?php require 'footer.php'; ?>