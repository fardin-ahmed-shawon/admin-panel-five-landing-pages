<?php
$current_page = basename($_SERVER['PHP_SELF']);
$page_title = 'Order Management';
?>
<?php require 'header.php'; ?>

<!-- SweetAlert2 CSS & JS - REMOVED, NO LONGER NEEDED -->
<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script> -->

<?php
// --------------------
// Fetch main categories (for the new dropdown filter)
// --------------------
$main_categories = [];
$mc_sql = "SELECT main_ctg_id, main_ctg_name FROM main_category ORDER BY main_ctg_name";
if ($mc_result = $conn->query($mc_sql)) {
    while ($mc_row = $mc_result->fetch_assoc()) {
        $main_categories[] = $mc_row;
    }
}

// --------------------
// POST handlers (existing behaviour kept intact)
// --------------------

// Update order status to "Processing" if the Mark As Processing button is pressed
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["mark_processing"])) {
  $invoice_no = $_POST["invoice_no"];
  $sql_update = "UPDATE order_info SET order_status = 'Processing' WHERE invoice_no = '$invoice_no'";
  if ($conn->query($sql_update) === TRUE) {
      $msg = "Order status updated successfully";
  } else {
      $msg = "Error updating record: " . $conn->error;
  }
}

// Update order status to "Shipped" if the Mark As Shipped button is pressed
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["mark_shipped"])) {
  $invoice_no = $_POST["invoice_no"];
  $sql_update = "UPDATE order_info SET order_status = 'Shipped' WHERE invoice_no = '$invoice_no'";
  if ($conn->query($sql_update) === TRUE) {
      $msg = "Order status updated successfully";
  } else {
      $msg = "Error updating record: " . $conn->error;
  }
}

// Update order status to "Completed" if the Mark As Completed button is pressed
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["mark_completed"])) {
  $invoice_no = $_POST["invoice_no"];
  $sql_update = "UPDATE order_info SET order_status = 'Completed' WHERE invoice_no = '$invoice_no'";
  if ($conn->query($sql_update) === TRUE) {
      $msg = "Order status updated successfully";
  } else {
      $msg = "Error updating record: " . $conn->error;
  }
}

// Update order status to "Canceled" if the Mark As Canceled button is pressed
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["mark_canceled"])) {
  $invoice_no = $_POST["invoice_no"];
  $sql_update = "UPDATE order_info SET order_status = 'Canceled' WHERE invoice_no = '$invoice_no'";
  if ($conn->query($sql_update) === TRUE) {
      $msg = "Order status updated successfully";
  } else {
      $msg = "Error updating record: " . $conn->error;
  }
}

//------------------- For order_info & payment_info table both -----------------------------

// Update order status to "Processing" if the Mark As Processing button is pressed
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["mark_processing_both"])) {
  $invoice_no = $_POST["invoice_no"];
  $sql_update = "UPDATE order_info, payment_info SET order_info.order_status = 'Processing', payment_info.order_status = 'Processing' WHERE order_info.invoice_no = '$invoice_no' AND payment_info.invoice_no = '$invoice_no'";
  if ($conn->query($sql_update) === TRUE) {
      $msg = "Order status updated successfully";
  } else {
      $msg = "Error updating record: " . $conn->error;
  }
}

// Update order status to "Shipped" if the Mark As Shipped button is pressed
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["mark_shipped_both"])) {
  $invoice_no = $_POST["invoice_no"];
  $sql_update = "UPDATE order_info, payment_info SET order_info.order_status = 'Shipped', payment_info.order_status = 'Shipped' WHERE order_info.invoice_no = '$invoice_no' AND payment_info.invoice_no = '$invoice_no'";
  if ($conn->query($sql_update) === TRUE) {
      $msg = "Order status updated successfully";
  } else {
      $msg = "Error updating record: " . $conn->error;
  }
}

// Update order status to "Completed" if the Mark As Completed button is pressed
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["mark_completed_both"])) {
  $invoice_no = $_POST["invoice_no"];
  $sql_update = "UPDATE order_info, payment_info SET order_info.order_status = 'Completed', payment_info.order_status = 'Completed' WHERE order_info.invoice_no = '$invoice_no' AND payment_info.invoice_no = '$invoice_no'";
  if ($conn->query($sql_update) === TRUE) {
      $msg = "Order status updated successfully";
  } else {
      $msg = "Error updating record: " . $conn->error;
  }
}

