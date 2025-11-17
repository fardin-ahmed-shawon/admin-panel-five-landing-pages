<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'Dashboard'; // Set the page title
?>
<?php require 'header.php'; ?>

<?php
// Update order status to "Processing" if Accept button is pressed
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accept_invoice'])) {
  $invoice_no = $_POST['invoice_no'];

  // Update order_info table
  $update_sql = "UPDATE order_info SET order_status='Processing' WHERE invoice_no=?";
  $stmt = $conn->prepare($update_sql);
  $stmt->bind_param("s", $invoice_no);
  $stmt->execute();
  $stmt->close();

  // Update payment_info table
  $update_sql = "UPDATE payment_info SET order_status='Processing' WHERE invoice_no=?";
  $stmt = $conn->prepare($update_sql);
  $stmt->bind_param("s", $invoice_no);
  $stmt->execute();
  $stmt->close();
}

// Fetch sales data for charts
$timeRange = isset($_GET['range']) ? $_GET['range'] : '30';

// Daily Sales Trend
$dailySalesQuery = "SELECT 
    DATE(order_date) as date,
    SUM(total_price) as revenue,
    COUNT(DISTINCT invoice_no) as orders
FROM order_info
WHERE order_date >= DATE_SUB(CURDATE(), INTERVAL $timeRange DAY)
    AND order_status != 'Canceled'
GROUP BY DATE(order_date)
ORDER BY date";
$dailySalesResult = $conn->query($dailySalesQuery);
$dailySalesData = [];
while($row = $dailySalesResult->fetch_assoc()) {
    $dailySalesData[] = $row;
}

// Top Selling Products
$topProductsQuery = "SELECT 
    p.product_title as name,
    SUM(o.total_price) as sales,
    SUM(o.product_quantity) as quantity
FROM order_info o
JOIN product_info p ON o.product_id = p.product_id
WHERE o.order_status != 'Canceled'
GROUP BY p.product_id, p.product_title
ORDER BY sales DESC
LIMIT 5";
$topProductsResult = $conn->query($topProductsQuery);
$topProductsData = [];
while($row = $topProductsResult->fetch_assoc()) {
    $topProductsData[] = $row;
}

// Payment Methods Distribution
$paymentMethodsQuery = "SELECT 
    payment_method as name,
    COUNT(*) as value,
    SUM(total_price) as amount
FROM order_info
WHERE order_status != 'Canceled'
GROUP BY payment_method
ORDER BY amount DESC";
$paymentMethodsResult = $conn->query($paymentMethodsQuery);
$paymentMethodsData = [];
while($row = $paymentMethodsResult->fetch_assoc()) {
    $paymentMethodsData[] = $row;
}

// City-wise Sales
$cityWiseQuery = "SELECT 
    city_address as city,
    COUNT(DISTINCT invoice_no) as orders,
    SUM(total_price) as revenue
FROM order_info
WHERE order_status != 'Canceled'
GROUP BY city_address
ORDER BY revenue DESC
LIMIT 5";
$cityWiseResult = $conn->query($cityWiseQuery);
$cityWiseData = [];
while($row = $cityWiseResult->fetch_assoc()) {
    $cityWiseData[] = $row;
}
?>

