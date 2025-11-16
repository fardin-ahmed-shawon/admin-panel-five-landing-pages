<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'Inventory'; // Set the page title
?>
<?php require 'header.php'; ?>

<?php

$sql = "SELECT * FROM product_info";
$result = mysqli_query($conn, $sql);
$row = mysqli_num_rows($result);


// Calculate total stock, total stock amount, and total profit amount
$total_stock = 0;
$total_stock_amount = 0;
$total_profit_amount = 0;

if ($row > 0) {
    while ($item = mysqli_fetch_array($result)) {
        $total_stock += $item['available_stock'];
        $total_stock_amount += $item['product_price'] * $item['available_stock'];

        $total_profit_amount += ($item['product_price'] - $item['product_purchase_price']) * $item['available_stock'];
    }
}
// END
?>

<!--------------------------->
<!-- START MAIN AREA -->
<!--------------------------->
<div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title">
                    <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-storefront"></i>
                    </span> Inventory
                </h3>
            </div>

            <!--------- Card --------->
            <div class="row">
              <h1>Inventory/Stock Report</h1>
              <div class="col-md-4">
                <div class="card bg-dark text-white p-3">
                  <div class="card-body">
                    <h1>Total <br>Unit</h1>
                    <h2><?php echo $total_stock; ?></h2>
                  </div>
                </div>
              </div>
              <div class="col-md-4 mt-3 mt-md-0">
                <div class="card bg-dark text-white p-3">
                  <div class="card-body">
                    <h1>Total <br>Product Amount</h1>
                    <h2>৳ <?php echo $total_stock_amount; ?></h2>
                  </div>
                </div>
              </div>
              <div class="col-md-4 mt-3 mt-md-0">
                <div class="card bg-dark text-white p-3">
                  <div class="card-body">
                    <h1>Total <br>Profit Amount</h1>
                    <h2>৳ <?php echo $total_profit_amount; ?></h2>
                  </div>
                </div>
              </div>
            </div>
            <br><br>

            <!--------- Searching & Sorting --------->
            <div class="row">
              <h1>Search Your Product</h1>
              <form method="GET" class="mb-3">
              <div class="row">
                <div class="col-md-6">
                  <label for="search"><b>Enter Product Title:</b></label>
                  <div class="d-flex">
                    <input type="text" name="search" id="search" class="form-control me-2" placeholder="Search by product title"
                      value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit" class="btn btn-primary mx-2">Search</button>
                    <!-- Reset Button -->
                    <?php if (isset($_GET['search']) && $_GET['search'] !== ''): ?>
                          <a href="<?php echo strtok($_SERVER["REQUEST_URI"], '?'); ?>" class="btn btn-dark d-flex align-items-center" title="Reset Search">
                            Reset
                          </a>
                        <?php endif; ?>
                  </div>
                </div>
              </div>
              </form>
            </div>
            <br><br>

            <!--------- Product List --------->
            <div class="row">
              <h1>Product List</h1>
              <div class="container">
                <div class="table-responsive">
                  <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                      <tr>
                        <td>ID</td>
                        <td>Image</td>
                        <td>Title</td>
                        <td>Main Category</td>
                        <td>Sub Category</td>
                        <td>Total Stock</td>
                        <td>Purchase Price</td>
                        <td>Selling Price</td>
                        <td>Total Stock Amount</td>
                        <td>Total Profit Amount</td>
                      </tr>
                    </thead>
                    <tbody>
                      <?php

                        $search = isset($_GET['search']) ? trim($_GET['search']) : '';

                        $sql = "SELECT p.*, mc.main_ctg_name, sc.sub_ctg_name 
                                FROM product_info p
                                LEFT JOIN main_category mc ON p.main_ctg_id = mc.main_ctg_id
                                LEFT JOIN sub_category sc ON p.sub_ctg_id = sc.sub_ctg_id";

                        if (!empty($search)) {
                            $search_safe = mysqli_real_escape_string($conn, $search);
                            $sql .= " WHERE p.product_title LIKE '%$search_safe%'";
                        }
                        $sql .= " ORDER BY p.product_id DESC";

                        $result = mysqli_query($conn, $sql);
                        if (mysqli_num_rows($result) === 0) {
                          echo "<tr><td colspan='10' class='text-center text-danger'>No products found.</td></tr>";
                      }
                      

                        while ($item = mysqli_fetch_array($result)) {
                          $stock_amount = $item['product_price'] * $item['available_stock'];
                          $profit_amount = ($item['product_price'] - $item['product_purchase_price']) * $item['available_stock'];

                          echo "<tr>
                                  <td>{$item['product_id']}</td>
                                  <td><img src='../img/{$item['product_img1']}' alt='img' style='width: 50px; height: 50px;'></td>
                                  <td>{$item['product_title']}</td>
                                  
                                  <td>{$item['main_ctg_name']}</td>
                                  <td>{$item['sub_ctg_name']}</td>
                                  <td>{$item['available_stock']}</td>
                                  <td>৳ {$item['product_purchase_price']}</td>
                                  <td>৳ {$item['product_price']}</td>
                                  <td>৳ {$stock_amount}</td>
                                  <td>৳ {$profit_amount}</td>
                                  
                                </tr>";
                        }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->

<?php require 'footer.php'; ?>