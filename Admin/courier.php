<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'Courier'; // Set the page title
?>
<?php require 'header.php'; ?>

<!--------------------------->
<!-- START MAIN AREA -->
<!--------------------------->
<div class="content-wrapper">
            <div class="page-header">
              <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                  <i class="mdi mdi-truck-outline"></i>
                </span> Courier
              </h3>
            </div>
            <br>

            <div style="display: flex; gap: 20px; justify-content: center; margin-bottom: 50px">
              <img src="img/steadfast.svg" alt="">
              <h2 style="font-weight: 600; border-left: 3px solid #34a487;" class="m-0 py-1 px-4 ">Courier Partner</h2>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                  <h1 class="m-0 py-2">Active Orders</h1>
                  <h4>- Choose Your Order & Send to Steadfast as Consignment</h4>
                </div>
                <a href="steadfast-api.php" class="btn btn-dark">API Setup <span class="mdi mdi-share-variant"></span></a>
            </div>

            <div class="row">
              
              <br><br>
              <!-- Table Area -->
              <div style="overflow-y: auto;">
                <table class="table table-under-bordered">
                  <tbody>
                    <tr>
                      <th>SL No</th>
                      <th>Invoice No</th>
                      <th>Customer Phone</th>
                      <th>Order Amount</th>
                      <th>Order Date</th>
                      <th>Payment Method</th>
                      <th>Status</th>
                      <th>Parcel Status</th>
                      <th colspan="2">Action</th>
                    </tr>
                    
                    <?php
                    // Fetch unique invoice_no and sum total_price grouped by invoice
                    $sql = "
                      SELECT 
                        invoice_no,
                        user_phone,
                        payment_method,
                        order_status,
                        order_date,
                        SUM(total_price) AS total_price
                      FROM order_info
                      WHERE order_status != 'Pending' AND order_visibility = 'Show'
                      GROUP BY invoice_no
                      ORDER BY MAX(order_no) DESC
                    ";

                    $result = $conn->query($sql);
                    $sl = 1;

                    if ($result->num_rows > 0) {
                      while ($row = $result->fetch_assoc()) {

                        // Fetch courier tracking_code & get parcel status
                        $invoice_no = $row['invoice_no'];

                        $sql2 = "SELECT tracking_code FROM parcel_info WHERE invoice_no   = '$invoice_no'";
                        $result2 = $conn->query($sql2);
                        $row2 = $result2->num_rows;

                        if ($row2 > 0) {
                          $data = $result2->fetch_assoc();

                          $is_tracking_code_set = 1;
                          $tracking_code = $data['tracking_code'];

                          $parcel_status = track_parcel($tracking_code);

                        } else {

                          $is_tracking_code_set = 0;
                          $parcel_status = 'Not Added';
                          
                        }
                        // End

                        echo "<tr>
                          <td>{$sl}</td>
                          <td>{$row['invoice_no']}</td>
                          <td>{$row['user_phone']}</td>
                          <td>{$row['total_price']} Tk</td>
                          <td>{$row['order_date']}</td>
                          <td>{$row['payment_method']}</td>
                          <td class='order-status'>{$row['order_status']}</td>
                          <td class='text-info'>{$parcel_status}</td>
                          ";

                          if ($is_tracking_code_set == 0) {
                            echo"
                            <td>
                                <a href='steadfast_entry.php?invoice_no={$row['invoice_no']}'>
                                  <button class='btn btn-primary'>Send to Steadfast <span class='mdi mdi-send'></span></button>
                                </a>
                              </td>
                            ";
                          } else {
                            echo"
                              <td>
                                <span class='btn btn-muted'>
                                  Consignment Sent <span class='mdi mdi-send-check'></span>
                                </span>
                              </td>
                              ";
                          }
                          

                          echo "
                          <td>
                            <a href='order_details.php?invoice_no={$row['invoice_no']}'>
                              <button class='btn btn-info'>View Details <span class='mdi mdi-details'></span></button>
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
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->

<?php require 'footer.php'; ?>