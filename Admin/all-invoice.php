<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include 'database/dbConnection.php';
require_once 'functions.php'; // your helper functions

// Optional selected invoices
$selectedInvoices = [];
if (isset($_GET['invoice'])) {
    $raw = $_GET['invoice'];
    if (is_array($raw)) $arr = $raw;
    else $arr = explode(',', (string)$raw);
    foreach ($arr as $inv) {
        $inv = trim($inv);
        if ($inv === '') continue;
        if (preg_match('/^[A-Za-z0-9_\-]+$/', $inv)) $selectedInvoices[] = $inv;
    }
    $selectedInvoices = array_values(array_unique($selectedInvoices));
}

// Website info
$webInfoRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM website_info WHERE id=1"));
$websiteName   = $webInfoRow['name'] ?? 'Easy Tech';
$websiteLogo   = $webInfoRow['logo'] ?? '';
$websitePhone  = $webInfoRow['phone'] ?? 'N/A';
$websiteEmail  = $webInfoRow['email'] ?? 'N/A';
$websiteAddress= $webInfoRow['address'] ?? 'N/A';

// Fetch invoices
if (!empty($selectedInvoices)) {
    $placeholders = implode(',', array_fill(0, count($selectedInvoices), '?'));
    $sql = "SELECT DISTINCT invoice_no FROM order_info WHERE invoice_no IN ($placeholders) AND order_status != 'Pending' AND order_visibility = 'Show' ORDER BY invoice_no DESC";
    $stmt = $conn->prepare($sql);
    $types = str_repeat('s', count($selectedInvoices));
    $stmt->bind_param($types, ...$selectedInvoices);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT DISTINCT invoice_no FROM order_info WHERE order_status != 'Pending' AND order_visibility = 'Show' ORDER BY invoice_no DESC";
    $result = mysqli_query($conn, $sql);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>All Invoices</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css" rel="stylesheet">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
body { background: #f5f6fa; font-family: 'Poppins', sans-serif; }
.invoice-container { max-width: 900px; margin: 40px auto; background: #fff; border: 1px solid #ddd; padding: 40px 50px; border-radius: 10px; page-break-after: always; }
.table th, .table td { vertical-align: middle !important; }
.text-end { text-align: right; }
.btn-area { display: flex; justify-content: center; gap: 15px; margin-top: 30px; }
.btn-custom { font-size: 16px; padding: 10px 25px; border-radius: 6px; transition: 0.3s; }
.btn-dark { background: #000; color: #fff; }
.btn-dark:hover { background: #333; }
.btn-outline-dark { border: 1px solid #000; color: #000; }
.btn-outline-dark:hover { background: #000; color: #fff; }
/* Print Styling */
@media print {
    body { background: #fff; }
    .invoice-container {
        page-break-after: always; /* separate invoices */
        position: relative !important;
        left: 0 !important;
        top: 0 !important;
        width: auto !important;
        min-height: auto !important;
        margin: 0 auto !important;
        padding: 20px !important;
        border: none !important;
        box-shadow: none !important;
    }

    .btn-area { display: none !important; }
}
</style>
</head>
<body>

<div class="btn-area">
    <button class="btn btn-dark btn-custom" onclick="downloadPDF()">
        <i class="mdi mdi-download"></i> Download PDF
    </button>
    <button class="btn btn-outline-dark btn-custom" onclick="window.print()">
        <i class="mdi mdi-printer"></i> Print Invoices
    </button>
</div>

<div id="invoiceArea">
<?php
if ($result && ($result->num_rows ?? 0) > 0) {
    $stmtOrderHeader = $conn->prepare("SELECT * FROM order_info WHERE invoice_no = ? LIMIT 1");
    $stmtItems = $conn->prepare("SELECT * FROM order_info WHERE invoice_no = ?");
    $stmtDiscount = $conn->prepare("SELECT total_discount_amount FROM order_discount_list WHERE invoice_no = ? LIMIT 1");

    while ($r = $result->fetch_assoc()) {
        $invoice_no = $r['invoice_no'];

        $stmtOrderHeader->bind_param("s", $invoice_no);
        $stmtOrderHeader->execute();
        $hdrRes = $stmtOrderHeader->get_result();
        $order = $hdrRes->fetch_assoc();
        if (!$order) continue;

        $discount_amount = 0.0;
        $stmtDiscount->bind_param("s", $invoice_no);
        $stmtDiscount->execute();
        $dres = $stmtDiscount->get_result();
        if ($dres && $dres->num_rows > 0) {
            $drow = $dres->fetch_assoc();
            $discount_amount = (float)$drow['total_discount_amount'];
        }

        $stmtItems->bind_param("s", $invoice_no);
        $stmtItems->execute();
        $prodRes = $stmtItems->get_result();
?>
<div class="invoice-container">
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
        while($item = $prodRes->fetch_assoc()) {
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
<?php
    } // end while
    $stmtOrderHeader->close();
    $stmtItems->close();
    $stmtDiscount->close();
} else {
    echo '<p class="text-center">No invoices found.</p>';
}
?>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
async function downloadPDF() {
    const { jsPDF } = window.jspdf;
    const blocks = document.querySelectorAll(".invoice-container");
    if (!blocks.length) { alert("No invoices to download"); return; }

    const pdf = new jsPDF("p", "mm", "a4");
    const pageWidth = 210;  // A4 width in mm
    const pageHeight = 297; // A4 height in mm
    const margin = 10;

    for (let i = 0; i < blocks.length; i++) {
        // Remove page-break for canvas rendering
        blocks[i].style.pageBreakAfter = "auto";

        const canvas = await html2canvas(blocks[i], { scale: 2 });
        const imgData = canvas.toDataURL("image/png");

        // Scale image to fit inside A4 page with margins
        const imgWidth = pageWidth - 2 * margin;
        const imgHeight = (canvas.height * imgWidth) / canvas.width;

        if (i > 0) pdf.addPage();
        pdf.addImage(imgData, "PNG", margin, margin, imgWidth, imgHeight);
    }
    pdf.save("All_Invoices.pdf");
}
</script>

</body>
</html>