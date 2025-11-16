<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'Edit Expense Category'; // Set the page title
?>
<?php require 'header.php'; ?>

<?php

// Get ID from query
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: add-expense-category.php");
    exit();
}

$category_id = intval($_GET['id']);

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_name = $_POST['category_name'];
    $stmt = $conn->prepare("UPDATE expense_category SET category_name = ? WHERE category_id = ?");
    $stmt->bind_param("si", $category_name, $category_id);

    if ($stmt->execute()) {
        header("Location: add-expense-category.php?msg=updated");
        exit();
    } else {
        $error = "Update failed: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch category data
$stmt = $conn->prepare("SELECT category_name FROM expense_category WHERE category_id = ?");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$stmt->bind_result($category_name);
$stmt->fetch();
$stmt->close();
?>

<!--------------------------->
<!-- START MAIN AREA -->
<!--------------------------->
<div class="content-wrapper">
            <h3>Edit Expense Category</h3>
            <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="category_name">Category Name</label>
                    <input type="text" class="form-control" name="category_name" id="category_name" value="<?php echo htmlspecialchars($category_name); ?>" required>
                </div>
                <button type="submit" class="btn btn-success mt-3">Update</button>
                <a href="add-expense-category.php" class="btn btn-secondary mt-3">Cancel</a>
            </form>
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->

<?php require 'footer.php'; ?>