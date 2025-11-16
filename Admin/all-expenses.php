<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'All Expenses'; // Set the page title
?>
<?php require 'header.php'; ?>

<?php
// Handle search and sorting
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$expense_category = isset($_GET['expense_category']) ? $_GET['expense_category'] : '';

$query = "SELECT * FROM expense_info WHERE 1=1";

if ($date_from && $date_to) {
    $query .= " AND expense_date BETWEEN '$date_from' AND '$date_to'";
}

if ($expense_category) {
    $query .= " AND expense_category = '$expense_category'";
}

$query .= " ORDER BY expense_date DESC";

$result = mysqli_query($conn, $query);
$total_expenses = 0;

// Fetch expense categories for the dropdown
$category_query = "SELECT category_name FROM expense_category";
$category_result = mysqli_query($conn, $category_query);
?>

<style>
      .table-responsive {
          max-height: 500px; /* Fixed height for the table */
          overflow-y: auto;
      }
      table thead th {
          position: sticky;
          top: 0;
          background: #f8f9fa; /* Header background color */
          z-index: 1;
      }
      table tfoot td {
          position: sticky;
          bottom: 0;
          background: #f8f9fa; /* Footer background color */
          z-index: 1;
      }
</style>

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
                <h1>All Expenses</h1>
                <form method="GET" class="mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="date_from">Date From:</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" value="<?php echo $date_from; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="date_to">Date To:</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" value="<?php echo $date_to; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="expense_category">Expense Category:</label>
                            <select name="expense_category" id="expense_category" class="form-control">
                                <option value="">All</option>
                                <?php
                                if (mysqli_num_rows($category_result) > 0) {
                                    while ($category_row = mysqli_fetch_assoc($category_result)) {
                                        $selected = $expense_category == $category_row['category_name'] ? 'selected' : '';
                                        echo "<option value='{$category_row['category_name']}' $selected>{$category_row['category_name']}</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary mt-4">Search</button>
                        </div>
                    </div>
                </form>
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="table-responsive w-100">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th><b>Expense ID</b></th>
                                <th><b>Title</b></th>
                                <th><b>Category</b></th>
                                <th><b>Amount (Tk)</b></th>
                                <th><b>Description</b></th>
                                <th><b>Date & Time</b></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>
                                            <td>{$row['expense_id']}</td>
                                            <td>{$row['expense_title']}</td>
                                            <td>{$row['expense_category']}</td>
                                            <td>" . number_format($row['expense_amount'], 2) . " Tk.</td>
                                            <td>{$row['expense_description']}</td>
                                            <td>{$row['expense_date']}</td>
                                          </tr>";
                                    $total_expenses += $row['expense_amount'];
                                }
                            } else {
                                echo "<tr><td colspan='6'>No records found</td></tr>";
                            }
                            ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td></td>
                                    <td><b>Total Expenses</b></td>
                                    <td></td>
                                    <td><b><?php echo number_format($total_expenses, 2); ?> Tk.</b></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->

<?php require 'footer.php'; ?>