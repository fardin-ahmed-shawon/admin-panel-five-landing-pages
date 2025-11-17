<?php
require '../database/dbConnection.php';

$invoice_no = $_GET['invoice_no'] ?? '';

$labels = [];
$brand_name = "";
$brand_phone = "";
$brand_address = "";

// Fetch Brand Info
$sql = "SELECT name, phone, address FROM website_info LIMIT 1";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $brand_name = $row['name'];
    $brand_phone = $row['phone'] ?? '';
    $brand_address = $row['address'] ?? '';
}

if (!empty($invoice_no)) {
    if ($invoice_no === "all") {
        $sql = "SELECT invoice_no, 
                  user_full_name, 
                  user_phone, 
                  user_address,
                  city_address,
                  order_date,
                  GROUP_CONCAT(
                    CONCAT(
                      product_title,
                      CASE 
                        WHEN product_size IS NOT NULL AND product_size <> '' 
                        THEN CONCAT(' - (', product_size, ')') 
                        ELSE '' 
                      END,
                      ' x', product_quantity
                    ) SEPARATOR ', '
                  ) AS products
            FROM order_info
            WHERE order_visibility = 'Show'
            GROUP BY invoice_no";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            $labels[] = [
                'invoice_no' => $row['invoice_no'],
                'customer_name' => $row['user_full_name'],
                'customer_phone' => $row['user_phone'],
                'customer_address' => $row['user_address'],
                'city_address' => $row['city_address'],
                'order_date' => $row['order_date'],
                'products' => $row['products']
            ];
        }
    } else {
        $invoice_array = explode(",", $invoice_no);
        $placeholders = implode(",", array_fill(0, count($invoice_array), "?"));
        $stmt = $conn->prepare("SELECT invoice_no, user_full_name, user_phone, user_address, city_address, order_date,
                                       GROUP_CONCAT(CONCAT(product_title, 
                                       CASE 
                                         WHEN product_size IS NOT NULL AND product_size <> '' 
                                         THEN CONCAT(' - (', product_size, ')') 
                                         ELSE '' 
                                       END,
                                       ' x', product_quantity) SEPARATOR ', ') AS products
                                FROM order_info 
                                WHERE invoice_no IN ($placeholders)
                                GROUP BY invoice_no");
        $types = str_repeat("s", count($invoice_array));
        $stmt->bind_param($types, ...$invoice_array);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $labels[] = [
                'invoice_no' => $row['invoice_no'],
                'customer_name' => $row['user_full_name'],
                'customer_phone' => $row['user_phone'],
                'customer_address' => $row['user_address'],
                'city_address' => $row['city_address'],
                'order_date' => $row['order_date'],
                'products' => $row['products']
            ];
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shipping Labels</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Roboto', Arial, sans-serif;
      background: #e9ecef;
      padding: 20px;
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      justify-content: center;
    }
    .label {
      width: 4in;
      height: 6in;
      border: 2px solid #000;
      background: #fff;
      padding: 0;
      font-size: 11px;
      position: relative;
      overflow: hidden;
      page-break-inside: avoid;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .label-header { background: #000; color: #fff; padding: 12px 15px; text-align: center; border-bottom: 3px solid #f8f9fa; }
    .brand-name { font-size: 18px; font-weight: 700; letter-spacing: 1px; margin-bottom: 3px; }
    .brand-info { font-size: 9px; opacity: 0.9; line-height: 1.3; }
    .label-body { padding: 15px; }
    .section { margin-bottom: 12px; padding-bottom: 10px; border-bottom: 1px dashed #ddd; }
    .section:last-child { border-bottom: none; margin-bottom: 0; }
    .section-title { font-size: 10px; font-weight: 700; color: #666; text-transform: uppercase; margin-bottom: 6px; letter-spacing: 0.5px; }
    .invoice-box { background: #f8f9fa; padding: 8px; text-align: center; border: 2px dashed #000; margin-bottom: 12px; }
    .invoice-label { font-size: 9px; color: #666; font-weight: 500; }
    .invoice-number { font-size: 16px; font-weight: 700; color: #000; letter-spacing: 1px; }
    .delivery-tag { display: inline-block; background: #000; color: #fff; padding: 4px 10px; font-size: 10px; font-weight: 700; border-radius: 3px; margin-bottom: 8px; }
    .customer-name { font-size: 13px; font-weight: 700; color: #000; margin-bottom: 5px; }
    .customer-phone { font-size: 13px; font-weight: 700; color: #000; }
    .address-box { background: #f8f9fa; padding: 10px; border-left: 4px solid #000; font-size: 11px; line-height: 1.6; min-height: 60px; }
    .products-list { max-height: 120px; overflow-y: auto; font-size: 10px; line-height: 1.6; padding: 8px; background: #f8f9fa; border-radius: 3px; }
    .product-item { padding: 3px 0; border-bottom: 1px dotted #ddd; }
    .product-item:last-child { border-bottom: none; }
    .label-footer { position: absolute; bottom: 0; left: 0; right: 0; background: #f8f9fa; padding: 8px 15px; border-top: 2px solid #000; display: flex; justify-content: space-between; align-items: center; font-size: 9px; }
    .date-stamp { font-weight: 500; color: #666; }
    .barcode-placeholder { font-family: 'Courier New', monospace; font-size: 20px; font-weight: 700; letter-spacing: 2px; }
    .print-button, .download-button {
      position: fixed; bottom: 30px; right: 30px; background: #000; color: #fff; border: none;
      padding: 15px 30px; font-size: 16px; font-weight: 600; cursor: pointer;
      border-radius: 5px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); z-index: 1000;
      transition: all 0.3s; display: flex; align-items: center; gap: 8px;
    }
    .download-button { right: 200px; background: #007bff; }
    .download-button:hover { background: #0056b3; }
    .print-button:hover { background: #333; transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,0.4); }
    @media print {
      body { background: none; padding: 0; gap: 0; }
      .label { margin: 0; box-shadow: none; page-break-after: always; }
      .print-button, .download-button { display: none; }
      @page { margin: 0; size: 4in 6in; }
    }
  </style>
</head>
<body>
  <?php if (!empty($labels)): ?>
      <?php foreach ($labels as $lbl): ?>
        <div class="label">
          <div class="label-header">
            <div class="brand-name"><?php echo htmlspecialchars($brand_name); ?></div>
            <div class="brand-info">
              <?php if(!empty($brand_phone)) echo htmlspecialchars($brand_phone); ?>
              <?php if(!empty($brand_phone) && !empty($brand_address)) echo ' | '; ?>
              <?php if(!empty($brand_address)) echo htmlspecialchars($brand_address); ?>
            </div>
          </div>

          <div class="label-body">
            <div class="invoice-box">
              <div class="invoice-label">INVOICE NO</div>
              <div class="invoice-number"><?php echo htmlspecialchars($lbl['invoice_no']); ?></div>
            </div>
            <div class="section">
              <div class="section-title">
                <?php if(!empty($lbl['city_address'])): ?>
                  <span class="delivery-tag"><?php echo htmlspecialchars($lbl['city_address']); ?></span>
                <?php else: ?>
                  <span class="delivery-tag">DELIVERY</span>
                <?php endif; ?>
              </div>
              <div class="customer-name"><?php echo htmlspecialchars($lbl['customer_name']); ?></div>
              <div class="customer-phone"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($lbl['customer_phone']); ?></div>
            </div>
            <div class="section">
              <div class="section-title"><i class="fas fa-map-marker-alt"></i> Delivery Address</div>
              <div class="address-box"><?php echo nl2br(htmlspecialchars($lbl['customer_address'])); ?></div>
            </div>
            <div class="section">
              <div class="section-title"><i class="fas fa-box"></i> Items in Package</div>
              <div class="products-list">
                <?php 
                  $products = explode(',', $lbl['products']);
                  foreach ($products as $p) {
                      echo '<div class="product-item">• ' . htmlspecialchars(trim($p)) . '</div>';
                  }
                ?>
              </div>
            </div>
          </div>

          <div class="label-footer">
            <div class="date-stamp"><?php echo date('d M Y', strtotime($lbl['order_date'])); ?></div>
            <div class="barcode-placeholder">|||||||||||</div>
          </div>
        </div>
      <?php endforeach; ?>
  <?php else: ?>
      <div style="width: 100%; text-align: center; padding: 40px; background: #fff; border-radius: 8px;">
        <h2>No labels found</h2>
        <p>Please provide a valid invoice number.</p>
      </div>
  <?php endif; ?>

  <button class="download-button" id="downloadPDF"><i class="fas fa-file-pdf"></i> Download PDF</button>
  <button class="print-button" onclick="window.print()"><i class="fas fa-print"></i> Print Labels</button>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script>
    document.getElementById('downloadPDF').addEventListener('click', async () => {
      const { jsPDF } = window.jspdf;
      const labels = document.querySelectorAll('.label');
      const pdf = new jsPDF({ orientation: 'portrait', unit: 'in', format: [4, 6] });

      for (let i = 0; i < labels.length; i++) {
        const canvas = await html2canvas(labels[i], { scale: 2 });
        const imgData = canvas.toDataURL('image/png');
        if (i > 0) pdf.addPage();
        pdf.addImage(imgData, 'PNG', 0, 0, 4, 6);
      }

      pdf.save('Shipping_Labels.pdf');
    });
  </script>
</body>
</html>