<?php
// all-invoice.php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
include 'database/dbConnection.php';

// Get optional `invoice[]` from GET (array or comma-separated string). Sanitize.
$selectedInvoices = [];
if (isset($_GET['invoice'])) {
    $raw = $_GET['invoice'];
    if (is_array($raw)) {
        $arr = $raw;
    } else {
        // comma separated?
        $arr = explode(',', (string)$raw);
    }
    foreach ($arr as $inv) {
        $inv = trim($inv);
        if ($inv === '') continue;
        // allow alphanumeric, dash, underscore
        if (preg_match('/^[A-Za-z0-9_\-]+$/', $inv)) {
            $selectedInvoices[] = $inv;
        }
    }
    $selectedInvoices = array_values(array_unique($selectedInvoices));
}

// Preload website_info once
$webInfoRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM website_info WHERE id=1"));
$websiteName   = $webInfoRow['name'] ?? 'Easy Tech';
$websiteLogo   = $webInfoRow['logo'] ?? '';
$websitePhone  = $webInfoRow['phone'] ?? 'N/A';
$websiteEmail  = $webInfoRow['email'] ?? 'N/A';
$websiteAddress= $webInfoRow['address'] ?? 'N/A';
$insideCharge  = isset($webInfoRow['inside_delivery_charge']) ? (int)$webInfoRow['inside_delivery_charge'] : 80;
$outsideCharge = isset($webInfoRow['outside_delivery_charge']) ? (int)$webInfoRow['outside_delivery_charge'] : 150;

