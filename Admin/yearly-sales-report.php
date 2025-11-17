<?php
$current_page = basename($_SERVER['PHP_SELF']);
$page_title = 'Yearly Sales Report';

require 'header.php';

// Get year filter from URL parameter or use current year
$selected_year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Query for yearly statistics
$stats_query = "SELECT 
    COUNT(DISTINCT invoice_no) as total_orders,
    SUM(total_price) as total_revenue,
    SUM(product_quantity) as total_items,
    AVG(total_price) as avg_order_value,
    COUNT(DISTINCT user_email) as unique_customers
FROM order_info 
WHERE YEAR(order_date) = ? AND order_visibility = 'Show'";

$stmt = $conn->prepare($stats_query);
$stmt->bind_param("i", $selected_year);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();

// Query for monthly breakdown
$monthly_query = "SELECT 
    MONTH(order_date) as month,
    COUNT(DISTINCT invoice_no) as orders,
    SUM(total_price) as revenue,
    SUM(product_quantity) as items
FROM order_info 
WHERE YEAR(order_date) = ? AND order_visibility = 'Show'
GROUP BY MONTH(order_date)
ORDER BY month";

$stmt = $conn->prepare($monthly_query);
$stmt->bind_param("i", $selected_year);
$stmt->execute();
$monthly_result = $stmt->get_result();
$monthly_data = array_fill(1, 12, ['month' => 0, 'orders' => 0, 'revenue' => 0, 'items' => 0]);
while ($row = $monthly_result->fetch_assoc()) {
    $monthly_data[$row['month']] = $row;
}

// Query for status breakdown
$status_query = "SELECT 
    order_status,
    COUNT(DISTINCT invoice_no) as count,
    SUM(total_price) as revenue
FROM order_info 
WHERE YEAR(order_date) = ? AND order_visibility = 'Show'
GROUP BY order_status";

$stmt = $conn->prepare($status_query);
$stmt->bind_param("i", $selected_year);
$stmt->execute();
$status_result = $stmt->get_result();
$status_data = [];
while ($row = $status_result->fetch_assoc()) {
    $status_data[] = $row;
}

// Query for payment method breakdown
$payment_query = "SELECT 
    payment_method,
    COUNT(DISTINCT invoice_no) as count,
    SUM(total_price) as revenue
FROM order_info 
WHERE YEAR(order_date) = ? AND order_visibility = 'Show'
GROUP BY payment_method";

$stmt = $conn->prepare($payment_query);
$stmt->bind_param("i", $selected_year);
$stmt->execute();
$payment_result = $stmt->get_result();
$payment_data = [];
while ($row = $payment_result->fetch_assoc()) {
    $payment_data[] = $row;
}

// Query for quarterly breakdown
$quarterly_query = "SELECT 
    QUARTER(order_date) as quarter,
    COUNT(DISTINCT invoice_no) as orders,
    SUM(total_price) as revenue
FROM order_info 
WHERE YEAR(order_date) = ? AND order_visibility = 'Show'
GROUP BY QUARTER(order_date)
ORDER BY quarter";

$stmt = $conn->prepare($quarterly_query);
$stmt->bind_param("i", $selected_year);
$stmt->execute();
$quarterly_result = $stmt->get_result();
$quarterly_data = [];
while ($row = $quarterly_result->fetch_assoc()) {
    $quarterly_data[] = $row;
}

// Query for top products
$products_query = "SELECT 
    p.product_code, 
    p.product_title,
    SUM(o.product_quantity) AS quantity_sold,
    SUM(o.total_price) AS revenue
FROM order_info AS o
JOIN product_info AS p 
    ON o.product_id = p.product_id
WHERE YEAR(o.order_date) = ? 
  AND o.order_visibility = 'Show'
GROUP BY p.product_title, p.product_code
ORDER BY quantity_sold DESC
LIMIT 20";

$stmt = $conn->prepare($products_query);
$stmt->bind_param("i", $selected_year);
$stmt->execute();
$products_result = $stmt->get_result();
$top_products = [];
while ($row = $products_result->fetch_assoc()) {
    $top_products[] = $row;
}

// Query for top customers
$customer_query = "SELECT 
    user_full_name,
    user_phone,
    user_email,
    COUNT(DISTINCT invoice_no) as order_count,
    SUM(total_price) as total_spent
FROM order_info 
WHERE YEAR(order_date) = ? AND order_visibility = 'Show'
GROUP BY user_full_name, user_phone, user_email
ORDER BY total_spent DESC
LIMIT 20";

