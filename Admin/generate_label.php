<?php
require '../database/dbConnection.php';

$invoice_no = $_GET['invoice_no'] ?? '';

$labels = [];
$brand_name = "";

// Fetch Brand Name
$sql = "SELECT name FROM website_info LIMIT 1";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $brand_name = $row['name'];
}

if (!empty($invoice_no)) {
    if ($invoice_no === "all") {
        // Fetch all invoices with products
        $sql = "SELECT invoice_no, 
                  user_full_name, 
                  user_phone, 
                  GROUP_CONCAT(
                    CONCAT(
                      product_title,
                      CASE 
                        WHEN product_size IS NOT NULL AND product_size <> '' 
                        THEN CONCAT(' - (', product_size, ')') 
                        ELSE '' 
                      END
                    ) SEPARATOR ', '
                  ) AS products
            FROM order_info
            WHERE order_visibility = 'Show'
            GROUP BY invoice_no
            ";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            $labels[] = [
                'invoice_no' => $row['invoice_no'],
                'customer_name' => $row['user_full_name'],
                'customer_phone' => $row['user_phone'],
                'products' => $row['products']
            ];
        }
    } else {
        // Handle multiple invoice numbers with products
        $invoice_array = explode(",", $invoice_no);
        $placeholders = implode(",", array_fill(0, count($invoice_array), "?"));

        $stmt = $conn->prepare("SELECT invoice_no, user_full_name, user_phone, 
                                       GROUP_CONCAT(CONCAT(product_title, ' - (', product_size, ')') SEPARATOR ', ') AS products
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
  <title>Order Labels</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f8f9fa;
      padding: 20px;
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }

    .label {
      width: 3in;
      min-height: 2in;
      border: 1px solid #000;
      padding: 4px;
      box-sizing: border-box;
      text-align: center;
      font-size: 10px;
      background: #fff;
    }

    .brand {
      font-weight: bold;
      font-size: 11px;
    }

    .products {
      margin-top: 5px;
      font-size: 9px;
      text-align: left;
    }

    @media print {
      body {
        background: none;
        padding: 0;
        gap: 0;
        display: block;
      }
      .label {
        margin: 2px;
        border: 1px solid #000;
        page-break-inside: avoid;
        display: inline-block;
      }
      .no-print {
        display: none;
      }
    }
  </style>
</head>
<body>
  <?php if (!empty($labels)): ?>
      <?php foreach ($labels as $lbl): ?>
        <div class="label">
          <div class="brand"><?php echo htmlspecialchars($brand_name); ?></div>
          <div><?php echo htmlspecialchars($lbl['invoice_no']); ?></div>
          <hr>
          <div style="display: flex; justify-content: space-between">
            <div>Name: <?php echo htmlspecialchars($lbl['customer_name']); ?></div>
            <div>Phone: <?php echo htmlspecialchars($lbl['customer_phone']); ?></div>
          </div>
          <hr><br>
          <div class="products" style="text-align: center">
            <?php 
              $products = explode(',', $lbl['products']);
              foreach ($products as $p) {
                  echo htmlspecialchars(trim($p)) . "<br>";
              }
            ?>
          </div>

        </div>
      <?php endforeach; ?>
  <?php else: ?>
      <p>No labels found.</p>
  <?php endif; ?>

  <div class="no-print" style="width:100%; text-align:center; margin-top:20px;">
    <button onclick="window.print()">Print / Download PDF</button>
  </div>
</body>
</html>