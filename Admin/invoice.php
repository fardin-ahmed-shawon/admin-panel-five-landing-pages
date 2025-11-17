<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include 'database/dbConnection.php';
require_once 'functions.php'; // Make sure your helper functions are loaded

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
$websiteLogo   = $webInfoRow['logo'] ?? '';
$websitePhone  = $webInfoRow['phone'] ?? 'N/A';
$websiteEmail  = $webInfoRow['email'] ?? 'N/A';
$websiteAddress= $webInfoRow['address'] ?? 'N/A';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Invoice <?php echo htmlspecialchars($invoice_no); ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css" rel="stylesheet">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
body {
    background: #f5f6fa;
    font-family: 'Poppins', sans-serif;
}
.invoice-container {
    max-width: 900px;
    margin: 40px auto;
    background: #fff;
    border: 1px solid #ddd;
    padding: 40px 50px;
    border-radius: 10px;
}
.table th, .table td {
    vertical-align: middle !important;
}
.text-right { text-align: right; }
.btn-area {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-top: 30px;
}
.btn-custom {
    font-size: 16px;
    padding: 10px 25px;
    border-radius: 6px;
    transition: 0.3s;
}
.btn-dark {
    background: #000;
    color: #fff;
}
.btn-dark:hover {
    background: #333;
}
.btn-outline-dark {
    border: 1px solid #000;
    color: #000;
}
.btn-outline-dark:hover {
    background: #000;
    color: #fff;
}

/* Print Styling */
@media print {
    body * {
        visibility: hidden;
    }
    .invoice-container, .invoice-container * {
        visibility: visible;
    }
    .invoice-container {
        position: absolute;
        left: 0;
        top: 0;
        width: 210mm;
        min-height: 297mm;
        margin: 0;
        padding: 20mm;
        box-shadow: none;
        border: none;
    }
    .btn-area { display: none; }
}
</style>
</head>
<body>

<div class="invoice-container" id="invoice">
    <div class="text-center mb-4">
        <?php if (!empty($websiteLogo)) echo '<img src="'.htmlspecialchars($websiteLogo).'" style="width:100px;">'; ?>
        <h2 class="mt-3">Invoice</h2>
        <h5>Invoice No: <?php echo htmlspecialchars($order['invoice_no']); ?></h5>
    </div>

    <div class="row mb-4">
        <div class="col-sm-6">
            <strong>FROM:</strong><br>
            <?php echo htmlspecialchars($websiteName); ?><br>
            <?php echo htmlspecialchars($websitePhone); ?><br>
            <?php echo htmlspecialchars($websiteEmail); ?><br>
            <?php echo nl2br(htmlspecialchars($websiteAddress)); ?>
        </div>
        <div class="col-sm-6 text-end">
            <strong>Billed To:</strong><br>
            <?php echo htmlspecialchars($order['user_full_name']); ?><br>
            <?php echo htmlspecialchars($order['user_phone']); ?><br>
            <?php echo htmlspecialchars($order['user_email']); ?><br>
            <?php echo nl2br(htmlspecialchars($order['user_address'])); ?>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-sm-6">
            <strong>Payment Method:</strong><br>
            <?php echo htmlspecialchars($order['payment_method']); ?>
        </div>
        <div class="col-sm-6 text-end">
            <strong>Order Date:</strong><br>
            <?php echo date("F j, Y", strtotime($order['order_date'])); ?>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Item</th>
                    <th class="text-end">Price</th>
                    <th class="text-center">Qty</th>
                    <th>Size</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $subtotal = 0;
                while($item = $itemsRes->fetch_assoc()) {
                    $qty = max(1,(int)$item['product_quantity']);
                    $unit_price = (float)$item['total_price'] / $qty;
                    $subtotal += (float)$item['total_price'];
                    echo '<tr>
                            <td>'.htmlspecialchars($item['product_title']).'</td>
                            <td class="text-end">৳ '.number_format($unit_price,2).'</td>
                            <td class="text-center">'.intval($qty).'</td>
                            <td>'.htmlspecialchars($item['product_size']).'</td>
                            <td class="text-end">৳ '.number_format((float)$item['total_price'],2).'</td>
                          </tr>';
                }
                $shipping = (float)find_shipping_charge($invoice_no);
                $total = $subtotal + $shipping - $discount_amount;
                ?>
                <tr><td colspan="4" class="text-end"><strong>Subtotal</strong></td><td class="text-end">৳ <?php echo number_format($subtotal,2); ?></td></tr>
                <tr><td colspan="4" class="text-end"><strong>Shipping</strong></td><td class="text-end">৳ <?php echo number_format($shipping,2); ?></td></tr>
                <tr><td colspan="4" class="text-end"><strong>Discount</strong></td><td class="text-end">৳ <?php echo number_format($discount_amount,2); ?></td></tr>
                <tr><td colspan="4" class="text-end"><strong>Grand Total</strong></td><td class="text-end"><strong>৳ <?php echo number_format($total,2); ?></strong></td></tr>
            </tbody>
        </table>
    </div>
</div>

<div class="btn-area">
    <button class="btn btn-dark btn-custom" onclick="downloadPDF()">
        <i class="mdi mdi-download"></i> Download PDF
    </button>
    <button class="btn btn-outline-dark btn-custom" onclick="window.print()">
        <i class="mdi mdi-printer"></i> Print Invoice
    </button>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
async function downloadPDF() {
    const { jsPDF } = window.jspdf;
    const invoice = document.querySelector("#invoice");

    // A4 size settings
    const pdf = new jsPDF("p", "mm", "a4");
    const canvas = await html2canvas(invoice, { scale: 2 });
    const imgData = canvas.toDataURL("image/png");

    const pdfWidth = 210; // A4 width in mm
    const pdfHeight = (canvas.height * pdfWidth) / canvas.width;

    const marginX = (210 - pdfWidth) / 2;
    pdf.addImage(imgData, "PNG", 0, 0, pdfWidth, pdfHeight);
    pdf.save("Invoice_<?php echo htmlspecialchars($invoice_no); ?>.pdf");
}
</script>

</body>
</html>