<style>
  /* Modern Stats Card Styles */
  .stats-card {
    position: relative;
    padding: 24px;
    border-radius: 20px;
    background: #fff;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    overflow: visible;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    height: 100%;
    border: 1px solid rgba(0, 0, 0, 0.05);
    min-height: 140px;
    display: flex;
    flex-direction: column;
  }

  .stats-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    border-radius: 0 0 20px 20px;
  }

  .stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, var(--gradient-start), var(--gradient-end));
    opacity: 0;
    transition: opacity 0.3s ease;
    border-radius: 20px 20px 0 0;
  }

  .stats-card:hover::before {
    opacity: 1;
  }

  .stats-icon {
    position: absolute;
    top: 20px;
    right: 20px;
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
  }

  .stats-card:hover .stats-icon {
    transform: rotate(10deg) scale(1.1);
  }

  .stats-icon i {
    font-size: 28px;
    color: #fff;
  }

  .stats-content {
    position: relative;
    z-index: 1;
    padding-right: 76px;
  }

  .stats-label {
    font-size: 14px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 12px;
  }

  .stats-value {
    font-size: 36px;
    font-weight: 600;
    background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin: 0;
    line-height: 1.2;
    margin-bottom: 16px;
  }

  .stats-trend {
    margin-top: auto;
  }

  .trend-icon {
    font-size: 22px;
    font-weight: bold;
    animation: bounce 2s infinite;
  }

  @keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
  }

  .stats-badge {
    margin-top: auto;
    padding: 8px 16px;
    border-radius: 25px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    display: inline-block;
    width: fit-content;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  }

  /* Gradient Variants */
  .stats-gradient-danger { --gradient-start: #ef4444; --gradient-end: #dc2626; }
  .stats-gradient-info { --gradient-start: #3b82f6; --gradient-end: #2563eb; }
  .stats-gradient-success { --gradient-start: #10b981; --gradient-end: #059669; }
  .stats-gradient-primary { --gradient-start: #8b5cf6; --gradient-end: #7c3aed; }
  .stats-gradient-warning { --gradient-start: #f59e0b; --gradient-end: #d97706; }
  .stats-gradient-purple { --gradient-start: #a855f7; --gradient-end: #9333ea; }
  .stats-gradient-dark { --gradient-start: #64748b; --gradient-end: #475569; }

  /* Enhanced Chart Card Styles */
  .chart-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border-radius: 24px;
    padding: 30px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    margin-bottom: 24px;
    border: 1px solid rgba(255, 255, 255, 0.5);
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
  }

  .chart-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.05) 0%, transparent 70%);
    pointer-events: none;
  }

  .chart-card:hover {
    box-shadow: 0 20px 45px rgba(0, 0, 0, 0.15);
    transform: translateY(-5px);
  }

  .chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    position: relative;
    z-index: 1;
  }

  .chart-title {
    font-size: 22px;
    font-weight: 900;
    background: linear-gradient(135deg, #1e293b 0%, #475569 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .chart-title::before {
    content: '';
    width: 4px;
    height: 24px;
    background: linear-gradient(180deg, #3b82f6, #8b5cf6);
    border-radius: 2px;
  }

  .time-filter {
    display: flex;
    gap: 8px;
    background: #fff;
    padding: 6px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  }

  .time-btn {
    padding: 10px 20px;
    border: none;
    background: transparent;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    color: #64748b;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
  }

  .time-btn:hover {
    background: #f1f5f9;
    color: #3b82f6;
    transform: translateY(-2px);
  }

  .time-btn.active {
    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
    color: #fff;
    box-shadow: 0 8px 16px rgba(59, 130, 246, 0.3);
  }

  /* Modern Table Styles */
  .modern-table-container {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border-radius: 24px;
    padding: 30px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
  }

  .modern-table-container:hover {
    box-shadow: 0 20px 45px rgba(0, 0, 0, 0.15);
    transform: translateY(-3px);
  }

  .table-header {
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 2px solid #e2e8f0;
  }

  .table-header h1 {
    font-size: 28px;
    font-weight: 800;
    background: linear-gradient(135deg, #1e293b 0%, #475569 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 8px;
  }

  .table-header p {
    color: #64748b;
    font-size: 15px;
    margin: 0;
  }

  .modern-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
  }

  .modern-table thead th {
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    color: #fff;
    padding: 16px;
    font-weight: 700;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    border: none;
    white-space: nowrap;
  }

  .modern-table thead th:first-child {
    border-radius: 12px 0 0 0;
  }

  .modern-table thead th:last-child {
    border-radius: 0 12px 0 0;
  }

  .modern-table tbody tr {
    transition: all 0.3s ease;
    background: #fff;
  }

  .modern-table tbody tr:hover {
    background: linear-gradient(90deg, #f8fafc 0%, #f1f5f9 100%);
    transform: scale(1.01);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  }

  .modern-table tbody td {
    padding: 16px;
    border-bottom: 1px solid #e2e8f0;
    color: #334155;
    font-size: 14px;
    font-weight: 500;
  }

  .modern-table tbody tr:last-child td {
    border-bottom: none;
  }

  .action-btn {
    padding: 10px 20px;
    border: none;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  }

  .action-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
  }

  .btn-accept {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #fff;
  }

  .btn-decline {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: #fff;
  }

  .view-all-btn {
    width: 100%;
    padding: 16px;
    border: none;
    border-radius: 12px;
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    color: #fff;
    font-size: 15px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 20px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
  }

  .view-all-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
  }

  /* Status Badge */
  .status-badge {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: inline-block;
  }

  .status-primary {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: #fff;
    box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
  }

  /* Responsive */
  @media (max-width: 1199px) {
    .stats-value { font-size: 28px; }
    .stats-icon { width: 50px; height: 50px; }
    .stats-icon i { font-size: 24px; }
  }

  @media (max-width: 767px) {
    .stats-card { padding: 20px; }
    .stats-value { font-size: 24px; }
    .stats-icon { width: 46px; height: 46px; top: 16px; right: 16px; }
    .stats-icon i { font-size: 20px; }
    .chart-card, .modern-table-container { padding: 20px; }
    .time-filter { flex-wrap: wrap; }
    .chart-title { font-size: 18px; }
    .modern-table { font-size: 12px; }
    .modern-table thead th, .modern-table tbody td { padding: 12px 8px; }
  }

  /* Chart Canvas Wrapper */
  .chart-canvas-wrapper {
    position: relative;
    /* height: 350px; */
    z-index: 1;
  }
  

  /* Badge variants */
  .badge-warning { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #92400e; }
  .badge-info { background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #1e40af; }
  .badge-success { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #065f46; }
  .badge-purple { background: linear-gradient(135deg, #e9d5ff, #d8b4fe); color: #6b21a8; }
  .badge-dark { background: linear-gradient(135deg, #e2e8f0, #cbd5e1); color: #334155; }
</style>

<!--------------------------->
<!-- START MAIN AREA -->
<!--------------------------->
<div class="content-wrapper">
            <div class="page-header">
              <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                  <i class="mdi mdi-home"></i>
                </span> Dashboard
              </h3>
            </div>

            <!-- Dashboard Stats Area -->
            <div class="row g-4">

              <!-- Total Products -->
              <div class="col-xl-3 col-md-6" onclick="window.location.href='viewProduct.php';" style="cursor: pointer;">
                <div class="stats-card stats-gradient-danger">
                  <div class="stats-icon">
                    <i class="mdi mdi-apps"></i>
                  </div>
                  <div class="stats-content">
                    <h6 class="stats-label">Total Products</h6>
                    <h2 class="stats-value">
                      <?php
                      $sql = "SELECT COUNT(product_id) AS total_products FROM product_info";
                      $result = $conn->query($sql);
                      $row = $result->fetch_assoc();
                      echo $row['total_products'];
                      ?>
                    </h2>
                  </div>
                  <div class="stats-trend">
                    <span class="trend-icon">↗</span>
                  </div>
                </div>
              </div>

              <!-- Product Categories -->
              <div class="col-xl-3 col-md-6" onclick="window.location.href='viewCategory.php';" style="cursor: pointer;">
                <div class="stats-card stats-gradient-info">
                  <div class="stats-icon">
                    <i class="mdi mdi-order-bool-ascending"></i>
                  </div>
                  <div class="stats-content">
                    <h6 class="stats-label">Product Categories</h6>
                    <h2 class="stats-value">
                      <?php
                      $sql = "SELECT COUNT(main_ctg_id) AS total_categories FROM main_category";
                      $result = $conn->query($sql);
                      $row = $result->fetch_assoc();
                      echo $row['total_categories'];
                      ?>
                    </h2>
                  </div>
                  <div class="stats-trend">
                    <span class="trend-icon">→</span>
                  </div>
                </div>
              </div>

              <?php if (isset($access['inventory']) && $access['inventory'] == 1) { ?>
              <!-- Total Stock -->
              <div class="col-xl-3 col-md-6" onclick="window.location.href='inventory.php';" style="cursor: pointer;">
                <div class="stats-card stats-gradient-success">
                  <div class="stats-icon">
                    <i class="mdi mdi-archive-clock-outline"></i>
                  </div>
                  <div class="stats-content">
                    <h6 class="stats-label">Total Stock Unit</h6>
                    <h2 class="stats-value">
                      <?php
                      $sql = "SELECT SUM(available_stock) AS total FROM product_info";
                      $result = $conn->query($sql);
                      $row = $result->fetch_assoc();
                      echo number_format($row['total']);
                      ?>
                    </h2>
                  </div>
                  <div class="stats-trend">
                    <span class="trend-icon">↗</span>
                  </div>
                </div>
              </div>
              <?php } ?>

              <?php if (isset($access['customers']) && $access['customers'] == 1) { ?>
              <!-- Total Customers -->
              <div class="col-xl-3 col-md-6" onclick="window.location.href='viewCustomers.php';" style="cursor: pointer;">
                <div class="stats-card stats-gradient-primary">
                  <div class="stats-icon">
                    <i class="mdi mdi-account"></i>
                  </div>
                  <div class="stats-content">
                    <h6 class="stats-label">Customers</h6>
                    <h2 class="stats-value">
                      <?php
                      $sql = "SELECT COUNT(user_id) AS total_customers FROM user_info";
                      $result = $conn->query($sql);
                      $row = $result->fetch_assoc();
                      echo number_format($row['total_customers']);
                      ?>
                    </h2>
                  </div>
                  <div class="stats-trend">
                    <span class="trend-icon">↗</span>
                  </div>
                </div>
              </div>
              <?php } ?>

              <?php if (isset($access['orders']) && $access['orders'] == 1) { ?>
              <!-- Total Purchased Unit -->
              <div class="col-xl-3 col-md-6" onclick="window.location.href='purchaseHistory.php';" style="cursor: pointer;">
                <div class="stats-card stats-gradient-primary">
                  <div class="stats-icon">
                    <i class="mdi mdi-cart-variant"></i>
                  </div>
                  <div class="stats-content">
                    <h6 class="stats-label">Total Purchased Unit</h6>
                    <h2 class="stats-value">
                      <?php
                      $sql = "SELECT SUM(product_quantity) AS total FROM order_info";
                      $result = $conn->query($sql);
                      $row = $result->fetch_assoc();
                      echo number_format($row['total'] ?? 0);
                      ?>
                    </h2>
                  </div>
                  <div class="stats-trend">
                    <span class="trend-icon">↗</span>
                  </div>
                </div>
              </div>
              <?php } ?>

              <?php if (isset($access['accounts']) && $access['accounts'] == 1) { ?>
              <!-- Total Sales -->
              <div class="col-xl-3 col-md-6" onclick="window.location.href='total-collections.php';" style="cursor: pointer;">
                <div class="stats-card stats-gradient-success">
                  <div class="stats-icon">
                    <i class="mdi mdi-cash-check"></i>
                  </div>
                  <div class="stats-content">
                    <h6 class="stats-label">Total Sales</h6>
                    <h2 class="stats-value">
                      <?php
                      $sql = "SELECT SUM(total_price) AS total_collection FROM order_info WHERE order_status = 'Completed'";
                      $result = $conn->query($sql);
                      $row = $result->fetch_assoc();
                      echo "৳ " . number_format($row['total_collection']);
                      ?>
                    </h2>
                  </div>
                  <div class="stats-trend">
                    <span class="trend-icon">↗</span>
                  </div>
                </div>
              </div>
              <?php } ?>

              <?php if (isset($access['orders']) && $access['orders'] == 1) { ?>
              <!-- Pending Orders -->
              <div class="col-xl-3 col-md-6" onclick="window.location.href='pendingOrders.php';" style="cursor: pointer;">
                <div class="stats-card stats-gradient-warning">
                  <div class="stats-icon">
                    <i class="mdi mdi-cart-arrow-down"></i>
                  </div>
                  <div class="stats-content">
                    <h6 class="stats-label">Pending Orders</h6>
                    <h2 class="stats-value">
                      <?php
                      $sql = "SELECT COUNT(order_no) AS total_orders FROM order_info WHERE order_visibility='Show' AND order_status='Pending'";
                      $result = $conn->query($sql);
                      $row = $result->fetch_assoc();
                      echo $row['total_orders'];
                      ?>
                    </h2>
                  </div>
                  <div class="stats-badge badge-warning">Action Required</div>
                </div>
              </div>

              <!-- Approved Orders -->
              <div class="col-xl-3 col-md-6" onclick="window.location.href='viewOrders.php';" style="cursor: pointer;">
                <div class="stats-card stats-gradient-info">
                  <div class="stats-icon">
                    <i class="mdi mdi-cart-arrow-up"></i>
                  </div>
                  <div class="stats-content">
                    <h6 class="stats-label">Approved Orders</h6>
                    <h2 class="stats-value">
                      <?php
                      $sql = "SELECT COUNT(order_no) AS total_orders FROM order_info WHERE order_visibility='Show' AND order_status !='Pending'";
                      $result = $conn->query($sql);
                      $row = $result->fetch_assoc();
                      echo $row['total_orders'];
                      ?>
                    </h2>
                  </div>
                  <div class="stats-trend">
                    <span class="trend-icon">↗</span>
                  </div>
                </div>
              </div>

              <!-- Processing Orders -->
              <div class="col-xl-3 col-md-6" onclick="window.location.href='makeInvoice.php';" style="cursor: pointer;">
                <div class="stats-card stats-gradient-info">
                  <div class="stats-icon">
                    <i class="mdi mdi-cart-outline"></i>
                  </div>
                  <div class="stats-content">
                    <h6 class="stats-label">Processing Orders</h6>
                    <h2 class="stats-value">
                      <?php
                      $sql = "SELECT COUNT(order_no) AS total_orders FROM order_info WHERE order_visibility='Show' AND order_status ='Processing'";
                      $result = $conn->query($sql);
                      $row = $result->fetch_assoc();
                      echo $row['total_orders'];
                      ?>
                    </h2>
                  </div>
                  <div class="stats-badge badge-info">In Progress</div>
                </div>
              </div>

              <!-- Shipped Orders -->
              <div class="col-xl-3 col-md-6" onclick="window.location.href='makeInvoice.php?search_query=&from_date=&to_date=&main_ctg=&filter=Shipped';" style="cursor: pointer;">
                <div class="stats-card stats-gradient-purple">
                  <div class="stats-icon">
                    <i class="mdi mdi-cart-arrow-right"></i>
                  </div>
                  <div class="stats-content">
                    <h6 class="stats-label">On The Way</h6>
                    <h2 class="stats-value">
                      <?php
                      $sql = "SELECT COUNT(order_no) AS total_orders FROM order_info WHERE order_visibility='Show' AND order_status = 'Shipped'";
                      $result = $conn->query($sql);
                      $row = $result->fetch_assoc();
                      echo $row['total_orders'];
                      ?>
                    </h2>
                  </div>
                  <div class="stats-badge badge-purple">Shipping</div>
                </div>
              </div>

              <!-- Delivered Orders -->
              <div class="col-xl-3 col-md-6" onclick="window.location.href='makeInvoice.php?search_query=&from_date=&to_date=&main_ctg=&filter=Completed';" style="cursor: pointer;">
                <div class="stats-card stats-gradient-success">
                  <div class="stats-icon">
                    <i class="mdi mdi-cart-check"></i>
                  </div>
                  <div class="stats-content">
                    <h6 class="stats-label">Delivered Orders</h6>
                    <h2 class="stats-value">
                      <?php
                      $sql = "SELECT COUNT(order_no) AS total_orders FROM order_info WHERE order_status ='Completed'";
                      $result = $conn->query($sql);
                      $row = $result->fetch_assoc();
                      echo $row['total_orders'];
                      ?>
                    </h2>
                  </div>
                  <div class="stats-badge badge-success">Completed</div>
                </div>
              </div>

              <!-- Cancelled Orders -->
              <div class="col-xl-3 col-md-6" onclick="window.location.href='makeInvoice.php?search_query=&from_date=&to_date=&main_ctg=&filter=Canceled';" style="cursor: pointer;">
                <div class="stats-card stats-gradient-dark">
                  <div class="stats-icon">
                    <i class="mdi mdi-cart-remove"></i>
                  </div>
                  <div class="stats-content">
                    <h6 class="stats-label">Cancelled Orders</h6>
                    <h2 class="stats-value">
                      <?php
                      $sql = "SELECT COUNT(order_no) AS total_orders FROM order_info WHERE order_status ='Canceled'";
                      $result = $conn->query($sql);
                      $row = $result->fetch_assoc();
                      echo $row['total_orders'];
                      ?>
                    </h2>
                  </div>
                  <div class="stats-badge badge-dark">Cancelled</div>
                </div>
              </div>
              <?php } ?>

            </div>
            <br>

            <!-- Sales Charts Area -->
            <?php if (isset($access['accounts']) && $access['accounts'] == 1) { ?>
            <div class="row">
              <!-- Sales Trend Chart -->
              <div class="col-lg-12">
                <div class="chart-card">
                  <div class="chart-header">
                    <h3 class="chart-title">Sales Trend</h3>
                    <div class="time-filter">
                      <a href="?range=7" class="time-btn <?php echo $timeRange == 7 ? 'active' : ''; ?>">7 Days</a>
                      <a href="?range=30" class="time-btn <?php echo $timeRange == 30 ? 'active' : ''; ?>">30 Days</a>
                      <a href="?range=90" class="time-btn <?php echo $timeRange == 90 ? 'active' : ''; ?>">90 Days</a>
                    </div>
                  </div>
                  <canvas id="salesTrendChart" height="100"></canvas>
                </div>
              </div>

              <!-- Payment Methods Chart -->
              <!-- <div class="col-lg-4">
                <div class="chart-card">
                  <div class="chart-header">
                    <h3 class="chart-title">Payment Methods</h3>
                  </div>
                  <div class="chart-canvas-wrapper">
                    <canvas id="paymentMethodChart" height="100"></canvas>
                  </div>
                </div>
              </div> -->

              <!-- Top Products Chart -->
              <div class="col-lg-6">
                <div class="chart-card">
                  <div class="chart-header">
                    <h3 class="chart-title">Top Selling Products</h3>
                  </div>
                  <canvas id="topProductsChart" height="150"></canvas>
                </div>
              </div>

              <!-- City-wise Sales Chart -->
              <div class="col-lg-6">
                <div class="chart-card">
                  <div class="chart-header">
                    <h3 class="chart-title">Sales by City</h3>
                  </div>
                  <canvas id="cityWiseChart" height="150"></canvas>
                </div>
              </div>
            </div>
            <br>
            <?php } ?>

            <!-- Latest Pending Orders & Latest Parcel Area -->
            <div class="row">
            <?php 
              // admin & autherized user can access this area
              if ($_SESSION['role'] == 'Admin' || (isset($access['orders']) && $access['orders'] == 1)) {
                ?>
                    <div class="col-lg-8">
                      <!-- Latest pending orders Card -->
                        <div class="card p-3">
                          <div class="card-body">
                            <h1 class="chart-title mb-1">Pending Orders</h1>
                            <p>List of latest pending orders</p><br>
                            <div class="table-responsive">
                              <table class="table table-bordered">
                                  <tr>
                                    <th>SL</th>
                                    <th>Order No(s)</th>
                                    <th>Customer Phone</th>
                                    <th>Invoice No</th>
                                    <th>Products</th>
                                    <th>Total</th>
                                    <th>Order Date</th>
                                    <th colspan="2">Action</th>
                                  </tr>
                                <tbody>
                                  <?php
                                  // Fetch grouped pending orders
                                  $sql = "SELECT 
                                            invoice_no,
                                            GROUP_CONCAT(order_no ORDER BY order_no) AS order_nos,
                                            GROUP_CONCAT(product_title SEPARATOR '<br>') AS products,
                                            SUM(total_price) AS total_price,
                                            user_phone,
                                            MIN(order_date) AS order_date
                                          FROM order_info
                                          WHERE order_status = 'Pending' 
                                            AND order_visibility = 'Show'
                                          GROUP BY invoice_no, user_phone
                                          ORDER BY MIN(order_no) DESC
                                          LIMIT 10";
                        
                                  $result = $conn->query($sql);
                                  $sl = 1;
                        
                                  if ($result->num_rows > 0) {
                                      while($row = $result->fetch_assoc()) {
                                          echo "<tr>
                                                  <td>{$sl}</td>
                                                  <td>{$row['order_nos']}</td>
                                                  <td>{$row['user_phone']}</td>
                                                  <td>{$row['invoice_no']}</td>
                                                  <td>{$row['products']}</td>
                                                  <td>{$row['total_price']} Tk</td>
                                                  <td>" . date('Y-m-d', strtotime($row['order_date'])) . "</td>
                                                  <td>
                                                      <form method='post' action=''>
                                                        <input type='hidden' name='invoice_no' value='{$row['invoice_no']}'>
                                                        <button type='submit' name='accept_invoice' class='btn btn-dark'>Accept</button>
                                                      </form>
                                                    </td>
                                                  <td>
                                                    <a href='removeOrder.php?invoice_no={$row['invoice_no']}'>
                            <button class='btn btn-danger' onclick='return checkDelete(event)'>Declined</button>
                          </a>
                                                  </td>
                                                </tr>";
                                          $sl++;
                                      }
                                  }
                                  ?>
                                </tbody>
                              </table>
                            </div> 
                          </div>  
                          <a href="pendingOrders.php" class="p-3">
                            <button class="btn btn-dark">See All Pending Orders</button>
                          </a>     
                        </div>
                    </div>
                <?php
              }
            ?>

            <?php 
              if ($_SESSION['role'] == 'Admin' || (isset($access['courier']) && $access['courier'] == 1)) {
                ?>
                    <div class="col-lg-4 mt-4 mt-md-0">
                      <!-- Latest parcel Card -->
                      <div class="card p-3">
                        <div class="card-body">
                          <h1 class="chart-title mb-1">Latest Parcel</h1>
                          <p>
                            List of latest parcels
                          </p><br>
                          <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                  <th>Invoice No</th>
                                  <th>Status</th>
                                </tr>
                              <tbody>
                                <?php
                                // Fetch data from parcel_info table
                                $sql = "SELECT parcel_info.* 
                                      FROM parcel_info
                                      JOIN order_info ON parcel_info.invoice_no = order_info.invoice_no
                                      WHERE order_info.order_status != 'Pending' 
                                        AND order_info.order_visibility = 'Show'
                                      GROUP BY order_info.invoice_no
                                      ORDER BY parcel_info.parcel_id DESC 
                                      LIMIT 10
                                      ";

                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {

                                      $status = track_parcel($row['tracking_code']);
     

                                      echo "
                                      <tr>
                                        <td>{$row['invoice_no']}</td>
                                        <td class='text-primary'>{$status}</td>
                                      </tr>";
                                    }
                                }
                                ?>
                              </tbody>
                            </table> 
                          </div> 
                          

                        </div>  
                        <a href="courier.php" class="p-3">
                          <button class="btn btn-dark">See All Parcel</button>
                        </a>     
                      </div>
                    </div>
                  
                <?php
              }
            ?>
            </div>   
            <!-- End -->   

</div>


<!-- Fraud Checker Modal -->
<div class="modal fade" id="fraudModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
    <div class="modal-content shadow-lg border-0 rounded-3">
      
      <!-- Header -->
      <div class="modal-header bg-dark text-white ">
        <h5 class="modal-title fw-bold">🔊 Fraud Checker</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <!-- Body -->
      <div class="modal-body bg-light">

        <!-- Phone Info -->
        <div class="alert alert-secondary py-2 mb-4">
          <strong>Phone:</strong> <span id="fraudPhone" class="fw-bold text-dark"></span>
        </div>

        <!-- Table -->
        <div class="table-responsive mb-4">
          <table class="table table-bordered align-middle text-center mb-0">
            <thead class="table-dark">
              <tr>
                <th>Courier</th>
                <th>Total</th>
                <th class="text-success">Success</th>
                <th class="text-danger">Cancel</th>
              </tr>
            </thead>
            <tbody id="fraudTableBody">
              <!-- Data injected here -->
            </tbody>
          </table>
        </div>

        <!-- Summary Cards -->
        <div class="row g-3">
          <div class="col-12 col-sm-4">
            <div class="card text-center bg-info text-white rounded-3 shadow-sm">
              <div class="card-body p-3">
                <h6 class="mb-1 fw-bold">Total</h6>
                <span class="h5 mb-0" id="fraudTotal">0</span>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-4">
            <div class="card text-center bg-success text-white rounded-3 shadow-sm">
              <div class="card-body p-3">
                <h6 class="mb-1 fw-bold">Success</h6>
                <span class="h5 mb-0" id="fraudSuccess">0</span>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-4">
            <div class="card text-center bg-danger text-white rounded-3 shadow-sm">
              <div class="card-body p-3">
                <h6 class="mb-1 fw-bold">Cancel</h6>
                <span class="h5 mb-0" id="fraudCancel">0</span>
              </div>
            </div>
          </div>
        </div>

      </div> <!-- end modal-body -->

    </div>
  </div>
</div>

<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  // Sales Trend Chart
  const salesTrendCtx = document.getElementById('salesTrendChart');
  if (salesTrendCtx) {
    const salesTrendData = <?php echo json_encode($dailySalesData); ?>;
    
    new Chart(salesTrendCtx, {
      type: 'line',
      data: {
        labels: salesTrendData.map(item => item.date),
        datasets: [
          {
            label: 'Revenue (৳)',
            data: salesTrendData.map(item => item.revenue),
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            yAxisID: 'y'
          },
          {
            label: 'Orders',
            data: salesTrendData.map(item => item.orders),
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            yAxisID: 'y1'
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        interaction: {
          mode: 'index',
          intersect: false,
        },
        plugins: {
          legend: {
            position: 'top',
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                let label = context.dataset.label || '';
                if (label) {
                  label += ': ';
                }
                if (context.parsed.y !== null) {
                  if (context.dataset.label === 'Revenue (৳)') {
                    label += '৳' + context.parsed.y.toLocaleString();
                  } else {
                    label += context.parsed.y;
                  }
                }
                return label;
              }
            }
          }
        },
        scales: {
          y: {
            type: 'linear',
            display: true,
            position: 'left',
            title: {
              display: true,
              text: 'Revenue (৳)'
            }
          },
          y1: {
            type: 'linear',
            display: true,
            position: 'right',
            title: {
              display: true,
              text: 'Orders'
            },
            grid: {
              drawOnChartArea: false,
            },
          },
        }
      }
    });
  }

  // Payment Methods Chart
  const paymentMethodCtx = document.getElementById('paymentMethodChart');
  if (paymentMethodCtx) {
    const paymentMethodData = <?php echo json_encode($paymentMethodsData); ?>;
    
    new Chart(paymentMethodCtx, {
      type: 'doughnut',
      data: {
        labels: paymentMethodData.map(item => item.name),
        datasets: [{
          data: paymentMethodData.map(item => item.amount),
          backgroundColor: [
            'rgba(59, 130, 246, 0.9)',
            'rgba(16, 185, 129, 0.9)',
            'rgba(245, 158, 11, 0.9)',
            'rgba(239, 68, 68, 0.9)',
            'rgba(139, 92, 246, 0.9)'
          ],
          borderColor: '#fff',
          borderWidth: 3,
          hoverOffset: 15
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        cutout: '65%',
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              padding: 15,
              font: {
                size: 13,
                weight: '600'
              },
              usePointStyle: true,
              pointStyle: 'circle'
            }
          },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            padding: 12,
            titleFont: { size: 14, weight: 'bold' },
            bodyFont: { size: 13 },
            callbacks: {
              label: function(context) {
                const value = context.parsed;
                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                const percentage = ((value / total) * 100).toFixed(1);
                return context.label + ': ৳' + value.toLocaleString() + ' (' + percentage + '%)';
              }
            }
          }
        }
      }
    });
  }

  // Top Products Chart
  const topProductsCtx = document.getElementById('topProductsChart');
  if (topProductsCtx) {
    const topProductsData = <?php echo json_encode($topProductsData); ?>;
    
    new Chart(topProductsCtx, {
      type: 'bar',
      data: {
        labels: topProductsData.map(item => item.name.length > 20 ? item.name.substring(0, 20) + '...' : item.name),
        datasets: [{
          label: 'Sales (৳)',
          data: topProductsData.map(item => item.sales),
          backgroundColor: '#8b5cf6',
          borderColor: '#7c3aed',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                return 'Sales: ৳' + context.parsed.y.toLocaleString();
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return '৳' + value.toLocaleString();
              }
            }
          }
        }
      }
    });
  }

  // City-wise Sales Chart
  const cityWiseCtx = document.getElementById('cityWiseChart');
  if (cityWiseCtx) {
    const cityWiseData = <?php echo json_encode($cityWiseData); ?>;
    
    new Chart(cityWiseCtx, {
      type: 'bar',
      data: {
        labels: cityWiseData.map(item => item.city),
        datasets: [{
          label: 'Revenue (৳)',
          data: cityWiseData.map(item => item.revenue),
          backgroundColor: '#f59e0b',
          borderColor: '#d97706',
          borderWidth: 1
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                return 'Revenue: ৳' + context.parsed.x.toLocaleString();
              }
            }
          }
        },
        scales: {
          x: {
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return '৳' + value.toLocaleString();
              }
            }
          }
        }
      }
    });
  }
</script>

<script>
      function checkDelete(event) {
        event.preventDefault(); // Prevent the default action of the button
        Swal.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, decline it!'
        }).then((result) => {
          if (result.isConfirmed) {
            // Redirect to the removeOrder.php page
            window.location.href = event.target.closest('a').href;
          }
        });
        return false; // Prevent the default form submission
      }
