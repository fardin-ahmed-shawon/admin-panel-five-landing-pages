<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'Edit Logo'; // Set the page title
?>
<?php require 'header.php'; ?>

<?php
// Handle logo upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_logo'])) {
    $logo = $info['logo'] ?? null;

    if (!empty($_FILES['logo']['name'])) {
        $logo = 'uploads/' . basename($_FILES['logo']['name']);
        if (!move_uploaded_file($_FILES['logo']['tmp_name'], $logo)) {
            $logo = $info['logo']; // fallback if upload fails
        }
    }

    // Check if record exists
    $checkQuery = "SELECT * FROM website_info WHERE id=1";
    $result = $conn->query($checkQuery);

    if ($result->num_rows > 0) {
        $updateQuery = "UPDATE website_info SET logo='$logo' WHERE id=1";
        $conn->query($updateQuery);
    } else {
        $insertQuery = "INSERT INTO website_info (id, logo) VALUES (1, '$logo')";
        $conn->query($insertQuery);
    }

    echo "<script>alert('Logo updated successfully!'); window.location.href=window.location.href;</script>";
}

// Handle logo_size update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_logo_size'])) {
    $logo_size = trim($_POST['logo_size']);

    if (!empty($logo_size)) {
        $updateQuery = "UPDATE website_info SET logo_size = ? WHERE id = 1";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("s", $logo_size);
        if ($stmt->execute()) {
            echo "<script>alert('Logo size updated successfully!'); window.location.href=window.location.href;</script>";
        } else {
            echo "<script>alert('Failed to update logo size.');</script>";
        }
    } else {
        echo "<script>alert('Logo size cannot be empty!');</script>";
    }
}

// Fetch data to display
$infoQuery = "SELECT * FROM website_info WHERE id=1";
$infoResult = $conn->query($infoQuery);
$info = $infoResult->fetch_assoc();
?>

<!--------------------------->
<!-- START MAIN AREA -->
<!--------------------------->
<div class="content-wrapper">
                <div class="row">
                    <!-- LEFT: Logo Upload -->
                    <div class="col-md-8">
                        <div class="card card-body p-5">
                            <h4>Update Logo</h4><br><br>
                            <form method="POST" action="" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="logo">Logo</label>
                                    <input type="file" class="form-control" id="logo" name="logo">
                                </div>
                                <button type="submit" name="save_logo" class="btn btn-primary">Save</button>
                            </form>
                        </div>
                    </div>

                    <!-- RIGHT: Preview + Logo Size Update -->
                    <div class="col-md-4">
                        <div class="card card-body p-5">
                            <h4>Preview</h4><br><br>
                            <p><strong>Logo:</strong></p>
                            <img src="<?= htmlspecialchars($info['logo'] ?? '#') ?>" alt="Logo"
                                 style="max-width: <?= htmlspecialchars($info['logo_size'] ?? '100px') ?>;">

                            <hr>

                            <form method="POST" action="" class="d-none">
                                <div class="form-group">
                                    <label for="logo_size"><strong>Logo Size:</strong></label>
                                    <input type="text" class="form-control" id="logo_size" name="logo_size"
                                           value="<?= htmlspecialchars($info['logo_size'] ?? '50') ?>"
                                           placeholder="Enter logo size">
                                </div>
                                <br>
                                <button type="submit" name="save_logo_size" class="btn btn-success">Save Logo Size</button>
                            </form>
                        </div>
                    </div>
                </div>
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->

<?php require 'footer.php'; ?>