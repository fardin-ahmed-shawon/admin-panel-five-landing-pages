<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include 'database/dbConnection.php';

// Get invoice_no from GET
$invoice_no = $_GET['inv'] ?? '';
if (!$invoice_no) die('Invoice number missing.');

// Fetch order header
$stmtOrder = $conn->prepare("SELECT * FROM order_info WHERE invoice_no = ? LIMIT 1");
$stmtOrder->bind_param("s", $invoice_no);
$stmtOrder->execute();
$orderRes = $stmtOrder->get_result();
$order = $orderRes->fetch_assoc();
if (!$order) die('Order not found.');

// Fetch discount
$stmtDiscount = $conn->prepare("SELECT total_discount_amount FROM order_discount_list WHERE invoice_no = ? LIMIT 1");
$stmtDiscount->bind_param("s", $invoice_no);
$stmtDiscount->execute();
$dres = $stmtDiscount->get_result();
$discount_amount = 0.0;
if ($dres && $dres->num_rows > 0) {
    $drow = $dres->fetch_assoc();
    $discount_amount = (float)$drow['total_discount_amount'];
}

// Fetch items
$stmtItems = $conn->prepare("SELECT * FROM order_info WHERE invoice_no = ?");
$stmtItems->bind_param("s", $invoice_no);
$stmtItems->execute();
$itemsRes = $stmtItems->get_result();

// Website info
$webInfoRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM website_info WHERE id=1"));
$websiteName   = $webInfoRow['name'] ?? 'Easy Tech';
$websiteLogo   = '';
$websitePhone  = $webInfoRow['phone'] ?? 'N/A';
$websiteEmail  = $webInfoRow['email'] ?? 'N/A';
$websiteAddress= $webInfoRow['address'] ?? 'N/A';
$insideCharge  = isset($webInfoRow['inside_delivery_charge']) ? (int)$webInfoRow['inside_delivery_charge'] : 80;
$outsideCharge = isset($webInfoRow['outside_delivery_charge']) ? (int)$webInfoRow['outside_delivery_charge'] : 150;
$noCharge = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Invoice <?php echo htmlspecialchars($invoice_no); ?></title>
<link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Courier+Prime:wght@400;700&display=swap');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background: #f0f0f0;
    font-family: 'Courier Prime', monospace;
    padding: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

.receipt-container {
    background: white;
    width: 80mm;
    padding: 10mm;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    position: relative;
}

.receipt-header {
    text-align: center;
    border-bottom: 2px dashed #000;
    padding-bottom: 10px;
    margin-bottom: 10px;
}

.receipt-header img {
    width: 50px;
    height: 50px;
    margin-bottom: 5px;
}

.receipt-header h2 {
    font-size: 18px;
    font-weight: bold;
    margin: 5px 0;
    text-transform: uppercase;
}

.receipt-header p {
    font-size: 11px;
    line-height: 1.4;
    margin: 2px 0;
}

.invoice-number {
    text-align: center;
    font-size: 12px;
    font-weight: bold;
    margin: 8px 0;
    padding: 5px 0;
    border-top: 1px dashed #000;
    border-bottom: 1px dashed #000;
}

.customer-info {
    font-size: 11px;
    margin: 10px 0;
    line-height: 1.5;
}

.customer-info strong {
    display: inline-block;
    width: 60px;
}

.order-details {
    font-size: 10px;
    margin: 10px 0;
    padding: 8px 0;
    border-top: 1px dashed #000;
    border-bottom: 1px dashed #000;
}

.order-details div {
    display: flex;
    justify-content: space-between;
    margin: 3px 0;
}

.items-table {
    width: 100%;
    font-size: 11px;
    margin: 10px 0;
    border-collapse: collapse;
}

.items-table thead {
    border-bottom: 1px solid #000;
}

.items-table th {
    padding: 5px 2px;
    text-align: left;
    font-weight: bold;
}

.items-table td {
    padding: 5px 2px;
    border-bottom: 1px dotted #ccc;
}

.items-table .item-name {
    max-width: 120px;
    word-wrap: break-word;
}

.items-table .text-right {
    text-align: right;
}

.items-table .text-center {
    text-align: center;
}

.summary {
    margin-top: 10px;
    padding-top: 10px;
    border-top: 2px solid #000;
    font-size: 11px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin: 5px 0;
}

.summary-row.total {
    font-size: 13px;
    font-weight: bold;
    margin-top: 8px;
    padding-top: 8px;
    border-top: 1px dashed #000;
}

.footer {
    text-align: center;
    margin-top: 15px;
    padding-top: 10px;
    border-top: 2px dashed #000;
    font-size: 10px;
}

.footer p {
    margin: 3px 0;
}

.thank-you {
    font-size: 13px;
    font-weight: bold;
    margin: 10px 0;
}

.action-buttons {
    position: fixed;
    bottom: 30px;
    right: 30px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    z-index: 1000;
}

.action-buttons button {
    background: #000;
    color: white;
    border: none;
    padding: 10px 25px;
    font-size: 15px;
    font-family: 'Courier Prime', monospace;
    cursor: pointer;
    border-radius: 5px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    gap: 8px;
}