</script>

<script>
  function checkFraud(phone) {
      // Reset modal
      document.getElementById("fraudPhone").textContent = phone;
      document.getElementById("fraudTableBody").innerHTML = "<tr><td colspan='4' class='text-center'>Loading...</td></tr>";
      document.getElementById("fraudTotal").textContent = "Loading...";
      document.getElementById("fraudSuccess").textContent = "Loading...";
      document.getElementById("fraudCancel").textContent = "Loading...";

      // Show modal
      var fraudModal = new bootstrap.Modal(document.getElementById('fraudModal'));
      fraudModal.show();

      // Prepare form data
      const formData = new FormData();
      formData.append('phone', phone);

      fetch('fraud_api.php', {
          method: 'POST',
          body: formData
      })
      .then(res => res.json())
      .then(data => {
          // Handle API errors
          if (data.error) {
              document.getElementById("fraudTableBody").innerHTML = `<tr><td colspan='4' class='text-danger'>${data.error}</td></tr>`;
              document.getElementById("fraudTotal").textContent = "0";
              document.getElementById("fraudSuccess").textContent = "0";
              document.getElementById("fraudCancel").textContent = "0";
              return;
          }

          // Update summary cards
          document.getElementById("fraudTotal").textContent = data.total_parcels ?? 0;
          document.getElementById("fraudSuccess").textContent = data.total_delivered ?? 0;
          document.getElementById("fraudCancel").textContent = data.total_cancel ?? 0;

          // Define fixed courier list
          const couriers = ["Pathao", "Steadfast", "Redx", "PaperFly"];
          let tbody = '';

          // Update table
          document.getElementById("fraudTableBody").innerHTML = tbody;
      })
      .catch(err => {
          document.getElementById("fraudTableBody").innerHTML = `<tr><td colspan='4' class='text-danger'>Error: ${err}</td></tr>`;
          document.getElementById("fraudTotal").textContent = "0";
          document.getElementById("fraudSuccess").textContent = "0";
          document.getElementById("fraudCancel").textContent = "0";
      });
  }
</script>
<?php require 'footer.php'; ?>