<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'Expense Category'; // Set the page title
?>
<?php require 'header.php'; ?>

<?php
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_name = $_POST['category_name'];

    $sql = "INSERT INTO expense_category (category_name) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $category_name);

    if ($stmt->execute()) {
        $message = "Expense category added successfully!";
    } else {
        $message = "Error adding expense category: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!--------------------------->
<!-- START MAIN AREA -->
<!--------------------------->
<div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title">
                    <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-finance"></i>
                    </span> Accounts
                </h3>
            </div>
            <div class="row">
              <h1>Expense Category</h1>
              <div class="card p-3 col-md-4" style="border-radius: 0;">
                <div class="card-body">
                    <?php if (isset($message)) { 
                        echo '<div class="alert alert-success" role="alert">'.$message.'</div>'; 
                        } 
                    ?>
                    <br>
                    <h5><b>Add Expense Category</b></h5>
                    <br>
                    <form method="POST" action="">
                      <div class="form-group">
                        <label for="category_name">Category Name</label>
                        <input type="text" class="form-control" id="category_name" name="category_name" placeholder="Enter expense category name" required>
                      </div>
                      <button type="submit" class="btn btn-gradient-primary mt-3">Add Category</button>
                    </form>
                </div>
              </div>

              
              <div class="card p-3 col-md-8" style="border-radius: 0;">
                  <div class="card-body">
                    <h5><b>Existing Expense Categories</b></h5>
                    <br>
                    <?php
                    if (isset($_GET['msg'])) {
                        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
                        if ($_GET['msg'] === 'updated') {
                            echo "<script>Swal.fire('Updated!', 'Expense category has been updated.', 'success');</script>";
                        } elseif ($_GET['msg'] === 'deleted') {
                            echo "<script>Swal.fire('Deleted!', 'Expense category has been deleted.', 'success');</script>";
                        }
                    }
                    ?>

                    <br>
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th scope="col"><b>ID</b></th>
                          <th scope="col"><b>Category Name</b></th>
                          <th colspan="2"><b>Action</b></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $sql = "SELECT * FROM expense_category";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>" . $row['category_id'] . "</td>
                                        <td>" . htmlspecialchars($row['category_name']) . "</td>
                                        <td>
                                            <a href='edit-expense-category.php?id=" . $row['category_id'] . "' class='btn btn-gradient-info'>Edit</a>
                                        </td>
                                        <td>
                                            <form method='POST' action='delete-expense-category.php' style='display:inline;'>
                                                <input type='hidden' name='category_id' value='" . $row['category_id'] . "'>
                                                <button type='submit' class='btn btn-gradient-danger' onclick='return confirm(\"Are you sure you want to delete this category?\");'>Delete</button>
                                            </form>
                                        </td>
                                      </tr>";
                            } 
                        } else {
                            echo "<tr><td colspan='2' class='text-center'>No categories found</td></tr>";
                        }
                        ?>
                      </tbody>
                    </table>
                  </div>
              </div>
              
            </div>
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->

<?php require 'footer.php'; ?>