// Update order status to "Canceled" if the Mark As Canceled button is pressed
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["mark_canceled_both"])) {
  $invoice_no = $_POST["invoice_no"];
  $sql_update = "UPDATE order_info, payment_info SET order_info.order_status = 'Canceled', payment_info.order_status = 'Canceled' WHERE order_info.invoice_no = '$invoice_no' AND payment_info.invoice_no = '$invoice_no'";
  if ($conn->query($sql_update) === TRUE) {
      $msg = "Order status updated successfully";
  } else {
      $msg = "Error updating record: " . $conn->error;
  }
}


// --------------------
// Bulk Actions
// --------------------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['selected_invoices'])) {
  $selected_invoices = $_POST['selected_invoices'];
  $inv_list = implode(",", $selected_invoices);

  if (isset($_POST['download_selected'])) {
      echo "<script>window.location.href = 'all-invoice.php?invoice=" . urlencode($inv_list) . "';</script>";
      exit;
  }

  if (isset($_POST['print_labels'])) {
      echo "<script>window.location.href = 'generate_label.php?invoice_no=" . urlencode($inv_list) . "';</script>";
      exit;
  }
}
// END


// --- Search and Filter Logic ---
$search_query = isset($_GET['search_query']) ? $conn->real_escape_string($_GET['search_query']) : '';
$from_date = isset($_GET['from_date']) ? $conn->real_escape_string($_GET['from_date']) : '';
$to_date = isset($_GET['to_date']) ? $conn->real_escape_string($_GET['to_date']) : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$main_ctg = isset($_GET['main_ctg']) ? intval($_GET['main_ctg']) : 0;

// For Cash On Delivery
$where_cod = "WHERE payment_method = 'Cash On Delivery' AND order_status != 'Pending'";
if ($search_query) {
    $where_cod .= " AND (invoice_no LIKE '%$search_query%' OR user_phone LIKE '%$search_query%')";
}
if ($from_date && $to_date) {
    $where_cod .= " AND order_date BETWEEN '$from_date' AND '$to_date'";
}
if ($filter == 'Processing') {
    $where_cod .= " AND order_status = 'Processing'";
} elseif ($filter == 'Shipped') {
    $where_cod .= " AND order_status = 'Shipped'";
} elseif ($filter == 'Canceled') {
    $where_cod .= " AND order_status = 'Canceled'";
} elseif ($filter == 'Completed') {
    $where_cod .= " AND order_status = 'Completed'";
} elseif ($filter == 'SendToSteadfast') {
    $where_cod .= " AND invoice_no IN (SELECT invoice_no FROM parcel_info WHERE tracking_code IS NULL)";
}

if ($main_ctg) {
    $where_cod .= " AND invoice_no IN (SELECT DISTINCT o.invoice_no FROM order_info o JOIN product_info p ON o.product_id = p.product_id WHERE p.main_ctg_id = $main_ctg)";
}

// For Mobile Banking
$where_mb = "WHERE order_visibility = 'Show'";
if ($search_query) {
    $where_mb .= " AND (invoice_no LIKE '%$search_query%' OR invoice_no IN (SELECT invoice_no FROM order_info WHERE user_phone LIKE '%$search_query%'))";
}
if ($from_date && $to_date) {
    $where_mb .= " AND payment_date BETWEEN '$from_date' AND '$to_date'";
}
if ($filter == 'Processing') {
    $where_mb .= " AND order_status = 'Processing'";
} elseif ($filter == 'Shipped') {
    $where_mb .= " AND order_status = 'Shipped'";
} elseif ($filter == 'Canceled') {
    $where_mb .= " AND order_status = 'Canceled'";
} elseif ($filter == 'Completed') {
    $where_mb .= " AND order_status = 'Completed'";
} elseif ($filter == 'SendToSteadfast') {
    $where_mb .= " AND invoice_no IN (SELECT invoice_no FROM parcel_info WHERE tracking_code IS NULL)";
}

if ($main_ctg) {
    $where_mb .= " AND invoice_no IN (SELECT DISTINCT o.invoice_no FROM order_info o JOIN product_info p ON o.product_id = p.product_id WHERE p.main_ctg_id = $main_ctg)";
}

?>