// Build SQL to fetch distinct invoice_no
if (!empty($selectedInvoices)) {
    // prepare placeholders
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
  <title>All Invoices</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
    * { font-family: 'Poppins', sans-serif; }
    .container { max-width: 1100px; margin: 0 auto; padding:20px; }
    .invoice-block { padding: 30px; margin-bottom: 50px; border: 1px solid #ddd; page-break-after: always; background:#fff; }
    .btn-dark { font-size: 16px; padding: 10px 25px; color:#fff; background-color:#000; border:none; }
    .btn-dark:hover { background:#222; }
    .toolbar { margin-bottom: 20px; }
  </style>
</head>
<body style="background:#f7f8fa;">
<div class="container">
    
  <div class="toolbar clearfix" style="display: none">
    <div class="pull-left">
      <h3 style="margin:0">All Invoices</h3>
      <?php if (!empty($selectedInvoices)): ?>
        <div style="color:#666; font-size:13px;">Showing selected invoices: <?php echo htmlspecialchars(implode(', ', $selectedInvoices)); ?> — <a href="all-invoice.php">Show all</a></div>
      <?php else: ?>
        <div style="color:#666; font-size:13px;">Showing all invoices</div>
      <?php endif; ?>
    </div>
    <div class="pull-right">
      <a href="makeInvoice.php" class="btn btn-default">Back to Make Invoice</a>
      <button class="btn btn-dark" onclick="window.print()"><span class="mdi mdi-printer"></span> Print</button>
      <button id="downloadPdfBtn" class="btn btn-success">Download Selected PDF</button>
    </div>
  </div>
  

  <div id="invoiceArea">
  <?php
  if ($result && $result->num_rows > 0) {
      // Prepared statements for items & discount + header
      $stmtOrderHeader = $conn->prepare("SELECT * FROM order_info WHERE invoice_no = ? LIMIT 1");
      $stmtItems = $conn->prepare("SELECT * FROM order_info WHERE invoice_no = ?");
      $stmtDiscount = $conn->prepare("SELECT total_discount_amount FROM order_discount_list WHERE invoice_no = ? LIMIT 1");

      while ($r = $result->fetch_assoc()) {
          $invoice_no = $r['invoice_no'];

          // header row
          $stmtOrderHeader->bind_param("s", $invoice_no);
          $stmtOrderHeader->execute();
          $hdrRes = $stmtOrderHeader->get_result();
          $order = $hdrRes->fetch_assoc();
          if (!$order) continue;

          // discount
          $discount_amount = 0.0;
          $stmtDiscount->bind_param("s", $invoice_no);
          $stmtDiscount->execute();
          $dres = $stmtDiscount->get_result();
          if ($dres && $dres->num_rows > 0) {
              $drow = $dres->fetch_assoc();
              $discount_amount = (float)$drow['total_discount_amount'];
          }

          // items
          $stmtItems->bind_param("s", $invoice_no);
          $stmtItems->execute();
          $prodRes = $stmtItems->get_result();

          echo '<div class="invoice-block">';
          echo '<div style="text-align:center;">';
          if (!empty($websiteLogo)) {
              echo '<img src="' . htmlspecialchars($websiteLogo) . '" style="width:100px;" alt="Logo"><br>';
          }
          echo '<h2 style="margin:8px 0 4px;">Invoice</h2>';
          echo '<h4 style="margin:0 0 6px;">Invoice No: ' . htmlspecialchars($order['invoice_no']) . '</h4>';
          echo '</div><hr>';

          echo '<div class="row">';
          echo '<div class="col-xs-6"><strong>FROM:</strong><br>';
          echo htmlspecialchars($websiteName) . '<br>';
          echo htmlspecialchars($websitePhone) . '<br>';
          echo htmlspecialchars($websiteEmail) . '<br>';
          echo nl2br(htmlspecialchars($websiteAddress));
          echo '</div>';

          echo '<div class="col-xs-6 text-right">';
          echo '<strong>Billed To:</strong><br>';
          echo htmlspecialchars($order['user_full_name']) . '<br>';
          echo htmlspecialchars($order['user_phone']) . '<br>';
          echo htmlspecialchars($order['user_email']) . '<br>';
          echo nl2br(htmlspecialchars($order['user_address']));
          echo '</div></div><br>';

          echo '<div class="row"><div class="col-xs-6"><strong>Payment Method:</strong><br>' . htmlspecialchars($order['payment_method']) . '</div>';
          echo '<div class="col-xs-6 text-right"><strong>Order Date:</strong><br>' . ($order['order_date'] ? date("F j, Y", strtotime($order['order_date'])) : 'N/A') . '</div></div><br>';

          // items table
          echo '<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>Item</th><th>Price</th><th>Qty</th><th>Size</th><th>Total</th></tr></thead><tbody>';
          $subtotals = 0.0;
          while ($item = $prodRes->fetch_assoc()) {
              $qty = max(1, (int)$item['product_quantity']);
              $unit_price = (float)$item['total_price'] / $qty;
              $subtotals += (float)$item['total_price'];

              echo '<tr>';
              echo '<td>' . htmlspecialchars($item['product_title']) . '</td>';
              echo '<td class="text-right">৳ ' . number_format($unit_price, 2) . '</td>';
              echo '<td class="text-center">' . intval($item['product_quantity']) . '</td>';
              echo '<td>' . htmlspecialchars($item['product_size']) . '</td>';
              echo '<td class="text-right">৳ ' . number_format((float)$item['total_price'], 2) . '</td>';
              echo '</tr>';
          }

          $shipping = number_format((float)find_shipping_charge($invoice_no), 2);
          $total = $subtotals + $shipping - $discount_amount;

          echo '<tr><td colspan="4" class="text-right"><strong>Subtotal</strong></td><td class="text-right">৳ ' . number_format($subtotals, 2) . '</td></tr>';
          echo '<tr><td colspan="4" class="text-right"><strong>Shipping</strong></td><td class="text-right">৳ ' . number_format($shipping, 2) . '</td></tr>';
          echo '<tr><td colspan="4" class="text-right"><strong>Discount</strong></td><td class="text-right">৳ ' . number_format($discount_amount, 2) . '</td></tr>';
          echo '<tr><td colspan="4" class="text-right"><strong>Total</strong></td><td class="text-right"><strong>৳ ' . number_format($total, 2) . '</strong></td></tr>';

          echo '</tbody></table></div>'; // table + responsive

          echo '</div>'; // invoice-block end
      } // end while invoices

      // close statements
      if (isset($stmtOrderHeader)) $stmtOrderHeader->close();
      if (isset($stmtItems)) $stmtItems->close();
      if (isset($stmtDiscount)) $stmtDiscount->close();

  } else {
      echo '<p class="text-center">No invoices found.</p>';
  }

  // close result & any prepared
  if (isset($stmt) && $stmt) {
      $stmt->close();
  } elseif (isset($result) && $result) {
      // result was mysqli_result
  }
  ?>
  </div> <!-- invoiceArea -->
</div> <!-- container -->

<!-- Scripts -->
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
document.getElementById('downloadPdfBtn').addEventListener('click', async function() {
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF('p', 'mm', 'a4');
    const invoiceBlocks = document.querySelectorAll('.invoice-block');
    if (!invoiceBlocks.length) {
        alert('No invoices to download.');
        return;
    }

    let pageHeight = pdf.internal.pageSize.getHeight();
    let margin = 10;
    let y = margin;
    for (let i = 0; i < invoiceBlocks.length; i++) {
        const block = invoiceBlocks[i];
        // render to canvas
        const canvas = await html2canvas(block, { scale: 2 });
        const imgData = canvas.toDataURL('image/png');
        const imgProps = pdf.getImageProperties(imgData);
        const pdfWidth = pdf.internal.pageSize.getWidth() - margin * 2;
        const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

        if (i > 0) pdf.addPage();
        pdf.addImage(imgData, 'PNG', margin, margin, pdfWidth, pdfHeight);
    }

    pdf.save('Selected_Invoices.pdf');
});
</script>

</body>
</html>