.action-buttons button:hover {
    background: #333;
}

@media print {
    body {
        background: white;
        padding: 0;
    }
    
    .receipt-container {
        width: 80mm;
        box-shadow: none;
        margin: 0;
    }
    
    .action-buttons {
        display: none;
    }
}
</style>
</head>
<body>

<div class="receipt-container" id="receipt">
    <!-- Header -->
    <div class="receipt-header">
        <?php if(!empty($websiteLogo)): ?>
            <img src="<?php echo htmlspecialchars($websiteLogo); ?>" alt="Logo">
        <?php endif; ?>
        <h2><?php echo htmlspecialchars($websiteName); ?></h2>
        <p><?php echo htmlspecialchars($websitePhone); ?></p>
        <p><?php echo htmlspecialchars($websiteEmail); ?></p>
        <p><?php echo htmlspecialchars($websiteAddress); ?></p>
    </div>

    <!-- Invoice Number -->
    <div class="invoice-number">
        INVOICE #<?php echo htmlspecialchars($order['invoice_no']); ?>
    </div>

    <!-- Order Details -->
    <div class="order-details">
        <div>
            <span>Date:</span>
            <span><?php echo date("d/m/Y H:i", strtotime($order['order_date'])); ?></span>
        </div>
        <div>
            <span>Payment:</span>
            <span><?php echo htmlspecialchars($order['payment_method']); ?></span>
        </div>
    </div>

    <!-- Customer Info -->
    <div class="customer-info">
        <div><strong>Customer:</strong> <?php echo htmlspecialchars($order['user_full_name']); ?></div>
        <div><strong>Phone:</strong> <?php echo htmlspecialchars($order['user_phone']); ?></div>
        <div><strong>Address:</strong> <?php echo htmlspecialchars($order['user_address']); ?></div>
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Price</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $subtotal = 0;
        while($item = $itemsRes->fetch_assoc()) {
            $qty = max(1,(int)$item['product_quantity']);
            $unit_price = (float)$item['total_price'] / $qty;
            $subtotal += (float)$item['total_price'];
            
            $itemName = htmlspecialchars($item['product_title']);
            if(!empty($item['product_size']) && $item['product_size'] != 'N/A') {
                $itemName .= ' (' . htmlspecialchars($item['product_size']) . ')';
            }
        ?>
            <tr>
                <td class="item-name"><?php echo $itemName; ?></td>
                <td class="text-center"><?php echo intval($qty); ?></td>
                <td class="text-right">৳<?php echo number_format($unit_price, 2); ?></td>
                <td class="text-right">৳<?php echo number_format((float)$item['total_price'], 2); ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <!-- Summary -->
    <?php
    $shipping = ($order['city_address'] === "") 
        ? $noCharge 
        : (($order['city_address'] === "Inside Dhaka") 
            ? $insideCharge 
            : $outsideCharge);
    
    $total = $subtotal + $shipping - $discount_amount;
    ?>
    <div class="summary">
        <div class="summary-row">
            <span>Subtotal:</span>
            <span>৳<?php echo number_format($subtotal, 2); ?></span>
        </div>
        <div class="summary-row">
            <span>Delivery Charge:</span>
            <span>৳<?php echo number_format($shipping, 2); ?></span>
        </div>
        <?php if($discount_amount > 0): ?>
        <div class="summary-row">
            <span>Discount:</span>
            <span>-৳<?php echo number_format($discount_amount, 2); ?></span>
        </div>
        <?php endif; ?>
        <div class="summary-row total">
            <span>TOTAL:</span>
            <span>৳<?php echo number_format($total, 2); ?></span>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p class="thank-you">THANK YOU!</p>
        <p>For any queries, please contact us</p>
        <p>Visit again!</p>
    </div>
</div>

<!-- Action Buttons -->
<div class="action-buttons">
    <button onclick="window.print()">
        <span class="mdi mdi-printer"></span>
        Print
    </button>
    <button id="downloadPdf">
        <span class="mdi mdi-file-pdf"></span>
        Download PDF
    </button>
</div>

<!-- JS Libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
document.getElementById("downloadPdf").addEventListener("click", async () => {
    const receipt = document.getElementById("receipt");
    const { jsPDF } = window.jspdf;

    const canvas = await html2canvas(receipt, { scale: 2 });
    const imgData = canvas.toDataURL("image/png");

    // Convert 80mm width to points (1mm = 2.8346pt)
    const pdfWidth = 80 * 2.8346;
    const pdfHeight = (canvas.height * pdfWidth) / canvas.width;

    const pdf = new jsPDF({
        orientation: "portrait",
        unit: "pt",
        format: [pdfWidth, pdfHeight]
    });

    pdf.addImage(imgData, "PNG", 0, 0, pdfWidth, pdfHeight);
    pdf.save("Invoice_<?php echo htmlspecialchars($invoice_no); ?>.pdf");
});
</script>

</body>
</html>