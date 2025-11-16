<?php
$current_page = basename($_SERVER['PHP_SELF']);
$page_title = 'Edit Role';
require 'header.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: view-roles.php');
    exit;
}

$role_id = intval($_GET['id']);
$role = null;
$access = [];

// Fetch the role and access details
$sql = "SELECT r.role_name, pa.*
        FROM roles r
        LEFT JOIN page_access pa ON r.id = pa.role_id
        WHERE r.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $role_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $role = $result->fetch_assoc();
    $access = $role;
} else {
    header('Location: view-roles.php');
    exit;
}

$pages = [
    'dashboard', 'product', 'categories', 'slider', 'banner',
    'discounts', 'coupons', 'customers', 'orders', 'payments',
    'accounts', 'inventory', 'invoice', 'courier', 'history', 'settings'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role_name = trim($_POST['role_name']);
    $values = [];
    foreach ($pages as $page) {
        $values[$page] = isset($_POST[$page]) ? 1 : 0;
    }

    // Update role name
    $sql = "UPDATE roles SET role_name = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $role_name, $role_id);
    $stmt->execute();

    // Update page access in one query
    $sql = "UPDATE page_access SET
        dashboard = ?, product = ?, categories = ?, slider = ?, banner = ?,
        discounts = ?, coupons = ?, customers = ?, orders = ?, payments = ?,
        accounts = ?, inventory = ?, invoice = ?, courier = ?, history = ?, settings = ?
        WHERE role_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "iiiiiiiiiiiiiiiii",
        $values['dashboard'],
        $values['product'],
        $values['categories'],
        $values['slider'],
        $values['banner'],
        $values['discounts'],
        $values['coupons'],
        $values['customers'],
        $values['orders'],
        $values['payments'],
        $values['accounts'],
        $values['inventory'],
        $values['invoice'],
        $values['courier'],
        $values['history'],
        $values['settings'],
        $role_id
    );
    $stmt->execute();

    echo "<script>
        Swal.fire('Success', 'Role updated successfully!', 'success').then(() => {
            window.location = 'view-roles.php';
        });
    </script>";
    exit;
}
?>

<div class="content-wrapper">
    <div class="row">
        <div class="card p-4 col-md-8 mx-auto" style="border-radius: 0;">
            <div class="card-body">
                <h1 class="text-center mb-4">Edit Role</h1>
                <form method="POST">
                    <div class="form-group">
                        <label for="role_name"><b>Role Name</b></label>
                        <input type="text" name="role_name" id="role_name" class="form-control" value="<?= htmlspecialchars($role['role_name']) ?>" required>
                    </div>
                    <h4>Select Page Access For This Role</h4>
                    <div class="d-flex flex-wrap gap-3 mt-3 px-4">
                        <?php foreach ($pages as $page): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="<?= $page ?>" id="<?= $page ?>" <?= (isset($access[$page]) && $access[$page]) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="<?= $page ?>"><?= ucfirst($page) ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="submit" class="btn btn-gradient-primary mt-4">Update Role & Access</button>
                    <a href="view-roles.php" class="btn btn-secondary mt-4">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require 'footer.php'; ?>