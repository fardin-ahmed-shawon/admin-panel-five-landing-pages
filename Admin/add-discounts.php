<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'Add Discount'; // Set the page title
?>
<?php require 'header.php'; ?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form inputs
    $purchase_amount = $_POST['purchase_amount'];
    $discount_amount = $_POST['discount_amount'];
    $free_shipping = isset($_POST['free_shipping']) ? 1 : 0; // Checkbox handling

    // Validate required fields
    if (empty($purchase_amount)) {
        $error_message = "All fields are required.";
    } else {
        // Prepare SQL query to insert data
        $sql = "INSERT INTO discount (purchase_amount, discount_amount, free_shipping, created_at) 
                VALUES (?, ?, ?, NOW())";

        // Use prepared statements
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $purchase_amount, $discount_amount, $free_shipping);

        // Execute the query
        if ($stmt->execute()) {
            $success_message = "Discount added successfully!";
        } else {
            $error_message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!--------------------------->
<!-- START MAIN AREA -->
<!--------------------------->
<div class="content-wrapper">
            <div class="page-header">
              <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                  <i class="mdi mdi-sale-outline"></i>
                </span> Discounts
              </h3>
            </div>
            <br>
            <div class="row">
                <div class="col-md-4 mx-auto mt-5">
                    <div class="card card-body p-4">
                        <div class="row mb-2">
                            <div class="col mt-3">
                                <h6>Add Discount</h6>
                            </div>
                        </div><hr>

                        <br>
                        <?php if (isset($success_message)): ?>
                            <div class="alert alert-success">
                                <?php echo $success_message; ?>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger">
                                <?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>

                        <form action="" method="POST">
                            <div class="form-group">
                                <label style="font-size: 17px" for="discountinput">Enter Purchase Amount *</label>
                                <input type="text" class="form-control" placeholder="Enter Purchase Amount" name="purchase_amount" required>
                            </div>

                            <div class="form-group">
                                <label style="font-size: 17px" for="discountinput">Enter Discount Amount </label>
                                <input type="text" class="form-control" id="discountinput" placeholder="Enter Discount Amount" name="discount_amount">
                            </div>

                            <div class="form-group form-check mt-3 mx-4">
                                <input type="checkbox" class="form-check-input" id="free_shipping" name="free_shipping" value="1">
                                <label class="form-check-label" for="free_shipping" style="font-size: 16px;">
                                    Free Shipping
                                </label>
                            </div>


                            <button type="submit" class="btn btn-primary">Add Discount</button>
                        </form>
                    </div>
                </div>

                <div class="col-md-7 mx-auto mt-5">
                    <div class="card card-body">
                      <div class="row mb-2">
                            <div class="col mt-3">
                                <h6 class="text-center">Discount List</h6>
                            </div>
                        </div><hr>

                        <div class="table-responsive">
                          <table class="table table-bordered">
                              <thead>
    <tr>
        <th>#</th>
        <th>Purchase Amount</th>
        <th>Discount Amount</th>
        <th>Free Shipping</th>
        <th>Created At</th>
        <th>Actions</th>
    </tr>
</thead>
<tbody>
    <?php
    $result = $conn->query("SELECT * FROM discount ORDER BY id DESC");
    if ($result && $result->num_rows > 0):
        $i = 1;
        while ($row = $result->fetch_assoc()):
    ?>
    <tr>
        <td><?php echo $i++; ?></td>
        <td><?php echo htmlspecialchars($row['purchase_amount']); ?></td>
        <td><?php echo htmlspecialchars($row['discount_amount']); ?></td>
        <td>
            <?php echo $row['free_shipping'] == 1 ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>'; ?>
        </td>
        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
        <td>
            <a href="edit-discount.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">Edit</a>
            <br>
            <a href="javascript:void(0);" class="btn btn-sm btn-danger delete-discount" data-id="<?php echo $row['id']; ?>">Delete</a>
        </td>
    </tr>
    <?php endwhile; else: ?>
    <tr>
        <td colspan="6" class="text-center">No discounts found.</td>
    </tr>
    <?php endif; ?>
</tbody>

                          </table>
                        </div>
                    </div>
                </div>
            </div>
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->

<script>
    // SweetAlert and AJAX for Delete
    document.querySelectorAll('.delete-discount').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var discountId = this.getAttribute('data-id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send AJAX request to delete the discount
                    fetch('delete-discount-ajax.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ id: discountId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire('Deleted!', data.message, 'success');
                            // Remove the row from the table
                            btn.closest('tr').remove();
                        } else {
                            Swal.fire('Error!', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error!', 'Something went wrong.', 'error');
                    });
                }
            });
        });
    });
</script>
<?php require 'footer.php'; ?>