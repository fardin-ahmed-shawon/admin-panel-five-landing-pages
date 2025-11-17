<?php
$current_page = basename($_SERVER['PHP_SELF']);
$page_title = 'Daily Sales Report';

require 'header.php';

// Get date filter from URL parameter or use today's date
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Query for daily statistics
$stats_query = "SELECT 
    COUNT(DISTINCT invoice_no) as total_orders,
    SUM(total_price) as total_revenue,
    SUM(product_quantity) as total_items,
    AVG(total_price) as avg_order_value
FROM order_info 
WHERE DATE(order_date) = ? AND order_visibility = 'Show'";

$stmt = $conn->prepare($stats_query);
$stmt->bind_param("s", $selected_date);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();

// Query for status breakdown
$status_query = "SELECT 
    order_status,
    COUNT(DISTINCT invoice_no) as count,
    SUM(total_price) as revenue
FROM order_info 
WHERE DATE(order_date) = ? AND order_visibility = 'Show'
GROUP BY order_status";

$stmt = $conn->prepare($status_query);
$stmt->bind_param("s", $selected_date);
$stmt->execute();
$status_result = $stmt->get_result();
$status_data = [];
while ($row = $status_result->fetch_assoc()) {
    $status_data[] = $row;
}

// Query for hourly sales
$hourly_query = "SELECT 
    HOUR(order_date) as hour,
    COUNT(DISTINCT invoice_no) as orders,
    SUM(total_price) as revenue
FROM order_info 
WHERE DATE(order_date) = ? AND order_visibility = 'Show'
GROUP BY HOUR(order_date)
ORDER BY hour";

$stmt = $conn->prepare($hourly_query);
$stmt->bind_param("s", $selected_date);
$stmt->execute();
$hourly_result = $stmt->get_result();
$hourly_data = [];
while ($row = $hourly_result->fetch_assoc()) {
    $hourly_data[] = $row;
}

// Query for detailed orders
$orders_query = "SELECT 
    p.product_code,
    o.invoice_no,
    o.product_title,
    o.product_quantity AS qty,
    o.total_price,
    o.user_full_name,
    o.user_phone,
    o.user_email,
    o.payment_method,
    o.order_status,
    o.order_date
FROM order_info AS o
JOIN product_info AS p 
    ON o.product_id = p.product_id
AND DATE(o.order_date) = ? 
  AND o.order_visibility = 'Show'
ORDER BY o.invoice_no DESC, o.order_date ASC
";


$stmt = $conn->prepare($orders_query);
$stmt->bind_param("s", $selected_date);
$stmt->execute();
$orders_result = $stmt->get_result();

// Query for top products
$products_query = "SELECT 
    p.product_code, 
    p.product_title,
    SUM(o.product_quantity) AS quantity_sold,
    SUM(o.total_price) AS revenue
FROM order_info AS o
JOIN product_info AS p 
    ON o.product_id = p.product_id
WHERE DATE(o.order_date) = ? 
  AND o.order_visibility = 'Show'
GROUP BY p.product_title, p.product_code
ORDER BY quantity_sold DESC
LIMIT 5
";


$stmt = $conn->prepare($products_query);
$stmt->bind_param("s", $selected_date);
$stmt->execute();
$products_result = $stmt->get_result();
$top_products = [];
while ($row = $products_result->fetch_assoc()) {
    $top_products[] = $row;
}
?>

