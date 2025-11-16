<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'View Coupons'; // Set the page title
?>
<?php require 'header.php'; ?>

<?php

// Handle AJAX delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $stmt = $conn->prepare("DELETE FROM coupon WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $success = $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => $success]);
    exit;
}

// Fetch all coupons
$result = $conn->query("SELECT * FROM coupon ORDER BY id DESC");
?>

<!--------------------------->
<!-- START MAIN AREA -->
<!--------------------------->
<div class="content-wrapper">
                <div class="page-header">
                    <h3 class="page-title">
                        <span class="page-title-icon bg-gradient-primary text-white me-2">
                            <i class="mdi mdi-ticket-percent"></i>
                        </span> Coupons List
                    </h3>
                </div>
                <div class="row card w-100 mx-auto mt-5">
                    <div class="card-body"><br>
                        <h6 class="text-center">All Coupons</h6>
                        <br><hr><br>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Coupon Name</th>
                                        <th>Coupon Code</th>
                                        <th>Discount</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result->num_rows > 0): ?>
                                        <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                                            <tr id="row-<?php echo $row['id']; ?>">
                                                <td><?php echo $i++; ?></td>
                                                <td><?php echo htmlspecialchars($row['coupon_name']); ?></td>
                                                <td><?php echo htmlspecialchars($row['coupon_code']); ?></td>
                                                <td><?php echo htmlspecialchars($row['coupon_discount']); ?> %</td>
                                                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                                <td>
                                                    <a href="edit-coupons.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm">
                                                        <i class="mdi mdi-pencil"></i> Edit
                                                    </a><br>
                                                    <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $row['id']; ?>">
                                                        <i class="mdi mdi-delete"></i> Delete
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="6" class="text-center">No coupons found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->
<script>
    document.querySelectorAll('.delete-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var couponId = this.getAttribute('data-id');
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
                    fetch('', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: 'delete_id=' + couponId
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('row-' + couponId).remove();
                            Swal.fire('Deleted!', 'Coupon has been deleted.', 'success');
                        } else {
                            Swal.fire('Error!', 'Failed to delete coupon.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
<?php require 'footer.php'; ?>