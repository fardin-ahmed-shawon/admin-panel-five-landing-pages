<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'Banner'; // Set the page title
?>
<?php require 'header.php'; ?>

<?php
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Fetch current data (if not already available)
    $infoQuery = "SELECT * FROM website_info WHERE id=1";
    $infoResult = $conn->query($infoQuery);
    $info = $infoResult->fetch_assoc();

    $updates = [];

    // Process banner_one
    if (!empty($_FILES['banner_one']['name'])) {
        $banner_one = 'uploads/' . basename($_FILES['banner_one']['name']);
        if (move_uploaded_file($_FILES['banner_one']['tmp_name'], $banner_one)) {
            $updates[] = "banner_one='$banner_one'";
        }
    }

    // Process banner_two
    if (!empty($_FILES['banner_two']['name'])) {
        $banner_two = 'uploads/' . basename($_FILES['banner_two']['name']);
        if (move_uploaded_file($_FILES['banner_two']['tmp_name'], $banner_two)) {
            $updates[] = "banner_two='$banner_two'";
        }
    }

    // Only update if any field was changed
    if (!empty($updates)) {
        // Check if a record exists
        $checkQuery = "SELECT * FROM website_info WHERE id=1";
        $result = $conn->query($checkQuery);

        if ($result->num_rows > 0) {
            // Update existing record
            $updateQuery = "UPDATE website_info SET " . implode(', ', $updates) . " WHERE id=1";
            $conn->query($updateQuery);
        } else {
            // Insert new record, filling only available fields
            $columns = ['id'];
            $values = [1];

            if (!empty($banner_one)) {
                $columns[] = 'banner_one';
                $values[] = "'$banner_one'";
            }

            if (!empty($banner_two)) {
                $columns[] = 'banner_two';
                $values[] = "'$banner_two'";
            }

            $insertQuery = "INSERT INTO website_info (" . implode(',', $columns) . ") VALUES (" . implode(',', $values) . ")";
            $conn->query($insertQuery);
        }
    }
}

// Fetch updated data
$infoQuery = "SELECT * FROM website_info WHERE id=1";
$infoResult = $conn->query($infoQuery);
$info = $infoResult->fetch_assoc();
?>

<!--------------------------->
<!-- START MAIN AREA -->
<!--------------------------->
<div class="content-wrapper">
                <div class="row">
                    <div class="col-md-5">
                        <div class="card card-body p-5">
                            <h4>Update Banners</h4><br><br>
                            <form method="POST" action="" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="b1">Banner One</label>
                                    <input type="file" class="form-control" id="b1" name="banner_one">
                                </div>
                                <div class="form-group">
                                    <label for="b2">Banner Two</label>
                                    <input type="file" class="form-control" id="b2" name="banner_two">
                                </div>
                                <button type="submit" class="btn btn-primary mt-3">Save</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-7 mt-3 mt-md-0">
                        <div class="card card-body p-5">
                            <h4>Preview</h4><br><br>
                            <p><strong>Banner One:</strong><br>
                                <img src="<?= $info['banner_one'] ?? '#' ?>" alt="Banner One" style="max-width: 100%; height: auto;">
                            </p>
                            <p><strong>Banner Two:</strong><br>
                                <img src="<?= $info['banner_two'] ?? '#' ?>" alt="Banner Two" style="max-width: 100%; height: auto;">
                            </p>
                        </div>
                    </div>
                </div>
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->

<?php require 'footer.php'; ?>