<?php
$current_page = basename($_SERVER['PHP_SELF']);
$page_title = 'Pending Orders';
?>
<?php require 'header.php'; ?>

<?php
// Update order status to "Processing" if Accept button is pressed
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accept_invoice'])) {
  $invoice_no = $_POST['invoice_no'];

  // Update order_info table
  $update_sql = "UPDATE order_info SET order_status='Processing' WHERE invoice_no=?";
  $stmt = $conn->prepare($update_sql);
  $stmt->bind_param("s", $invoice_no);
  $stmt->execute();
  $stmt->close();

  // Update payment_info table
  $update_sql = "UPDATE payment_info SET order_status='Processing' WHERE invoice_no=?";
  $stmt = $conn->prepare($update_sql);
  $stmt->bind_param("s", $invoice_no);
  $stmt->execute();
  $stmt->close();
}
?>

<div class="content-wrapper">
  <div class="page-header">
    <h3 class="page-title">
      <span class="page-title-icon bg-gradient-primary text-white me-2">
        <i class="mdi mdi-home"></i>
      </span> Orders
    </h3>
  </div>
  <br>

  <div class="row">
    <h1>Pending Orders</h1>
    <div style="overflow-y: auto;">
      <table class="table table-under-bordered">
        <thead>
          <tr>
            <th>SL</th>
            <th>Invoice No</th>
            <th>Order No</th>
            <th>Customer Name</th>
            <th>Customer Phone</th>
            <th>Address</th>
            <th>Order Date</th>
            <th>Payment Method</th>
            <th>Status</th>
            <th>Products</th>
            <th>Total</th>
            <th colspan="2">Action</th>
          </tr>
        </thead>
        <tbody>
        <?php
        // Fetch pending orders
        $sql = "SELECT * FROM order_info 
                WHERE order_status='Pending' 
                AND order_visibility='Show' 
                ORDER BY invoice_no DESC, order_no ASC 
                LIMIT 50";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $orders = [];
            while ($row = $result->fetch_assoc()) {
                $invoice = $row['invoice_no'];
                if (!isset($orders[$invoice])) {
                    $orders[$invoice] = [
                        'invoice_no' => $invoice,
                        'user_full_name' => $row['user_full_name'],
                        'user_phone' => $row['user_phone'],
                        'user_address' => $row['user_address'],
                        'order_date' => $row['order_date'],
                        'payment_method' => $row['payment_method'],
                        'order_status' => $row['order_status'],
                        'total_price' => 0,
                        'products' => [],
                        'order_nos' => []
                    ];
                }
                $orders[$invoice]['products'][] = [
                    'title' => $row['product_title'],
                    'size' => $row['product_size'],
                    'quantity' => $row['product_quantity'],
                    'price' => $row['total_price']
                ];
                $orders[$invoice]['total_price'] += $row['total_price'];
                $orders[$invoice]['order_nos'][] = $row['order_no'];
            }

            // Display grouped orders
            $sl = 1;
            foreach ($orders as $order) {
                $orderNos = implode(", ", $order['order_nos']); // comma separated order_no
                echo "<tr>
                        <td>{$sl}</td>
                        <td>{$order['invoice_no']}</td>
                        <td>{$orderNos}</td>
                        <td>{$order['user_full_name']}</td>
                        <td>{$order['user_phone']}</td>
                        <td>{$order['user_address']}</td>
                        <td>{$order['order_date']}</td>
                        <td>{$order['payment_method']}</td>
                        <td class='text-primary'>{$order['order_status']}</td>
                        <td>
                          <ul>";
                          foreach ($order['products'] as $p) {
                              echo "<li>{$p['title']} ({$p['size']}) - Qty: {$p['quantity']} - {$p['price']} Tk</li>";
                          }
                echo      "</ul>
                        </td>
                        <td>{$order['total_price']} Tk</td>
                        <td>
                          <form method='post' action=''>
                            <input type='hidden' name='invoice_no' value='{$order['invoice_no']}'>
                            <button type='submit' name='accept_invoice' class='btn btn-dark'>Accept</button>
                          </form>
                        </td>
                        <td>
                          <a href='removeOrder.php?invoice_no={$order['invoice_no']}'>
                            <button class='btn btn-danger' onclick='return checkDelete(event)'>Declined</button>
                          </a>
                        </td>
                      </tr>";
                $sl++;
            }
        }
        ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
function checkDelete(event) {
  event.preventDefault();
  Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, decline it!'
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = event.target.closest('a').href;
    }
  });
  return false;
}
</script>
<?php require 'footer.php'; ?>