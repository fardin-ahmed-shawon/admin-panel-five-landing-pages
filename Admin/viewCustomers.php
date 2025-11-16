<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'View Customers'; // Set the page title
?>
<?php require 'header.php'; ?>

<!--------------------------->
<!-- START MAIN AREA -->
<!--------------------------->
<div class="content-wrapper">
            <div class="page-header">
              <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                  <i class="mdi mdi-home"></i>
                </span> Customers
              </h3>
            </div>
            <br>
            <div class="row">
              <h1>Your Customer List</h1>
              <!-- <form class="form-group" action="#">
                <input type="search" name="search" id="search" placeholder="Search Customer" class="form-control">
              </form> -->
              <!-- Table Area -->
              <div style="overflow-y: auto;">
                <table class="table table-under-bordered">
                  <tbody>
                      <tr>
                        <th>Serial No</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Customer ID</th>
                        <th>Customer Phone</th>
                        <th>Customer Email</th>
                        <th>Gender</th>
                        <!-- <th>Action</th> -->
                      </tr>
                      <?php
                      $sql = "SELECT user_id, user_fName, user_lName, user_phone, user_email, user_gender FROM user_info";
                      $result = $conn->query($sql);

                      if ($result->num_rows > 0) {
                        $serialNo = 1;
                          while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $serialNo++ . "</td>";
                            echo "<td>" . $row["user_fName"] . "</td>";
                            echo "<td>" . $row["user_lName"] . "</td>";
                            echo "<td>" . $row["user_id"] . "</td>";
                            echo "<td>" . $row["user_phone"] . "</td>";
                            echo "<td>" . $row["user_email"] . "</td>";
                            echo "<td>" . $row["user_gender"] . "</td>";
                            //echo '<td><button class="btn btn-dark">Remove</button></td>';
                            echo "</tr>";
                          }
                      } else {
                        echo "<tr><td colspan='8'>No customers found</td></tr>";
                      }
                      $conn->close();
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