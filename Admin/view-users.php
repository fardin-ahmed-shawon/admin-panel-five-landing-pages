<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'View Users'; // Set the page title
require 'header.php';

// Only allow access to this page if the user is an Admin
if ($_SESSION['role'] != 'Admin') {
    echo "<script>alert('Access denied.'); window.location.href='404.php';</script>";
    exit();
}

// Fetch all admins with their roles
$sql = "SELECT a.admin_id, a.admin_username, a.admin_picture, r.role_name, a.created_at
        FROM admin_info a
        LEFT JOIN roles r ON a.role_id = r.id
        ORDER BY a.admin_id ASC";
$result = $conn->query($sql);
?>

<!--------------------------->
<!-- START MAIN AREA -->
<!--------------------------->
<div class="content-wrapper">
  <div class="row">
    <div class="col-12 grid-margin">
      <div class="card p-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="mb-4">User List</h1>
            <a href="add-user.php" class="btn btn-gradient-primary"><span class="mdi mdi-plus-box"></span> Add New User</a>
        </div>
        <div class="card-body px-0 mx-0">
          <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                  <th>#</th>
                  <th>Username</th>
                  <th>Role</th>
                  <th>Created At</th>
                  <th colspan="2">Action</th>
                </tr>
              <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                  <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                    <tr>
                      <td><?= $i++; ?></td>
                      <td><?= htmlspecialchars($row['admin_username']); ?></td>
                      <td><?= htmlspecialchars($row['role_name']); ?></td>
                      <td><?= htmlspecialchars($row['created_at']); ?></td>
                      <td><a href="change-role.php?id=<?= htmlspecialchars($row['admin_id']); ?>" class="btn btn-info">Change Role</a></td>
                      <td>
                        <a href="delete-user.php?id=<?= htmlspecialchars($row['admin_id']); ?>" 
                          class="btn btn-danger delete-user-btn">Delete</a>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="5" class="text-center">No users found.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->
<script>
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.delete-user-btn').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const url = this.getAttribute('href');
      Swal.fire({
        title: 'Are you sure?',
        text: "This user will be deleted!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = url;
        }
      });
    });
  });
});
</script>
<?php require 'footer.php'; ?>