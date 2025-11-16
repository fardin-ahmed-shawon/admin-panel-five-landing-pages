<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'Add Role'; // Set the page title
?>
<?php 
require 'header.php';

// Only allow access to this page if the user is an Admin
if ($_SESSION['role'] != 'Admin') {
    echo "<script>alert('Access denied.'); window.location.href='404.php';</script>";
    exit();
}
?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role_name = trim($_POST['role_name']);

    if (!empty($role_name)) {
        // Check for duplicate role
        $check = $conn->prepare("SELECT * FROM roles WHERE role_name = ?");
        $check->bind_param("s", $role_name);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $message = "duplicate";
        } else {
            // Insert role
            $insertRole = $conn->prepare("INSERT INTO roles (role_name) VALUES (?)");
            $insertRole->bind_param("s", $role_name);

            if ($insertRole->execute()) {
                $role_id = $conn->insert_id;

                // Pages list
                $pages = [
                    'dashboard', 'product', 'categories', 'slider', 'banner',
                    'discounts', 'coupons', 'customers', 'orders', 'payments',
                    'accounts', 'inventory', 'invoice', 'courier', 'history', 'settings'
                ];

                // Set default 0 and update if checkbox selected
                $values = [];
                foreach ($pages as $page) {
                    $values[$page] = isset($_POST[$page]) ? 1 : 0;
                }

                // Insert into page_access
                $insertAccess = $conn->prepare("INSERT INTO page_access (
                    role_id, dashboard, product, categories, slider, banner, discounts, coupons,
                    customers, orders, payments, accounts, inventory, invoice, courier, history, settings
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                $insertAccess->bind_param("iiiiiiiiiiiiiiiii",
                    $role_id,
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
                    $values['settings']
                );

                if ($insertAccess->execute()) {
                    $message = "success";
                } else {
                    $message = "access_error";
                }
                $insertAccess->close();
            } else {
                $message = "role_error";
            }
            $insertRole->close();
        }
        $check->close();
    } else {
        $message = "empty";
    }
}
?>

<!--------------------------->
<!-- START MAIN AREA -->
<!--------------------------->
<div class="content-wrapper">
        <div class="row">
          <div class="card p-4 col-md-8 mx-auto" style="border-radius: 0;">
            <h1 class="text-center mb-4">Add Role</h1>
            <div class="card-body">
              <form method="POST">
                <div class="form-group">
                  <label for="role_name"><b>Role Name</b></label>
                  <input type="text" name="role_name" id="role_name" class="form-control" placeholder="Enter role name" required>
                </div>

                <h4>Select Page Access For This Role</h4>

                <div class="d-flex flex-wrap gap-3 mt-3 px-4">
                    <?php
                    $pages = [
                    'dashboard', 'product', 'categories', 'slider', 'banner',
                    'discounts', 'coupons', 'customers', 'orders', 'payments',
                    'accounts', 'inventory', 'invoice', 'courier', 'history', 'settings'
                    ];

                    foreach ($pages as $page) {
                        echo "<div class='form-check'>
                                <input class='form-check-input' type='checkbox' name='{$page}' id='{$page}'>
                                <label class='form-check-label' for='{$page}'>" . ucfirst($page) . "</label>
                            </div>";
                    }
                    ?>
                    </div>


                <button type="submit" class="btn btn-gradient-primary mt-4">Save Role & Access</button>
              </form>
            </div>
          </div>
        </div>
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->

<?php if (isset($message)): ?>
  <script>
    <?php if ($message === "success"): ?>
      Swal.fire({
        title: 'Success',
        text: 'Role and access saved successfully!',
        icon: 'success'
      }).then(() => {
        window.location.href = 'view-roles.php';
      });
    <?php elseif ($message === "duplicate"): ?>
      Swal.fire('Warning', 'This role already exists!', 'warning');
    <?php elseif ($message === "access_error"): ?>
      Swal.fire('Error', 'Role saved but access could not be saved!', 'error');
    <?php elseif ($message === "role_error"): ?>
      Swal.fire('Error', 'Failed to save role!', 'error');
    <?php elseif ($message === "empty"): ?>
      Swal.fire('Error', 'Role name is required!', 'error');
    <?php endif; ?>
  </script>
<?php endif; ?>

<?php require 'footer.php'; ?>