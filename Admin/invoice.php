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
$websiteLogo   = $webInfoRow['logo'] ?? '';
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
<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
* { font-family: 'Poppins', sans-serif; }
body { background:#f7f8fa; }
.container { max-width: 900px; margin: 20px auto; padding:20px; }
.invoice-block { padding: 30px; border:1px solid #ddd; background:#fff; }
.table th, .table td { vertical-align: middle !important; }
.btn-dark { font-size: 16px; padding: 10px 25px; color:#fff; background-color:#000; border:none; margin-top:10px; }
.btn-dark:hover { background:#222; }
</style>
</head>
<body>

<div class="container">
    <div class="invoice-block">
        <div style="text-align:center;">
            <?php if(!empty($websiteLogo)) echo '<img src="'.htmlspecialchars($websiteLogo).'" style="width:100px;">'; ?>
            <h2 style="margin:8px 0 4px;">Invoice</h2>
            <h4 style="margin:0 0 6px;">Invoice No: <?php echo htmlspecialchars($order['invoice_no']); ?></h4>
        </div>
        <hr>
        <div class="row">
            <div class="col-xs-6">
                <strong>FROM:</strong><br>
                <?php echo htmlspecialchars($websiteName); ?><br>
                <?php echo htmlspecialchars($websitePhone); ?><br>
                <?php echo htmlspecialchars($websiteEmail); ?><br>
                <?php echo nl2br(htmlspecialchars($websiteAddress)); ?>
            </div>
            <div class="col-xs-6 text-right">
                <strong>Billed To:</strong><br>
                <?php echo htmlspecialchars($order['user_full_name']); ?><br>
                <?php echo htmlspecialchars($order['user_phone']); ?><br>
                <?php echo htmlspecialchars($order['user_email']); ?><br>
                <?php echo nl2br(htmlspecialchars($order['user_address'])); ?>
            </div>
        </div><br>

        <div class="row">
            <div class="col-xs-6"><strong>Payment Method:</strong><br><?php echo htmlspecialchars($order['payment_method']); ?></div>
            <div class="col-xs-6 text-right"><strong>Order Date:</strong><br><?php echo date("F j, Y", strtotime($order['order_date'])); ?></div>
        </div><br>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Size</th>
                        <th>Total</th>
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
                            <td class="text-right">৳ '.number_format($unit_price,2).'</td>
                            <td class="text-center">'.intval($qty).'</td>
                            <td>'.htmlspecialchars($item['product_size']).'</td>
                            <td class="text-right">৳ '.number_format((float)$item['total_price'],2).'</td>
                          </tr>';
                }

                $shipping = number_format((float)find_shipping_charge($invoice_no), 2);


                $total = $subtotal + $shipping - $discount_amount;
                ?>
                <tr><td colspan="4" class="text-right"><strong>Subtotal</strong></td><td class="text-right">৳ <?php echo number_format($subtotal,2); ?></td></tr>
                <tr><td colspan="4" class="text-right"><strong>Shipping</strong></td><td class="text-right">৳ <?php echo number_format($shipping,2); ?></td></tr>
                <tr><td colspan="4" class="text-right"><strong>Discount</strong></td><td class="text-right">৳ <?php echo number_format($discount_amount,2); ?></td></tr>
                <tr><td colspan="4" class="text-right"><strong>Total</strong></td><td class="text-right"><strong>৳ <?php echo number_format($total,2); ?></strong></td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <button class="btn btn-dark" onclick="downloadPDF()">Download & Print <span class="mdi mdi-tray-arrow-down"></span></button>
</div>

<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
async function downloadPDF() {
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF('p','mm','a4');
    const element = document.querySelector('.invoice-block');
    const canvas = await html2canvas(element, { scale:2 });
    const imgData = canvas.toDataURL('image/png');
    const pdfWidth = 190;
    const pdfHeight = (canvas.height * pdfWidth) / canvas.width;
    pdf.addImage(imgData,'PNG',10,10,pdfWidth,pdfHeight);
    pdf.save('Invoice_<?php echo htmlspecialchars($invoice_no); ?>.pdf');
}
</script>
</body>
</html>