$stmt = $conn->prepare($customer_query);
$stmt->bind_param("i", $selected_year);
$stmt->execute();
$customer_result = $stmt->get_result();
$top_customers = [];
while ($row = $customer_result->fetch_assoc()) {
    $top_customers[] = $row;
}

// Calculate growth compared to previous year
$prev_year = $selected_year - 1;
$prev_stats_query = "SELECT 
    SUM(total_price) as total_revenue
FROM order_info 
WHERE YEAR(order_date) = ? AND order_visibility = 'Show'";

$stmt = $conn->prepare($prev_stats_query);
$stmt->bind_param("i", $prev_year);
$stmt->execute();
$prev_stats = $stmt->get_result()->fetch_assoc();

$growth_rate = 0;
if ($prev_stats['total_revenue'] > 0) {
    $growth_rate = (($stats['total_revenue'] - $prev_stats['total_revenue']) / $prev_stats['total_revenue']) * 100;
}

// Find best and worst performing months
$best_month = ['revenue' => 0, 'month' => 0];
$worst_month = ['revenue' => PHP_INT_MAX, 'month' => 0];
foreach ($monthly_data as $month => $data) {
    if ($data['revenue'] > $best_month['revenue']) {
        $best_month = ['revenue' => $data['revenue'], 'month' => $month];
    }
    if ($data['revenue'] > 0 && $data['revenue'] < $worst_month['revenue']) {
        $worst_month = ['revenue' => $data['revenue'], 'month' => $month];
    }
}
?>