<style>
    /* Modern Color Palette */
    :root {
        --primary-color: #6366f1;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --info-color: #3b82f6;
        --dark-color: #1f2937;
        --light-bg: #f9fafb;
        --border-color: #e5e7eb;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .enhanced-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, #8b5cf6 100%);
        border-radius: 16px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-lg);
    }

    .enhanced-header h2 {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .enhanced-header p {
        margin: 0.5rem 0 0 0;
        opacity: 0.9;
    }

    .filter-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: var(--shadow-md);
        margin-bottom: 2rem;
    }

    .filter-section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .enhanced-input {
        border: 2px solid var(--border-color);
        border-radius: 10px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .enhanced-input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        outline: none;
    }

    .status-filters {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-top: 1.5rem;
    }

    .status-pill {
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        border: 2px solid transparent;
        font-weight: 600;
        transition: all 0.3s ease;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .status-pill:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .status-pill.active {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .action-bar {
        background: white;
        border-radius: 12px;
        padding: 1rem 1.5rem;
        box-shadow: var(--shadow-sm);
        margin-bottom: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .bulk-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .order-section {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: var(--shadow-md);
        margin-bottom: 2rem;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--border-color);
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark-color);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .payment-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .badge-cod {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-online {
        background: #dbeafe;
        color: #1e40af;
    }

    .enhanced-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .enhanced-table thead th {
        background: var(--light-bg);
        color: var(--dark-color);
        font-weight: 600;
        text-align: left;
        padding: 1rem;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid var(--border-color);
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .enhanced-table tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid var(--border-color);
    }

    .enhanced-table tbody tr:hover {
        transform: scale(1.01);
        box-shadow: var(--shadow-sm);
    }

    .enhanced-table tbody td {
        padding: 1rem;
        vertical-align: middle;
    }

    .status-dropdown {
        padding: 0.65rem 2.75rem 0.65rem 1.25rem;
        border-radius: 8px;
        border: 2px solid rgba(0, 0, 0, 0.15);
        font-size: 0.9rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        appearance: none;
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        min-width: 140px;
        text-align: center;
    }

    .status-dropdown[value="Shipped"] {
        background-color: #10b981;
        color: white;
        border-color: #059669;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' fill='white' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
    }

    .status-dropdown[value="Completed"] {
        background-color: #3b82f6;
        color: white;
        border-color: #2563eb;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' fill='white' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
    }

    .status-dropdown[value="Canceled"] {
        background-color: #ef4444;
        color: white;
        border-color: #dc2626;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' fill='white' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
    }

    .status-dropdown:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        border-width: 2px;
    }

    .status-dropdown:focus {
        outline: none;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.3);
    }

    .status-dropdown option {
        padding: 0.75rem;
        background: white;
        color: var(--dark-color);
        font-weight: 600;
    }

    .order-row-shipped {
        background-color: #f0fdf4 !important;
        border-left: 4px solid #10b981;
    }

    .order-row-completed {
        background-color: #eff6ff !important;
        border-left: 4px solid #3b82f6;
    }

    .order-row-canceled {
        background-color: #fef2f2 !important;
        border-left: 4px solid #ef4444;
    }

    .order-row-pending {
        background-color: #fffbeb !important;
        border-left: 4px solid #f59e0b;
    }

    .order-row-shipped:hover {
        background-color: #dcfce7 !important;
    }

    .order-row-completed:hover {
        background-color: #dbeafe !important;
    }

    .order-row-canceled:hover {
        background-color: #fee2e2 !important;
    }

    .order-row-pending:hover {
        background-color: #fef3c7 !important;
    }

    .btn-modern {
        padding: 0.6rem 1.2rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        white-space: nowrap;
    }

    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-modern:active {
        transform: translateY(0);
    }

    .btn-primary-modern {
        background: var(--primary-color);
        color: white;
    }

    .btn-success-modern {
        background: var(--success-color);
        color: white;
    }

    .btn-info-modern {
        background: var(--info-color);
        color: white;
    }

    .btn-warning-modern {
        background: var(--warning-color);
        color: white;
    }

    .btn-danger-modern {
        background: var(--danger-color);
        color: white;
    }

    .btn-dark-modern {
        background: var(--dark-color);
        color: white;
    }

    .btn-outline-modern {
        background: transparent;
        border: 2px solid var(--border-color);
        color: var(--dark-color);
    }

    .btn-sm-modern {
        padding: 0.4rem 0.8rem;
        font-size: 0.8rem;
    }

    .custom-checkbox {
        width: 20px;
        height: 20px;
        cursor: pointer;
        accent-color: var(--primary-color);
    }

    .table-container {
        overflow-x: auto;
        border-radius: 12px;
        max-height: 500px;
        overflow-y: auto;
    }

    .table-container::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .table-container::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }

    .table-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .table-container::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .spinner {
        width: 50px;
        height: 50px;
        border: 4px solid rgba(255, 255, 255, 0.3);
        border-top-color: white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .toast-message {
        position: fixed;
        top: 20px;
        right: 20px;
        background: var(--success-color);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        box-shadow: var(--shadow-lg);
        display: none;
        align-items: center;
        gap: 0.75rem;
        z-index: 10000;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .animated-popup {
        animation: zoomIn 0.3s ease;
    }

    @keyframes zoomIn {
        from {
            transform: scale(0.8);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    .pulse-button {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }

    @media (max-width: 768px) {
        .enhanced-header h2 {
            font-size: 1.5rem;
        }
        
        .action-bar {
            flex-direction: column;
            align-items: stretch;
        }
        
        .bulk-actions {
            width: 100%;
            justify-content: stretch;
        }
        
        .bulk-actions button {
            flex: 1;
        }
        
        .status-filters {
            flex-direction: column;
        }
        
        .status-pill {
            justify-content: center;
        }
    }

    @media print {
        .filter-card,
        .action-bar,
        .btn-modern {
            display: none !important;
        }
    }
</style>

<div class="content-wrapper">
    <div class="enhanced-header">
        <h2>
            <i class="mdi mdi-package-variant"></i>
            Order Management System
        </h2>
        <p>Manage and track all your orders efficiently</p>
    </div>

    <div class="action-bar">
        <div class="bulk-actions">
            <button type="button" class="btn-modern btn-dark-modern" onclick="window.location.href='all-invoice.php'">
                <i class="mdi mdi-printer"></i> Print All Invoices
            </button>
            <button type="button" class="btn-modern btn-primary-modern" onclick="window.location.href='generate_label.php?invoice_no=all'">
                <i class="mdi mdi-label-multiple"></i> Print All Labels
            </button>
        </div>
    </div>

    <div class="filter-card">
        <div class="filter-section-title">
            <i class="mdi mdi-filter-variant"></i>
            Search & Filter Orders
        </div>
        
        <form method="get" id="filterForm">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 600; color: #6b7280;">Search</label>
                    <input type="text" name="search_query" class="form-control enhanced-input" placeholder="Invoice No or Phone" value="<?php echo isset($_GET['search_query']) ? htmlspecialchars($_GET['search_query']) : ''; ?>">
                </div>
                
                <div class="col-md-2">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 600; color: #6b7280;">From Date</label>
                    <input type="date" name="from_date" class="form-control enhanced-input" value="<?php echo isset($_GET['from_date']) ? htmlspecialchars($_GET['from_date']) : ''; ?>">
                </div>
                
                <div class="col-md-2">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 600; color: #6b7280;">To Date</label>
                    <input type="date" name="to_date" class="form-control enhanced-input" value="<?php echo isset($_GET['to_date']) ? htmlspecialchars($_GET['to_date']) : ''; ?>">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label" style="font-size: 0.85rem; font-weight: 600; color: #6b7280;">Category</label>
                    <select name="main_ctg" class="form-control enhanced-input">
                        <option value="">All Categories</option>
                        <?php foreach ($main_categories as $mc) : ?>
                            <option value="<?php echo $mc['main_ctg_id']; ?>" <?php echo ($main_ctg == $mc['main_ctg_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($mc['main_ctg_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn-modern btn-primary-modern flex-fill">
                        <i class="mdi mdi-magnify"></i> Search
                    </button>
                    <a href="makeInvoice.php" class="btn-modern btn-outline-modern">
                        <i class="mdi mdi-refresh"></i>
                    </a>
                </div>
            </div>

            <div class="status-filters">
                <button type="submit" name="filter" value="all" class="status-pill <?php echo $filter == 'all' ? 'active' : ''; ?>" style="background: #f3f4f6; color: #374151; border-color: #d1d5db;">
                    <i class="mdi mdi-view-grid"></i> All Orders
                </button>
                <button type="submit" name="filter" value="Processing" class="status-pill <?php echo $filter == 'Processing' ? 'active' : ''; ?>" style="background: #fff3cd; color: #856404; border-color: #ceab43ff;">
                <i class="mdi mdi-progress-clock"></i> Processing
                </button>

                <button type="submit" name="filter" value="Shipped" class="status-pill <?php echo $filter == 'Shipped' ? 'active' : ''; ?>" style="background: #d1fae5; color: #065f46; border-color: #10b981;">
                    <i class="mdi mdi-truck-delivery"></i> Shipped
                </button>
                <button type="submit" name="filter" value="Completed" class="status-pill <?php echo $filter == 'Completed' ? 'active' : ''; ?>" style="background: #dbeafe; color: #1e40af; border-color: #3b82f6;">
                    <i class="mdi mdi-check-circle"></i> Completed
                </button>
                <button type="submit" name="filter" value="Canceled" class="status-pill <?php echo $filter == 'Canceled' ? 'active' : ''; ?>" style="background: #fee2e2; color: #991b1b; border-color: #ef4444;">
                    <i class="mdi mdi-close-circle"></i> Canceled
                </button>
                <button type="submit" name="filter" value="SendToSteadfast" class="status-pill <?php echo $filter == 'SendToSteadfast' ? 'active' : ''; ?>" style="background: #e0e7ff; color: #3730a3; border-color: #6366f1;">
                    <i class="mdi mdi-send"></i> Ready to Ship
                </button>
            </div>
        </form>
    </div>

    <form method="post" id="bulkActionForm">

        <div class="order-section">
            <div class="section-header">
                <div class="section-title">
                    <i class="mdi mdi-cash"></i>
                    Cash On Delivery Orders
                </div>
                <div class="payment-badge badge-cod">
                    <i class="mdi mdi-cash-multiple"></i>
                    COD
                </div>
            </div>

            <div class="table-container">
                <table class="enhanced-table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAllCod" class="custom-checkbox"></th>
                            <th>#</th>
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th>Order No</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Courier</th>
                            <th>Invoice</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="CODTable">
                        <?php
                        $sql = "SELECT invoice_no, GROUP_CONCAT(order_no ORDER BY order_no SEPARATOR ', ') as order_no, user_phone, order_status, order_visibility, payment_method
                                FROM order_info
                                $where_cod
                                GROUP BY invoice_no, order_status, payment_method";

                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            $count = 1;
                            while($row = $result->fetch_assoc()) {
                                if ($row["payment_method"] == "Cash On Delivery" && $row["order_status"] != "Pending" && $row["order_visibility"] == "Show") {
                                    $invoice_no = $row['invoice_no'];
                                    $sql2 = "SELECT tracking_code FROM parcel_info WHERE invoice_no = '$invoice_no'";
                                    $result2 = $conn->query($sql2);
                                    $is_tracking_code_set = ($result2->num_rows > 0) ? 1 : 0;

                                    $status_class = '';
                                    $status_color = '';
                                    if ($row["order_status"] == 'Processing') {
                                        $status_class = 'status-processing';
                                        $status_color = 'processing';
                                    }
                                    if ($row["order_status"] == 'Shipped') {
                                        $status_class = 'status-shipped';
                                        $status_color = 'shipped';
                                    } elseif ($row["order_status"] == 'Completed') {
                                        $status_class = 'status-completed';
                                        $status_color = 'completed';
                                    } elseif ($row["order_status"] == 'Canceled') {
                                        $status_class = 'status-canceled';
                                        $status_color = 'canceled';
                                    } else {
                                        $status_class = 'status-pending';
                                        $status_color = 'pending';
                                    }

                                    echo '<tr class="order-row-' . $status_color . '">';
                                    echo '<td><input type="checkbox" name="selected_invoices[]" value="' . $row["invoice_no"] . '" class="custom-checkbox"></td>';
                                    echo '<td><strong>' . $count . '</strong></td>';
                                    echo '<td><strong>' . $row["invoice_no"] . '</strong></td>';
                                    echo '<td><i class="mdi mdi-phone"></i> ' . $row["user_phone"] . '</td>';
                                    echo '<td>' . $row["order_no"] . '</td>';
                                    echo '<td><strong>৳' . calculate_order_amount($row["invoice_no"]) . '</strong></td>';
                                    echo '<td>';
                                    echo '<select class="status-dropdown" data-invoice="' . $row["invoice_no"] . '" data-payment="cod" onchange="changeOrderStatus(this)">';
                                    echo '<option value="Processing" ' . ($row["order_status"] == 'Processing' ? 'selected' : '') . '>Processing</option>';
                                    echo '<option value="Shipped" ' . ($row["order_status"] == 'Shipped' ? 'selected' : '') . '>Shipped</option>';
                                    echo '<option value="Completed" ' . ($row["order_status"] == 'Completed' ? 'selected' : '') . '>Completed</option>';
                                    echo '<option value="Canceled" ' . ($row["order_status"] == 'Canceled' ? 'selected' : '') . '>Canceled</option>';
                                    echo '</select>';
                                    echo '</td>';
                                    echo '<td>' . find_order_date($row["invoice_no"]) . '</td>';
                                    
                                    if ($is_tracking_code_set == 0) {
                                        echo '<td><button type="button" onclick="window.location.href=\'steadfast_entry.php?invoice_no='. $row["invoice_no"] .'\'" class="btn-modern btn-primary-modern btn-sm-modern"><i class="mdi mdi-send"></i> Send</button></td>';
                                    } else {
                                        echo '<td><span class="btn-modern btn-success-modern btn-sm-modern" style="cursor: default;"><i class="mdi mdi-check"></i> Sent</span></td>';
                                    }

                                    echo '<td>';
                                    echo '<div class="btn-group gap-2" role="group">';
                                    echo '<button type="button" class="btn-modern btn-sm-modern btn-dark-modern" onclick="window.location.href=\'invoice.php?inv='.$row['invoice_no'].'\'" title="Large Invoice"><i class="mdi mdi-file-document"></i></button>';
                                    echo '<button type="button" class="btn-modern btn-sm-modern btn-info-modern" onclick="window.location.href=\'pos-invoice.php?inv='.$row['invoice_no'].'\'" title="POS Invoice"><i class="mdi mdi-receipt"></i></button>';
                                    echo '<button type="button" class="btn-modern btn-sm-modern btn-primary-modern" onclick="window.open(\'generate_label.php?invoice_no='.$row['invoice_no'].'\', \'_blank\')" title="Label"><i class="mdi mdi-label"></i></button>';
                                    echo '</div>';
                                    echo '</td>';

                                    echo '<td>';
                                    echo '<div class="btn-group gap-2" role="group">';
                                    echo '<button type="button" class="btn-modern btn-sm-modern btn-success-modern" onclick="window.location.href=\'order_details.php?invoice_no='.$row['invoice_no'].'\'" title="View Details"><i class="mdi mdi-eye"></i></button>';
                                    echo '<button type="button" class="btn-modern btn-sm-modern btn-warning-modern" onclick="window.location.href=\'edit-order.php?invoice_no='.$row['invoice_no'].'\'" title="Edit Details"><i class="mdi mdi-text-box-edit-outline"></i></button>';
                                    echo '</div>';
                                    echo '</td>';

                                    echo '</tr>';
                                    $count++;
                                }
                            }
                        } else {
                            echo '<tr><td colspan="11" style="text-align: center; padding: 3rem; color: #9ca3af;"><i class="mdi mdi-package-variant" style="font-size: 3rem; display: block; margin-bottom: 1rem;"></i>No orders found</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="order-section">
            <div class="section-header">
                <div class="section-title">
                    <i class="mdi mdi-cellphone"></i>
                    Mobile Banking Orders
                </div>
                <div class="payment-badge badge-online">
                    <i class="mdi mdi-credit-card"></i>
                    Online Payment
                </div>
            </div>

            <div class="table-container">
                <table class="enhanced-table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAllMb" class="custom-checkbox"></th>
                            <th>#</th>
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th>Order No</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Courier</th>
                            <th>Invoice</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="MbTable">
                        <?php
                        $sql = "SELECT invoice_no, 
                                GROUP_CONCAT(CASE 
                                            WHEN order_status != 'Pending' AND order_visibility = 'Show' 
                                            THEN order_no 
                                            END SEPARATOR ', ') as order_no, 
                                serial_no, 
                                order_status, 
                                order_visibility, 
                                payment_method, 
                                acc_number, 
                                transaction_id, 
                                payment_date, 
                                payment_status 
                                FROM payment_info 
                                $where_mb
                                GROUP BY invoice_no";
                        $result = $conn->query($sql);

                        $count = 1;
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                if ($row["payment_status"] == "Paid") {
                                    $invoice_no = $row['invoice_no'];
                                    $sql2 = "SELECT tracking_code FROM parcel_info WHERE invoice_no = '$invoice_no'";
                                    $result2 = $conn->query($sql2);
                                    $is_tracking_code_set = ($result2->num_rows > 0) ? 1 : 0;

                                    $status_class = '';
                                    $status_color = '';
                                    if ($row["order_status"] == 'Shipped') {
                                        $status_class = 'status-shipped';
                                        $status_color = 'shipped';
                                    } elseif ($row["order_status"] == 'Completed') {
                                        $status_class = 'status-completed';
                                        $status_color = 'completed';
                                    } elseif ($row["order_status"] == 'Canceled') {
                                        $status_class = 'status-canceled';
                                        $status_color = 'canceled';
                                    } else {
                                        $status_class = 'status-pending';
                                        $status_color = 'pending';
                                    }

                                    echo '<tr class="order-row-' . $status_color . '">';
                                    echo '<td><input type="checkbox" name="selected_invoices[]" value="' . $row["invoice_no"] . '" class="custom-checkbox"></td>';
                                    echo '<td><strong>' . $count . '</strong></td>';
                                    echo '<td><strong>' . $row["invoice_no"] . '</strong></td>';
                                    echo '<td><i class="mdi mdi-phone"></i> ' . find_customer_phone($row["invoice_no"]) . '</td>';
                                    echo '<td>' . $row["order_no"] . '</td>';
                                    echo '<td><strong>৳' . calculate_order_amount($row["invoice_no"]) . '</strong></td>';
                                    echo '<td>';
                                    echo '<select class="status-dropdown" data-invoice="' . $row["invoice_no"] . '" data-payment="mobile" onchange="changeOrderStatus(this)">';
                                    echo '<option value="Processing" ' . ($row["order_status"] == 'Processing' ? 'selected' : '') . '>Processing</option>';
                                    echo '<option value="Shipped" ' . ($row["order_status"] == 'Shipped' ? 'selected' : '') . '>Shipped</option>';
                                    echo '<option value="Completed" ' . ($row["order_status"] == 'Completed' ? 'selected' : '') . '>Completed</option>';
                                    echo '<option value="Canceled" ' . ($row["order_status"] == 'Canceled' ? 'selected' : '') . '>Canceled</option>';
                                    echo '</select>';
                                    echo '</td>';
                                    echo '<td>' . find_order_date($row["invoice_no"]) . '</td>';

                                    if ($is_tracking_code_set == 0) {
                                        echo '<td><button type="button" onclick="window.location.href=\'steadfast_entry.php?invoice_no='. $row["invoice_no"] .'\'" class="btn-modern btn-primary-modern btn-sm-modern"><i class="mdi mdi-send"></i> Send</button></td>';
                                    } else {
                                        echo '<td><span class="btn-modern btn-success-modern btn-sm-modern" style="cursor: default;"><i class="mdi mdi-check"></i> Sent</span></td>';
                                    }

                                    echo '<td>';
                                    echo '<div class="btn-group gap-2" role="group">';
                                    echo '<button type="button" class="btn-modern btn-sm-modern btn-dark-modern" onclick="window.location.href=\'invoice.php?inv='.$row['invoice_no'].'\'" title="Large Invoice"><i class="mdi mdi-file-document"></i></button>';
                                    echo '<button type="button" class="btn-modern btn-sm-modern btn-dark-modern" onclick="window.location.href=\'pos-invoice.php?inv='.$row['invoice_no'].'\'" title="POS Invoice"><i class="mdi mdi-receipt"></i></button>';
                                    echo '<button type="button" class="btn-modern btn-sm-modern btn-primary-modern" onclick="window.open(\'generate_label.php?invoice_no='.$row['invoice_no'].'\', \'_blank\')" title="Label"><i class="mdi mdi-label"></i></button>';
                                    echo '</div>';
                                    echo '</td>';

                                    echo '<td>';
                                    echo '<div class="btn-group gap-2" role="group">';
                                    echo '<button type="button" class="btn-modern btn-sm-modern btn-info-modern" onclick="window.location.href=\'order_details.php?invoice_no='.$row['invoice_no'].'\'" title="View Details"><i class="mdi mdi-eye"></i></button>';
                                    echo '<button type="button" class="btn-modern btn-sm-modern btn-warning-modern" onclick="window.location.href=\'edit-order.php?invoice_no='.$row['invoice_no'].'\'" title="Edit Details"><i class="mdi mdi-text-box-edit-outline"></i></button>';
                                    echo '</div>';
                                    echo '</td>';

                                    echo '</tr>';
                                    $count++;
                                }
                            }
                        } else {
                            echo '<tr><td colspan="11" style="text-align: center; padding: 3rem; color: #9ca3af;"><i class="mdi mdi-package-variant" style="font-size: 3rem; display: block; margin-bottom: 1rem;"></i>No orders found</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="order-section" style="background: linear-gradient(135deg, #f9fafb 0%, #e5e7eb 100%);">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div style="font-weight: 600; color: #374151;">
                    <i class="mdi mdi-checkbox-marked-circle"></i>
                    Selected items will be processed
                </div>
                <div class="bulk-actions">
                    <button type="submit" name="download_selected" class="btn-modern btn-dark-modern">
                        <i class="mdi mdi-printer"></i> Print Selected Invoices
                    </button>
                    <button type="submit" name="print_labels" class="btn-modern btn-primary-modern">
                        <i class="mdi mdi-label-multiple"></i> Print Selected Labels
                    </button>
                </div>
            </div>
        </div>

    </form>

</div>

<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
</div>

<div class="toast-message" id="successToast">
    <i class="mdi mdi-check-circle" style="font-size: 1.5rem;"></i>
    <span id="toastMessage">Order status updated successfully!</span>
</div>

<script>
document.getElementById('selectAllCod').addEventListener('change', function() {
    let checkboxes = document.querySelectorAll('.CODTable input[name="selected_invoices[]"]');
    checkboxes.forEach(cb => cb.checked = this.checked);
});

document.getElementById('selectAllMb').addEventListener('change', function() {
    let checkboxes = document.querySelectorAll('.MbTable input[name="selected_invoices[]"]');
    checkboxes.forEach(cb => cb.checked = this.checked);
});

document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        if (this.id !== 'filterForm') {
            document.getElementById('loadingOverlay').style.display = 'flex';
        }
    });
});

<?php if (isset($msg)): ?>
    showToast('<?php echo $msg; ?>');
<?php endif; ?>

function showToast(message, type = 'success') {
    const toast = document.getElementById('successToast');
    const toastMessage = document.getElementById('toastMessage');
    toastMessage.textContent = message;
    
    // Change color based on type
    if (type === 'error') {
        toast.style.background = '#ef4444';
    } else {
        toast.style.background = '#10b981';
    }
    
    toast.style.display = 'flex';
    
    setTimeout(() => {
        toast.style.display = 'none';
    }, 3000);
}

document.getElementById('bulkActionForm').addEventListener('submit', function(e) {
    const selectedCheckboxes = document.querySelectorAll('input[name="selected_invoices[]"]:checked');
    
    if (selectedCheckboxes.length === 0) {
        e.preventDefault();
        alert('Please select at least one order to proceed.');
        return false;
    }
});

setTimeout(() => {
    document.getElementById('loadingOverlay').style.display = 'none';
}, 10000);

function changeOrderStatus(selectElement) {
    const invoiceNo = selectElement.getAttribute('data-invoice');
    const newStatus = selectElement.value;
    const paymentType = selectElement.getAttribute('data-payment');
    const oldValue = selectElement.getAttribute('data-old-value') || selectElement.value;
    
    if (!selectElement.getAttribute('data-old-value')) {
        selectElement.setAttribute('data-old-value', oldValue);
    }
    
    // Simple confirmation dialog
    const confirmMsg = `Change status to ${newStatus} for invoice ${invoiceNo}?`;
    if (!confirm(confirmMsg)) {
        selectElement.value = oldValue;
        return;
    }
    
    // Show loading overlay
    document.getElementById('loadingOverlay').style.display = 'flex';
    
    const formData = new FormData();
    formData.append('invoice_no', invoiceNo);
    
    if (paymentType === 'cod') {
        if (newStatus === 'Processing') {
            formData.append('mark_processing', '1');
        } else if (newStatus === 'Shipped') {
            formData.append('mark_shipped', '1');
        } else if (newStatus === 'Completed') {
            formData.append('mark_completed', '1');
        } else if (newStatus === 'Canceled') {
            formData.append('mark_canceled', '1');
        }
    } else if (paymentType === 'mobile') {
        if (newStatus === 'Processing') {
            formData.append('mark_processing_both', '1');
        } else if (newStatus === 'Shipped') {
            formData.append('mark_shipped_both', '1');
        } else if (newStatus === 'Completed') {
            formData.append('mark_completed_both', '1');
        } else if (newStatus === 'Canceled') {
            formData.append('mark_canceled_both', '1');
        }
    }
    
    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        // Hide loading overlay
        document.getElementById('loadingOverlay').style.display = 'none';
        
        // Show success toast
        showToast(`Order status updated to ${newStatus} successfully!`);
        
        // Reload page after showing toast
        setTimeout(() => {
            location.reload();
        }, 1500);
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('loadingOverlay').style.display = 'none';
        
        // Show error toast
        showToast('Failed to update order status. Please try again.', 'error');
        selectElement.value = oldValue;
    });
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.status-dropdown').forEach(select => {
        select.setAttribute('data-old-value', select.value);
    });
});
</script>

<?php require 'footer.php'; ?>