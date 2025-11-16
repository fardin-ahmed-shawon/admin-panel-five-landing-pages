<?php
$current_page = basename($_SERVER['PHP_SELF']); 
$page_title = 'Sizes'; 
?>
<?php require 'header.php'; ?>

<?php
// ADD OR UPDATE SIZE
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $size_label = trim($_POST['size_label']);
    $edit_id = isset($_POST['edit_id']) ? intval($_POST['edit_id']) : 0;

    if (empty($size_label)) {
        $error_message = "Size label is required.";
    } else {
        if ($edit_id > 0) {
            // Update existing size
            $sql = "UPDATE size_labels SET size_label=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $size_label, $edit_id);
            if ($stmt->execute()) {
                $success_message = "Size updated successfully!";
            } else {
                $error_message = "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            // Insert new size
            $sql = "INSERT INTO size_labels (size_label) VALUES (?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $size_label);
            if ($stmt->execute()) {
                $success_message = "Size added successfully!";
            } else {
                $error_message = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// DELETE SIZE
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $sql = "DELETE FROM size_labels WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Size deleted successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete size.']);
    }
    $stmt->close();
    exit;
}
?>

<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-home"></i>
            </span> Sizes
        </h3>
    </div>
    <br>
    <div class="row">
        <!-- Add / Edit Size Form -->
        <div class="col-md-4 mx-auto mt-5">
            <div class="card card-body p-4">
                <div class="row mb-2">
                    <div class="col mt-3">
                        <h6 id="form-title">Add Size</h6>
                    </div>
                </div><hr>

                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <form action="" method="POST" id="size-form">
                    <input type="hidden" name="edit_id" id="edit_id">
                    <div class="form-group">
                        <label style="font-size: 17px" for="sizeinput">Enter Size Label *</label>
                        <input type="text" class="form-control" id="sizeinput" name="size_label" required>
                    </div>
                    <button type="submit" class="btn btn-primary" id="submit-btn">Add Size</button>
                    <button type="button" class="btn btn-secondary d-none" id="cancel-edit">Cancel</button>
                </form>
            </div>
        </div>

        <!-- Size List -->
        <div class="col-md-7 mx-auto mt-5">
            <div class="card card-body">
                <div class="row mb-2">
                    <div class="col mt-3">
                        <h6 class="text-center">Size List</h6>
                    </div>
                </div><hr>

                <div class="table-responsive">
                    <table class="table table-bordered" id="sizes-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Size Label</th>
                                <th colspan="2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = $conn->query("SELECT * FROM size_labels ORDER BY id DESC");
                            if ($result && $result->num_rows > 0):
                                $i = 1;
                                while ($row = $result->fetch_assoc()):
                            ?>
                            <tr data-id="<?php echo $row['id']; ?>" data-label="<?php echo htmlspecialchars($row['size_label']); ?>">
                                <td><?php echo $i++; ?></td>
                                <td class="size-label"><?php echo htmlspecialchars($row['size_label']); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info edit-btn">Edit</button>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-danger delete-btn">Delete</button>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr>
                                <td colspan="3" class="text-center">No sizes found.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Edit size
document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const row = this.closest('tr');
        const id = row.getAttribute('data-id');
        const label = row.getAttribute('data-label');

        document.getElementById('edit_id').value = id;
        document.getElementById('sizeinput').value = label;
        document.getElementById('form-title').innerText = "Edit Size";
        document.getElementById('submit-btn').innerText = "Update Size";
        document.getElementById('cancel-edit').classList.remove('d-none');
    });
});

// Cancel edit
document.getElementById('cancel-edit').addEventListener('click', function() {
    document.getElementById('edit_id').value = '';
    document.getElementById('sizeinput').value = '';
    document.getElementById('form-title').innerText = "Add Size";
    document.getElementById('submit-btn').innerText = "Add Size";
    this.classList.add('d-none');
});

// Delete size
document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const row = this.closest('tr');
        const id = row.getAttribute('data-id');

        Swal.fire({
            title: 'Are you sure?',
            text: "This size will be deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch("delete-size-label.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "delete_id=" + id
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === "success") {
                        Swal.fire("Deleted!", data.message, "success");
                        row.remove();
                    } else {
                        Swal.fire("Error!", data.message, "error");
                    }
                })
                .catch(() => Swal.fire("Error!", "Something went wrong.", "error"));
            }
        });
    });
});
</script>

<?php require 'footer.php'; ?>