<style>
    :root {
        --primary-color: #4F46E5;
        --success-color: #10B981;
        --warning-color: #F59E0B;
        --danger-color: #EF4444;
        --info-color: #3B82F6;
        --purple-color: #8B5CF6;
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

    /* Year Filter */
    .year-filter-container {
        background: white;
        padding: 20px;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        margin-bottom: 24px;
    }

    .year-filter-form {
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

    .form-group-modern input, .form-group-modern select {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius-md);
        font-size: 14px;
        transition: all 0.2s;
    }

    .form-group-modern input:focus, .form-group-modern select:focus {
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
        text-decoration: none;
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
    .stat-card-modern.purple { border-left-color: var(--purple-color); }

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
    .stat-icon-modern.purple { background: rgba(139, 92, 246, 0.1); color: var(--purple-color); }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--gray-900);
        line-height: 1;
    }

    .stat-growth {
        margin-top: 8px;
        font-size: 12px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .stat-growth.positive { color: var(--success-color); }
    .stat-growth.negative { color: var(--danger-color); }

    /* Insights Cards */
    .insights-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }

    .insight-card {
        background: linear-gradient(135deg, var(--primary-color), #6366F1);
        padding: 24px;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        color: white;
    }

    .insight-card.success {
        background: linear-gradient(135deg, var(--success-color), #059669);
    }

    .insight-card.warning {
        background: linear-gradient(135deg, var(--warning-color), #D97706);
    }

    .insight-card h4 {
        font-size: 14px;
        font-weight: 600;
        margin: 0 0 12px 0;
        opacity: 0.9;
    }

    .insight-value {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .insight-label {
        font-size: 13px;
        opacity: 0.9;
    }

    /* Charts */
    .charts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }

    .chart-card {
        background: white;
        padding: 24px;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
    }

    .chart-card.full-width {
        grid-column: 1 / -1;
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

    /* Quarterly Summary */
    .quarterly-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .quarter-card {
        background: white;
        padding: 20px;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        border-top: 3px solid var(--primary-color);
    }

    .quarter-card h4 {
        font-size: 13px;
        font-weight: 600;
        color: var(--gray-600);
        margin: 0 0 12px 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .quarter-stats {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .quarter-stat {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
    }

    .quarter-stat-label {
        color: var(--gray-600);
    }

    .quarter-stat-value {
        font-weight: 600;
        color: var(--gray-900);
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
        
        .year-filter-form {
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

        .charts-grid {
            grid-template-columns: 1fr;
        }

        .chart-card.full-width {
            grid-column: 1;
        }

        .quarterly-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* Print Styles */
    @media print {
        .sidebar, .navbar, .year-filter-container, .modern-header, .btn-actions {
            display: none !important;
        }
        
        .content-wrapper {
            margin: 0 !important;
            padding: 20px !important;
        }
        
        .table-card, .chart-card {
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
                <i class="mdi mdi-calendar-range"></i>
            </div>
            Yearly Sales Report
        </h1>
    </div>

    <!-- Year Filter -->
    <div class="year-filter-container">
        <form method="GET" action="" class="year-filter-form">
            <div class="form-group-modern">
                <label><b>Select Year</b></label>
                <select name="year" required>
                    <?php
                    $current_year = date('Y');
                    for ($y = $current_year; $y >= 2020; $y--) {
                        $selected = ($y == $selected_year) ? 'selected' : '';
                        echo "<option value='$y' $selected>$y</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="btn-actions">
                <button type="submit" class="btn-modern btn-primary">
                    <i class="mdi mdi-magnify"></i> View Report
                </button>
                <a href="?year=<?php echo date('Y'); ?>" class="btn-modern btn-outline">
                    <i class="mdi mdi-calendar-today"></i> This Year
                </a>
                <a href="?year=<?php echo date('Y') - 1; ?>" class="btn-modern btn-secondary">
                    <i class="mdi mdi-calendar-minus"></i> Last Year
                </a>
                <button type="button" class="btn-modern btn-success" onclick="exportToExcel()">
                    <i class="mdi mdi-file-excel"></i> Export Excel
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
                    <?php if (abs($growth_rate) > 0): ?>
                    <div class="stat-growth <?php echo $growth_rate >= 0 ? 'positive' : 'negative'; ?>">
                        <i class="mdi mdi-<?php echo $growth_rate >= 0 ? 'trending-up' : 'trending-down'; ?>"></i>
                        <?php echo number_format(abs($growth_rate), 1); ?>% vs last year
                    </div>
                    <?php endif; ?>
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
        <div class="stat-card-modern purple">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Unique Customers</div>
                    <div class="stat-value"><?php echo number_format($stats['unique_customers'] ?? 0); ?></div>
                </div>
                <div class="stat-icon-modern purple">
                    <i class="mdi mdi-account-group"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Insights -->
    <div class="insights-grid">
        <div class="insight-card">
            <h4>BEST PERFORMING MONTH</h4>
            <div class="insight-value"><?php echo date('F', mktime(0, 0, 0, $best_month['month'], 1)); ?></div>
            <div class="insight-label">৳<?php echo number_format($best_month['revenue']); ?> Revenue</div>
        </div>
        <?php if ($worst_month['revenue'] < PHP_INT_MAX): ?>
        <div class="insight-card warning">
            <h4>LOWEST PERFORMING MONTH</h4>
            <div class="insight-value"><?php echo date('F', mktime(0, 0, 0, $worst_month['month'], 1)); ?></div>
            <div class="insight-label">৳<?php echo number_format($worst_month['revenue']); ?> Revenue</div>
        </div>
        <?php endif; ?>
        <div class="insight-card success">
            <h4>AVERAGE MONTHLY REVENUE</h4>
            <div class="insight-value">৳<?php echo number_format(($stats['total_revenue'] ?? 0) / 12); ?></div>
            <div class="insight-label">Per Month in <?php echo $selected_year; ?></div>
        </div>
    </div>

    <!-- Quarterly Breakdown -->
    <div class="quarterly-grid">
        <?php 
        $quarters = ['Q1', 'Q2', 'Q3', 'Q4'];
        foreach ($quarterly_data as $index => $quarter): 
        ?>
        <div class="quarter-card">
            <h4><?php echo $quarters[$quarter['quarter'] - 1]; ?> - <?php echo $selected_year; ?></h4>
            <div class="quarter-stats">
                <div class="quarter-stat">
                    <span class="quarter-stat-label">Orders:</span>
                    <span class="quarter-stat-value"><?php echo number_format($quarter['orders']); ?></span>
                </div>
                <div class="quarter-stat">
                    <span class="quarter-stat-label">Revenue:</span>
                    <span class="quarter-stat-value">৳<?php echo number_format($quarter['revenue']); ?></span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Charts -->
    <div class="charts-grid">
        <div class="chart-card full-width">
            <h4><b>Monthly Revenue & Orders Trend</b></h4>
            <div class="chart-wrapper">
                <canvas id="monthlyTrendChart"></canvas>
            </div>
        </div>
        <div class="chart-card">
            <h4><b>Quarterly Performance</b></h4>
            <div class="chart-wrapper">
                <canvas id="quarterlyChart"></canvas>
            </div>
        </div>
        <div class="chart-card">
            <h4><b>Payment Methods Distribution</b></h4>
            <div class="chart-wrapper">
                <canvas id="paymentChart"></canvas>
            </div>
        </div>
        <div class="chart-card">
            <h4><b>Order Status Breakdown</b></h4>
            <div class="chart-wrapper">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Products -->
    <div class="table-card">
        <div class="table-header">
            <h3><b>Top 20 Products of <?php echo $selected_year; ?></b></h3>
        </div>
        <div class="table-responsive-modern">
            <table class="modern-table" id="productsTable">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>SKU</th>
                        <th>Product Name</th>
                        <th>Quantity Sold</th>
                        <th>Revenue</th>
                        <th>% of Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    foreach ($top_products as $product): 
                        $percentage = ($stats['total_revenue'] > 0) ? ($product['revenue'] / $stats['total_revenue']) * 100 : 0;
                    ?>
                    <tr>
                        <td><strong>#<?php echo $i++; ?></strong></td>
                        <td><?php echo htmlspecialchars($product['product_code']); ?></td>
                        <td><?php echo htmlspecialchars($product['product_title']); ?></td>
                        <td><?php echo number_format($product['quantity_sold']); ?> units</td>
                        <td><strong style="color: var(--success-color)">৳<?php echo number_format($product['revenue']); ?></strong></td>
                        <td><?php echo number_format($percentage, 2); ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($top_products)): ?>
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="mdi mdi-package-variant-closed"></i>
                                <p>No products sold in this year</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top Customers -->
    <div class="table-card">
        <div class="table-header">
            <h3><b>Top 20 Customers of <?php echo $selected_year; ?></b></h3>
        </div>
        <div class="table-responsive-modern">
            <table class="modern-table" id="customersTable">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Customer Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Orders</th>
                        <th>Total Spent</th>
                        <th>Avg Order</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    foreach ($top_customers as $customer): 
                        $avg_order = $customer['order_count'] > 0 ? $customer['total_spent'] / $customer['order_count'] : 0;
                    ?>
                    <tr>
                        <td><strong>#<?php echo $i++; ?></strong></td>
                        <td><?php echo htmlspecialchars($customer['user_full_name']); ?></td>
                        <td><?php echo htmlspecialchars($customer['user_phone']); ?></td>
                        <td><?php echo htmlspecialchars($customer['user_email']); ?></td>
                        <td><?php echo $customer['order_count']; ?> orders</td>
                        <td><strong style="color: var(--success-color)">৳<?php echo number_format($customer['total_spent']); ?></strong></td>
                        <td>৳<?php echo number_format($avg_order); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($top_customers)): ?>
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="mdi mdi-account-off"></i>
                                <p>No customers found in this year</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Monthly Breakdown Table -->
    <div class="table-card">
        <div class="table-header">
            <h3><b>Monthly Breakdown for <?php echo $selected_year; ?></b></h3>
        </div>
        <div class="table-responsive-modern">
            <table class="modern-table" id="monthlyTable">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Orders</th>
                        <th>Items Sold</th>
                        <th>Revenue</th>
                        <th>Avg Order Value</th>
                        <th>% of Annual</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $month_names = ['', 'January', 'February', 'March', 'April', 'May', 'June', 
                                   'July', 'August', 'September', 'October', 'November', 'December'];
                    for ($m = 1; $m <= 12; $m++): 
                        $data = $monthly_data[$m];
                        $avg_value = $data['orders'] > 0 ? $data['revenue'] / $data['orders'] : 0;
                        $percentage = ($stats['total_revenue'] > 0) ? ($data['revenue'] / $stats['total_revenue']) * 100 : 0;
                    ?>
                    <tr>
                        <td><strong><?php echo $month_names[$m]; ?></strong></td>
                        <td><?php echo number_format($data['orders']); ?></td>
                        <td><?php echo number_format($data['items']); ?></td>
                        <td><strong style="color: var(--success-color)">৳<?php echo number_format($data['revenue']); ?></strong></td>
                        <td>৳<?php echo number_format($avg_value); ?></td>
                        <td><?php echo number_format($percentage, 1); ?>%</td>
                    </tr>
                    <?php endfor; ?>
                    <tr style="background: var(--dark); font-weight: 600;">
                        <td><strong>TOTAL</strong></td>
                        <td><?php echo number_format($stats['total_orders']); ?></td>
                        <td><?php echo number_format($stats['total_items']); ?></td>
                        <td><strong style="color: var(--success-color)">৳<?php echo number_format($stats['total_revenue']); ?></strong></td>
                        <td>৳<?php echo number_format($stats['avg_order_value']); ?></td>
                        <td>100%</td>
                    </tr>
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

// Monthly Trend Chart
const monthlyData = <?php echo json_encode(array_values($monthly_data)); ?>;
const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
const monthlyRevenue = monthlyData.map(d => d.revenue);
const monthlyOrders = monthlyData.map(d => d.orders);

const ctx1 = document.getElementById('monthlyTrendChart').getContext('2d');
new Chart(ctx1, {
    type: 'line',
    data: {
        labels: monthLabels,
        datasets: [{
            label: 'Revenue (৳)',
            data: monthlyRevenue,
            borderColor: '#10B981',
            backgroundColor: 'rgba(16, 185, 129, 0.05)',
            tension: 0.4,
            fill: true,
            borderWidth: 3,
            yAxisID: 'y'
        }, {
            label: 'Orders',
            data: monthlyOrders,
            borderColor: '#4F46E5',
            backgroundColor: 'rgba(79, 70, 229, 0.05)',
            tension: 0.4,
            fill: true,
            borderWidth: 3,
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
                ticks: { 
                    font: { size: 11 },
                    callback: function(value) {
                        return '৳' + value.toLocaleString();
                    }
                }
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

// Quarterly Chart
const quarterlyData = <?php echo json_encode($quarterly_data); ?>;
const quarterLabels = quarterlyData.map(q => 'Q' + q.quarter);
const quarterRevenue = quarterlyData.map(q => q.revenue);

const ctx2 = document.getElementById('quarterlyChart').getContext('2d');
new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: quarterLabels,
        datasets: [{
            label: 'Revenue (৳)',
            data: quarterRevenue,
            backgroundColor: ['#4F46E5', '#10B981', '#F59E0B', '#EF4444'],
            borderRadius: 8,
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                cornerRadius: 8,
                titleFont: { size: 13, weight: '600' },
                bodyFont: { size: 12 },
                callbacks: {
                    label: function(context) {
                        return 'Revenue: ৳' + context.parsed.y.toLocaleString();
                    }
                }
            }
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: { font: { size: 11 } }
            },
            y: {
                beginAtZero: true,
                grid: { color: '#F3F4F6' },
                ticks: { 
                    font: { size: 11 },
                    callback: function(value) {
                        return '৳' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

// Payment Methods Chart
const paymentData = <?php echo json_encode($payment_data); ?>;
const paymentLabels = paymentData.map(p => p.payment_method);
const paymentCounts = paymentData.map(p => p.count);
const paymentColors = ['#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#3B82F6', '#8B5CF6'];

const ctx3 = document.getElementById('paymentChart').getContext('2d');
new Chart(ctx3, {
    type: 'doughnut',
    data: {
        labels: paymentLabels,
        datasets: [{
            data: paymentCounts,
            backgroundColor: paymentColors,
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
                bodyFont: { size: 12 },
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.parsed || 0;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((value / total) * 100).toFixed(1);
                        return label + ': ' + value + ' (' + percentage + '%)';
                    }
                }
            }
        },
        cutout: '65%'
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

const ctx4 = document.getElementById('statusChart').getContext('2d');
new Chart(ctx4, {
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
    const wb = XLSX.utils.book_new();
    
    // Summary Sheet
    const summaryData = [
        ['Yearly Sales Report'],
        ['Year', '<?php echo $selected_year; ?>'],
        [],
        ['Metric', 'Value'],
        ['Total Orders', '<?php echo number_format($stats['total_orders'] ?? 0); ?>'],
        ['Total Revenue', '৳<?php echo number_format($stats['total_revenue'] ?? 0); ?>'],
        ['Items Sold', '<?php echo number_format($stats['total_items'] ?? 0); ?>'],
        ['Average Order Value', '৳<?php echo number_format($stats['avg_order_value'] ?? 0); ?>'],
        ['Unique Customers', '<?php echo number_format($stats['unique_customers'] ?? 0); ?>'],
        ['Growth Rate vs Previous Year', '<?php echo number_format($growth_rate, 2); ?>%']
    ];
    const ws1 = XLSX.utils.aoa_to_sheet(summaryData);
    XLSX.utils.book_append_sheet(wb, ws1, "Summary");
    
    // Monthly Breakdown Sheet
    const monthlyTable = document.getElementById('monthlyTable');
    const ws2 = XLSX.utils.table_to_sheet(monthlyTable);
    XLSX.utils.book_append_sheet(wb, ws2, "Monthly Breakdown");
    
    // Top Products Sheet
    const productsTable = document.getElementById('productsTable');
    const ws3 = XLSX.utils.table_to_sheet(productsTable);
    XLSX.utils.book_append_sheet(wb, ws3, "Top Products");
    
    // Top Customers Sheet
    const customersTable = document.getElementById('customersTable');
    const ws4 = XLSX.utils.table_to_sheet(customersTable);
    XLSX.utils.book_append_sheet(wb, ws4, "Top Customers");
    
    // Generate Excel file
    XLSX.writeFile(wb, 'Yearly_Sales_Report_<?php echo $selected_year; ?>.xlsx');
}
</script>

<?php require 'footer.php'; ?>