<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
// database connection
include('database/dbConnection.php');

$admin_username = $_SESSION['admin'];

// Get admin's role_id
$sql = "SELECT role_id FROM admin_info WHERE admin_username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $admin_username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();
    $role_id = $admin['role_id'];
} else {
    die("Invalid admin session.");
}

// Get access rights from page_access
$access_sql = "SELECT * FROM page_access WHERE role_id = ?";
$access_stmt = $conn->prepare($access_sql);
$access_stmt->bind_param("i", $role_id);
$access_stmt->execute();
$access_result = $access_stmt->get_result();

if ($access_result->num_rows === 1) {
    $access = $access_result->fetch_assoc();
} else {
    die("Access rights not set for this role.");
}

// Mapping pages to column names
$pageAccessMap = [
    'index.php'               => 'dashboard',

    'addProduct.php'          => 'product',
    'viewProduct.php'         => 'product',
    'sizes.php'               => 'product',
 
    'insertCategory.php'      => 'categories',
    'viewCategory.php'        => 'categories',
    'deleteCategory.php'      => 'categories',

    'slider.php'              => 'slider',
    'banner.php'              => 'banner',
    'discounts.php'           => 'discounts',

    'add-coupons.php'         => 'coupons',
    'view-coupons.php'        => 'coupons',

    'viewCustomers.php'       => 'customers',

    'pendingOrders.php'       => 'orders',
    'viewOrders.php'          => 'orders',

    'viewPayments.php'        => 'payments',

    'accounts-dashboard.php'  => 'accounts',
    'total-collections.php'   => 'accounts',
    'expense-category.php'    => 'accounts',
    'add-expense.php'         => 'accounts',
    'all-expenses.php'        => 'accounts',
    'statements.php'          => 'accounts',
    'view-statement.php'      => 'accounts',

    'inventory.php'           => 'inventory',

    'makeInvoice.php'         => 'invoice',
    'invoice.php'             => 'invoice',

    'courier.php'             => 'courier',
    'steadfast_entry.php'     => 'courier',
    'order_details.php'       => 'courier',

    'purchaseHistory.php'     => 'history',
    'settings.php'            => 'settings'
];

// Get the current page filename
$current_page = basename($_SERVER['PHP_SELF']);

// Check if the current page is restricted
if (isset($pageAccessMap[$current_page])) {
    $page_key = $pageAccessMap[$current_page];

    // If the page access is not allowed
    if (!isset($access[$page_key]) || $access[$page_key] == 0) {
        echo "<script>alert('Access Denied to this page.'); window.location.href = 'index.php';</script>";
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin - <?= $page_title; ?></title>

    <!-- plugins:css -->
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/ti-icons/css/themify-icons.css">
    <!-- Layout styles -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="assets/images/favicon.png" />

    <!-- Custom CSS-->
    <link rel="stylesheet" href="css/form.css">
    <link rel="stylesheet" href="css/style.css">

    <style>
      #success-box {
        max-width: 800px;
        margin: auto;
        text-align: center;
        font-size: 18px;
        padding: 20px;
        color: #0A3622;
        background: #D1E7DD;
      }

      /* Add Role Page */
      .form-check {
        min-width: 150px;
      }
      .form-check .form-check-label {
          margin-left: 0;
      }

      /* Add Product Page */
      .img-upload-box {
        border: 1px dashed #ccc; 
        padding: 0 20px; 
        padding-top: 10px; 
        border-radius: 10px; 
        margin-bottom: 20px;
      }

      .custum-file-upload {
        border: 2px dashed #ccc;
        padding: 25px;
        max-width: 320px;
        width: 100%;
        text-align: center;
        cursor: pointer;
        border-radius: 12px;
        transition: 0.3s ease;
        display: inline-block;
        background: #f9f9f9;
        margin-bottom: 20px;
      }

      .custum-file-upload:hover {
        background-color: #eef4ff;
        border-color: #007bff;
        color: #007bff;
      }

      .custum-file-upload .icon {
        margin-bottom: 10px;
        color: #666;
      }

      .custum-file-upload .icon svg {
        width: 40px;
        height: 40px;
        fill: currentColor;
      }

      .custum-file-upload .text span {
        font-size: 15px;
        font-weight: 500;
        color: #333;
      }

      .custum-file-upload input[type="file"] {
        display: none;
      }

      .image-block {
        margin-bottom: 30px;
      }

      .image-block h4 {
        font-size: 14px;
        color: #888;
        margin-top: 5px;
      }

      .details {
        font-weight: bold;
        display: block;
        margin-bottom: 5px;
        font-size: 16px;
      }
    </style>

  </head>
  <body>
    <div class="container-scroller">
      <!-- partial:partials/_navbar.php -->
      <?php include('navbar.php'); ?>
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        
        <!-- partial:partials/_sidebar.php -->
        <?php include('sidebar.php'); ?>
        <div class="main-panel">