<?php
$current_page = basename($_SERVER['PHP_SELF']);
$page_title = 'View Orders';
?>
<?php require 'header.php'; ?>

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
    <h1>Active Orders</h1>
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
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $sql = "SELECT * FROM order_info 
                WHERE order_status!='Pending' 
                AND order_visibility='Show' 
                ORDER BY invoice_no DESC, order_no ASC";
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
                        'order_nos' => [] // store multiple order_no
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
                $orderNos = implode(", ", $order['order_nos']); // join order_no with comma
                echo "<tr>
                        <td>{$sl}</td>
                        <td>{$order['invoice_no']}</td>
                        <td>{$orderNos}</td>
                        <td>{$order['user_full_name']}</td>
                        <td>{$order['user_phone']}</td>
                        <td>{$order['user_address']}</td>
                        <td>{$order['order_date']}</td>
                        <td>{$order['payment_method']}</td>
                        <td class='order-status'>{$order['order_status']}</td>
                        <td>
                          <ul>";
                          foreach ($order['products'] as $p) {
                              echo "<li>{$p['title']} ({$p['size']}) - Qty: {$p['quantity']} - {$p['price']} Tk</li>";
                          }
                echo      "</ul>
                        </td>
                        <td>{$order['total_price']} Tk</td>
                        <td>
                          <a href='removeOrder.php?invoice_no={$order['invoice_no']}'>
                            <button class='btn btn-danger' onclick='return checkDelete(event)'>Remove</button>
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
    confirmButtonText: 'Yes, remove it!'
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = event.target.closest('a').href;
    }
  });
  return false;
}
</script>
<?php require 'footer.php'; ?>