<style>
    :root {
        --primary-color: #4F46E5;
        --success-color: #10B981;
        --warning-color: #F59E0B;
        --danger-color: #EF4444;
        --info-color: #3B82F6;
        --gray-50: #F9FAFB;
        --gray-100: #F3F4F6;
        --gray-200: #E5E7EB;
        --gray-300: #D1D5DB;
        --gray-600: #4B5563;
        --gray-700: #374151;
        --gray-900: #111827;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        --radius-lg: 12px;
        --radius-md: 8px;
    }

    body {
        background-color: var(--gray-50);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    /* Page Header */
    .modern-header {
        /* background: white; */
        padding: 24px 0;
        border-radius: var(--radius-lg);
        /* box-shadow: var(--shadow-sm); */
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }

    .modern-header h1 {
        font-size: 24px;
        font-weight: 800;
        color: var(--gray-900);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .modern-header .icon-wrapper {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--primary-color), #6366F1);
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    /* Date Filter */
    .date-filter-container {
        background: white;
        padding: 20px;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        margin-bottom: 24px;
    }

    .date-filter-form {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: flex-end;
    }

    .form-group-modern {
        flex: 1;
        min-width: 200px;
    }

    .form-group-modern label {
        display: block;
        font-size: 13px;
        font-weight: 500;
        color: var(--gray-700);
        margin-bottom: 6px;
    }

    .form-group-modern input {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius-md);
        font-size: 14px;
        transition: all 0.2s;
    }

    .form-group-modern input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    .btn-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .btn-modern {
        padding: 10px 18px;
        border: none;
        border-radius: var(--radius-md);
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .btn-modern:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    .btn-primary {
        background: var(--primary-color);
        color: white;
    }

    .btn-secondary {
        background: var(--gray-600);
        color: white;
    }

    .btn-outline {
        background: white;
        color: var(--gray-700);
        border: 1px solid var(--gray-300);
    }

    .btn-success {
        background: var(--success-color);
        color: white;
    }

    .btn-danger {
        background: var(--danger-color);
        color: white;
    }

    /* Statistics Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }

    .stat-card-modern {
        background: white;
        padding: 24px;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        border-left: 4px solid;
        transition: all 0.3s;
    }

    .stat-card-modern:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-2px);
    }

    .stat-card-modern.primary { border-left-color: var(--primary-color); }
    .stat-card-modern.success { border-left-color: var(--success-color); }
    .stat-card-modern.info { border-left-color: var(--info-color); }
    .stat-card-modern.warning { border-left-color: var(--warning-color); }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
    }

    .stat-label {
        font-size: 13px;
        font-weight: 500;
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-icon-modern {
        width: 44px;
        height: 44px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .stat-icon-modern.primary { background: rgba(79, 70, 229, 0.1); color: var(--primary-color); }
    .stat-icon-modern.success { background: rgba(16, 185, 129, 0.1); color: var(--success-color); }
    .stat-icon-modern.info { background: rgba(59, 130, 246, 0.1); color: var(--info-color); }
    .stat-icon-modern.warning { background: rgba(245, 158, 11, 0.1); color: var(--warning-color); }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--gray-900);
        line-height: 1;
    }

    /* Charts */
    .charts-grid {
        margin-bottom: 24px;
    }

    .chart-card {
        background: white;
        padding: 24px;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
    }

    .chart-card h3 {
        font-size: 16px;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0 0 20px 0;
    }

    .chart-wrapper {
        height: 300px;
        position: relative;
    }

    @media (max-width: 1200px) {
        .chart-wrapper {
            height: 350px;
        }
    }

    @media (max-width: 768px) {
        .chart-card {
            padding: 16px;
        }
        
        .chart-card h3 {
            font-size: 14px;
            margin-bottom: 16px;
        }
        
        .chart-wrapper {
            height: 280px;
        }
    }

    /* Table */
    .table-card {
        background: white;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        margin-bottom: 24px;
    }

    .table-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .table-header h3 {
        font-size: 16px;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0;
    }

    .table-responsive-modern {
        overflow-x: auto;
    }

    .modern-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .modern-table thead th {
        background: var(--gray-50);
        padding: 14px 16px;
        text-align: left;
        font-size: 12px;
        font-weight: 600;
        color: var(--gray-700);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--gray-200);
        white-space: nowrap;
    }

    .modern-table tbody td {
        padding: 16px;
        font-size: 14px;
        color: var(--gray-700);
        border-bottom: 1px solid var(--gray-100);
    }

    .modern-table tbody tr:hover {
        background: var(--gray-50);
    }

    .modern-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Status Badges */
    .status-badge-modern {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        white-space: nowrap;
    }

    .status-badge-modern::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        margin-right: 6px;
    }

    .status-pending { background: #FEF3C7; color: #92400E; }
    .status-pending::before { background: #F59E0B; }
    .status-processing { background: #DBEAFE; color: #1E40AF; }
    .status-processing::before { background: #3B82F6; }
    .status-shipped { background: #D1FAE5; color: #065F46; }
    .status-shipped::before { background: #10B981; }
    .status-completed { background: #D1FAE5; color: #065F46; }
    .status-completed::before { background: #10B981; }
    .status-canceled { background: #FEE2E2; color: #991B1B; }
    .status-canceled::before { background: #EF4444; }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--gray-600);
    }

    .empty-state i {
        font-size: 48px;
        color: var(--gray-300);
        margin-bottom: 16px;
    }

    .empty-state p {
        font-size: 14px;
        margin: 8px 0 0 0;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .modern-header {
            padding: 20px;
        }
        
        .modern-header h1 {
            font-size: 20px;
        }
        
        .stats-grid {
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 12px;
        }
        
        .stat-card-modern {
            padding: 16px;
        }
        
        .stat-value {
            font-size: 24px;
        }
        
        .stat-icon-modern {
            width: 36px;
            height: 36px;
            font-size: 18px;
        }
        
        .date-filter-form {
            flex-direction: column;
        }
        
        .form-group-modern {
            width: 100%;
        }
        
        .btn-actions {
            width: 100%;
            flex-direction: column;
        }
        
        .btn-modern {
            width: 100%;
            justify-content: center;
        }
        
        .modern-table {
            font-size: 12px;
        }
        
        .modern-table thead th,
        .modern-table tbody td {
            padding: 10px 8px;
        }
    }

    /* Print Styles */
    @media print {
        .sidebar, .navbar, .date-filter-container, .modern-header, .charts-grid, .btn-actions {
            display: none !important;
        }
        
        .content-wrapper {
            margin: 0 !important;
            padding: 20px !important;
        }
        
        .table-card {
            box-shadow: none;
            page-break-inside: avoid;
        }
    }
</style>

<!--------------------------->
<!-- START MAIN AREA -->
<!--------------------------->
<div class="content-wrapper">
    <!-- Page Header -->
    <div class="modern-header">
        <h1>
            <div class="icon-wrapper">
                <i class="mdi mdi-chart-line"></i>
            </div>
            Daily Sales Report
        </h1>
    </div>

    <!-- Date Filter -->
    <div class="date-filter-container">
        <form method="GET" action="" class="date-filter-form">
            <div class="form-group-modern">
                <label><b>Select Date</b></label>
                <input type="date" name="date" value="<?php echo $selected_date; ?>" max="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="btn-actions">
                <button type="submit" class="btn-modern btn-primary">
                    <i class="mdi mdi-magnify"></i> View Report
                </button>
                <a href="?date=<?php echo date('Y-m-d'); ?>" class="btn btn-modern btn-outline">
                    <i class="mdi mdi-calendar-today"></i> Today
                </a>
                <a href="?date=<?php echo date('Y-m-d', strtotime('-1 day')); ?>" class="btn btn-modern btn-secondary">
                    <i class="mdi mdi-calendar-minus"></i> Yesterday
                </a>
                <button type="button" class="btn-modern btn-success" onclick="exportToExcel()">
                    <i class="mdi mdi-file-excel"></i> Excel
                </button>
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card-modern primary">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Orders</div>
                    <div class="stat-value"><?php echo number_format($stats['total_orders'] ?? 0); ?></div>
                </div>
                <div class="stat-icon-modern primary">
                    <i class="mdi mdi-cart"></i>
                </div>
            </div>
        </div>
        <div class="stat-card-modern success">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Revenue</div>
                    <div class="stat-value">৳<?php echo number_format($stats['total_revenue'] ?? 0); ?></div>
                </div>
                <div class="stat-icon-modern success">
                    <i class="mdi mdi-currency-bdt"></i>
                </div>
            </div>
        </div>
        <div class="stat-card-modern info">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Items Sold</div>
                    <div class="stat-value"><?php echo number_format($stats['total_items'] ?? 0); ?></div>
                </div>
                <div class="stat-icon-modern info">
                    <i class="mdi mdi-package-variant"></i>
                </div>
            </div>
        </div>
        <div class="stat-card-modern warning">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Avg Order Value</div>
                    <div class="stat-value">৳<?php echo number_format($stats['avg_order_value'] ?? 0); ?></div>
                </div>
                <div class="stat-icon-modern warning">
                    <i class="mdi mdi-chart-bar"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row" style="margin-bottom: 24px;">
        <div class="col-md-8">
            <div class="card card-body">
                <h4><b>Hourly Sales Trend</b></h4>
                <div>
                    <canvas id="hourlySalesChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4 mt-4 mt-md-0">
            <div class="card card-body">
                <h4><b>Order Status</b></h4>
                <div>
                    <canvas id="statusChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Products -->
    <div class="table-card">
        <div class="table-header">
            <h3><b>Top 5 Products</b></h3>
        </div>
        <div class="table-responsive-modern">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>SKU</th>
                        <th>Product Name</th>
                        <th>Quantity Sold</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    foreach ($top_products as $product): 
                    ?>
                    <tr>
                        <td><strong>#<?php echo $i++; ?></strong></td>
                        <td><?php echo htmlspecialchars($product['product_code']); ?></td>
                        <td><?php echo htmlspecialchars($product['product_title']); ?></td>
                        <td><?php echo $product['quantity_sold']; ?> units</td>
                        <td><strong style="color: var(--success-color)">৳<?php echo number_format($product['revenue']); ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($top_products)): ?>
                    <tr>
                        <td colspan="4">
                            <div class="empty-state">
                                <i class="mdi mdi-package-variant-closed"></i>
                                <p>No products sold on this date</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Order Details (Per Product) -->
    <div class="table-card">
        <div class="table-header">
            <h3><b>Order Details</b></h3>
        </div>
        <div class="table-responsive-modern">
            <table class="modern-table" id="ordersTable">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>SKU</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Customer</th>
                        <th>Contact</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($orders_result->num_rows > 0): ?>
                        <?php while ($order = $orders_result->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($order['invoice_no']); ?></strong></td>
                                <td><?php echo htmlspecialchars($order['product_code']); ?></td>
                                <td><?php echo htmlspecialchars($order['product_title']); ?></td>
                                <td><?php echo (int)$order['qty']; ?></td>
                                <td><strong style="color: var(--success-color);">৳<?php echo number_format($order['total_price']); ?></strong></td>
                                <td><?php echo htmlspecialchars($order['user_full_name']); ?></td>
                                <td>
                                    <div><?php echo htmlspecialchars($order['user_phone']); ?></div>
                                    <small style="color: var(--gray-600);">
                                        <?php echo htmlspecialchars($order['user_email']); ?>
                                    </small>
                                </td>
                                <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                                <td>
                                    <span class="status-badge-modern status-<?php echo strtolower($order['order_status']); ?>">
                                        <?php echo htmlspecialchars($order['order_status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('h:i A', strtotime($order['order_date'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="mdi mdi-cart-off"></i>
                                    <p>No orders found for this date</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<!-- SheetJS for Excel Export -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<script>
// Chart.js defaults
Chart.defaults.font.family = "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif";
Chart.defaults.color = '#4B5563';

// Hourly Sales Chart
const hourlyData = <?php echo json_encode($hourly_data); ?>;
const hours = Array.from({length: 24}, (_, i) => i);
const hourlyRevenue = hours.map(h => {
    const found = hourlyData.find(d => parseInt(d.hour) === h);
    return found ? found.revenue : 0;
});
const hourlyOrders = hours.map(h => {
    const found = hourlyData.find(d => parseInt(d.hour) === h);
    return found ? found.orders : 0;
});

const ctx1 = document.getElementById('hourlySalesChart').getContext('2d');
new Chart(ctx1, {
    type: 'line',
    data: {
        labels: hours.map(h => (h < 10 ? '0' : '') + h + ':00'),
        datasets: [{
            label: 'Revenue (৳)',
            data: hourlyRevenue,
            borderColor: '#10B981',
            backgroundColor: 'rgba(16, 185, 129, 0.05)',
            tension: 0.4,
            fill: true,
            borderWidth: 2,
            yAxisID: 'y'
        }, {
            label: 'Orders',
            data: hourlyOrders,
            borderColor: '#4F46E5',
            backgroundColor: 'rgba(79, 70, 229, 0.05)',
            tension: 0.4,
            fill: true,
            borderWidth: 2,
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'index',
            intersect: false
        },
        plugins: {
            legend: {
                display: true,
                position: 'top',
                align: 'end',
                labels: {
                    boxWidth: 12,
                    boxHeight: 12,
                    padding: 15,
                    font: { size: 12 }
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                cornerRadius: 8,
                titleFont: { size: 13, weight: '600' },
                bodyFont: { size: 12 }
            }
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: { font: { size: 11 } }
            },
            y: {
                position: 'left',
                title: { display: true, text: 'Revenue (৳)', font: { size: 11, weight: '500' } },
                beginAtZero: true,
                grid: { color: '#F3F4F6' },
                ticks: { font: { size: 11 } }
            },
            y1: {
                position: 'right',
                title: { display: true, text: 'Orders', font: { size: 11, weight: '500' } },
                grid: { drawOnChartArea: false },
                beginAtZero: true,
                ticks: { font: { size: 11 } }
            }
        }
    }
});

// Status Chart
const statusData = <?php echo json_encode($status_data); ?>;
const statusLabels = statusData.map(s => s.order_status);
const statusCounts = statusData.map(s => s.count);
const statusColors = {
    'Pending': '#F59E0B',
    'Processing': '#3B82F6',
    'Shipped': '#10B981',
    'Completed': '#10B981',
    'Canceled': '#EF4444'
};

const ctx2 = document.getElementById('statusChart').getContext('2d');
new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: statusLabels,
        datasets: [{
            data: statusCounts,
            backgroundColor: statusLabels.map(l => statusColors[l] || '#6B7280'),
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    font: { size: 12 },
                    boxWidth: 12,
                    boxHeight: 12,
                    usePointStyle: true
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                cornerRadius: 8,
                titleFont: { size: 13, weight: '600' },
                bodyFont: { size: 12 }
            }
        },
        cutout: '65%'
    }
});

// Export to Excel
function exportToExcel() {
    const table = document.getElementById('ordersTable');
    const wb = XLSX.utils.book_new();
    
    // Create worksheet from table
    const ws = XLSX.utils.table_to_sheet(table);
    
    // Add worksheet to workbook
    XLSX.utils.book_append_sheet(wb, ws, "Orders");
    
    // Generate Excel file
    XLSX.writeFile(wb, 'Daily_Sales_Report_<?php echo $selected_date; ?>.xlsx');
}
</script>

<?php require 'footer.php'; ?>