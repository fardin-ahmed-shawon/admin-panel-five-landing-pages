<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'Add Expense'; // Set the page title
?>
<?php require 'header.php'; ?>

<?php
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $expense_title = $_POST['expense_title'];
    $expense_category = $_POST['expense_category'];
    $expense_amount = $_POST['expense_amount'];
    $expense_description = $_POST['expense_description'];
    $expense_date = $_POST['expense_date']; // Get the selected date

    $sql = "INSERT INTO expense_info (expense_title, expense_category, expense_amount, expense_description, expense_date) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdss", $expense_title, $expense_category, $expense_amount, $expense_description, $expense_date);

    if ($stmt->execute()) {
        $message = "Expense added successfully!";
    } else {
        $message = "Error adding expense: " . $stmt->error;
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
              
              <div class="card p-3 col-md-6 mx-auto" style="border-radius: 0;">
                <div class="card-body">
                    <?php if (isset($message)) { 
                        echo '<div class="alert alert-success" role="alert">'.$message.'</div>'; 
                        } 
                    ?>
                    <br>
                    <h1 class="text-center">Add Expense</h1>
                    <form method="POST" action="">
                      <div class="form-group">
                        <label for="expense_title">Expense Title</label>
                        <input type="text" class="form-control" id="expense_title" name="expense_title" placeholder="Enter your expense title" required>
                      </div>
                      <div class="form-group">
                        <label for="expense_category">Expense Category</label>
                        <select class="form-control" id="expense_category" name="expense_category" required>
                          <option value="">Choose Expense Category</option>
                          <?php
                          $category_query = "SELECT category_name FROM expense_category";
                          $result = $conn->query($category_query);
                          while ($row = $result->fetch_assoc()) {
                              echo "<option value='{$row['category_name']}'>{$row['category_name']}</option>";
                          }
                          ?>
                        </select>
                      </div>
                      <div class="form-group">
                        <label for="expense_amount">Expense Amount</label>
                        <input type="number" step="0.01" class="form-control" id="expense_amount" name="expense_amount" placeholder="Enter your expense amount" required>
                      </div>
                      <div class="form-group">
                        <label for="expense_description">Expense Description</label>
                        <textarea class="form-control" id="expense_description" name="expense_description" placeholder="Enter your expense description"></textarea>
                      </div>
                      <div class="form-group">
                        <label for="expense_date">Expense Date</label>
                        <input type="date" class="form-control" id="expense_date" name="expense_date" required>
                      </div>
                      <button type="submit" class="btn btn-gradient-primary mt-3">Add Expense</button>
                  </form>
                </div>
              </div>
            </div>
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->

<?php require 'footer.php'; ?>