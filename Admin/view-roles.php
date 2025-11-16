<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'View Roles'; // Set the page title
require 'header.php';

// Only allow access to this page if the user is an Admin
if ($_SESSION['role'] != 'Admin') {
    echo "<script>alert('Access denied.'); window.location.href='404.php';</script>";
    exit();
}

// Fetch all roles and their access
$roles = [];
$pages = [
    'dashboard', 'product', 'categories', 'slider', 'banner',
    'discounts', 'coupons', 'customers', 'orders', 'payments',
    'accounts', 'inventory', 'invoice', 'courier', 'history', 'settings'
];

// Fix: Use roles.id as role_id for consistency with your schema
$sql = "SELECT r.id AS role_id, r.role_name, pa.*
        FROM roles r
        LEFT JOIN page_access pa ON r.id = pa.role_id
        ORDER BY r.id ASC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $roles[] = $row;
    }
}
?>

<!--------------------------->
<!-- START MAIN AREA -->
<!--------------------------->
<div class="content-wrapper">
    <div class="row">
        <div class="p-4 col-md-12 mx-auto" style="border-radius: 0;">
            <div>
                <h1 class="mb-3">Roles</h1>
                <div class="row">
                    <div class="col-md-6">
                        <ul>
                            <li>Dashboard and Settings have limited access for other users.</li>
                            <li>Admin has full access to all pages.</li>
                        </ul>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="add-role.php" class="btn btn-gradient-primary"><span class="mdi mdi-plus-box"></span> Add New Role</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                            <tr>
                                <th>#</th>
                                <th>Role Name</th>
                                <?php foreach ($pages as $page): ?>
                                    <th><?= ucfirst($page) ?></th>
                                <?php endforeach; ?>
                                <th>Actions</th>
                            </tr>
                        <tbody>
                        <?php if (count($roles) > 0): ?>
                            <?php foreach ($roles as $i => $role): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= htmlspecialchars($role['role_name']) ?></td>
                                    <?php foreach ($pages as $page): ?>
                                        <td>
                                            <?php if (isset($role[$page]) && $role[$page]): ?>
                                                <span class="badge bg-success">✔</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">✖</span>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>
                                    <td>
                                        <a href="edit-role.php?id=<?= $role['role_id'] ?>" class="btn btn-sm btn-dark">Edit</a>
                                        <a href="javascript:void(0);" class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $role['role_id'] ?>)">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="<?= count($pages) + 3 ?>" class="text-center">No roles found.</td>
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
function confirmDelete(roleId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This will permanently delete the role.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'delete-role.php?id=' + roleId;
        }
    });
}
</script>
<?php require 'footer.php'; ?>