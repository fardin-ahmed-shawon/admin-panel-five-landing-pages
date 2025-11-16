<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
$page_title = 'Steadfast API'; // Set the page title
?>
<?php require 'header.php'; ?>

<?php

$msg = '';
$type = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $api_url = $conn->real_escape_string($_POST['api_url']);
    $api_key = $conn->real_escape_string($_POST['api_key']);
    $secret_key = $conn->real_escape_string($_POST['secret_key']);

    $check = $conn->query("SELECT id FROM steadfast_info LIMIT 1");
    if ($check->num_rows > 0) {
        $row = $check->fetch_assoc();
        $id = $row['id'];
        $conn->query("UPDATE steadfast_info SET api_url='$api_url', api_key='$api_key', secret_key='$secret_key' WHERE id=$id");
        $msg = "API info updated successfully!";
        $type = "success";
    } else {
        $conn->query("INSERT INTO steadfast_info (api_url, api_key, secret_key) VALUES ('$api_url', '$api_key', '$secret_key')");
        $msg = "API info inserted successfully!";
        $type = "success";
    }
}

$result = $conn->query("SELECT * FROM steadfast_info LIMIT 1");
$info = $result->fetch_assoc();
?>

<!--------------------------->
<!-- START MAIN AREA -->
<!--------------------------->
<div class="content-wrapper">
    <div class="row">
        <div class="card col-md-6 mx-auto p-4" style="border-radius: 0;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="text-center mb-0">Steadfast API Info</h1>
                <a href="courier.php" class="btn btn-dark">
                    <span class="mdi mdi-arrow-left"></span> Back
                </a>
            </div>
            <div class="card-body">
                <form method="post" autocomplete="off">
                    <div class="form-group">
                        <label for="api_url">API URL:</label>
                        <input class="form-control" id="api_url" type="text" name="api_url" value="<?php echo htmlspecialchars($info['api_url'] ?? ''); ?>"    placeholder="Enter API URL" required>
                    </div>
                    <div class="form-group">
                        <label for="api_key">API Key:</label>
                        <input class="form-control" id="api_key" type="text" name="api_key" value="<?php echo htmlspecialchars($info['api_key'] ?? ''); ?>" placeholder="Enter API Key" required>
                    </div>
                    <div class="form-group">
                        <label for="secret_key">Secret Key:</label>
                        <input class="form-control" id="secret_key" type="text" name="secret_key" value="<?php echo htmlspecialchars($info['secret_key'] ?? ''); ?>" placeholder="Enter Secret Key" required>
                    </div>
                    <button type="submit" class="btn btn-gradient-primary mt-3">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!--------------------------->
<!-- END MAIN AREA -->
<!--------------------------->
<?php if (!empty($msg)): ?>
<script>
    Swal.fire({
        icon: '<?php echo $type; ?>',
        title: '<?php echo $msg; ?>',
        showConfirmButton: false,
        timer: 2000
    });
</script>
<?php endif; ?>

<?php require 'footer.php'; ?>