<?php
$current_page = basename($_SERVER['PHP_SELF']);
$page_title = 'Edit Landing Page';
require 'header.php';

// Get landing_id from GET
$landing_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$landing_id) die("Invalid landing page ID");

// Fetch landing page + product info
$stmt = $conn->prepare("
    SELECT lp.*, p.product_slug 
    FROM landing_pages lp
    INNER JOIN product_info p ON lp.product_id = p.product_id
    WHERE lp.id = ?
");
$stmt->bind_param("i", $landing_id);
$stmt->execute();
$result = $stmt->get_result();
$landing = $result->fetch_assoc();
$stmt->close();

if (!$landing) die("Landing page not found");

$product_id = $landing['product_id'];

// Fetch Features, Reviews, Gallery
$features = $conn->query("SELECT * FROM features WHERE product_id = $product_id")->fetch_all(MYSQLI_ASSOC);
$reviews = $conn->query("SELECT * FROM reviews WHERE product_id = $product_id")->fetch_all(MYSQLI_ASSOC);
$gallery = $conn->query("SELECT * FROM gallery WHERE product_id = $product_id")->fetch_all(MYSQLI_ASSOC);

// Helper: Compress image
function compressImage($source, $destination, $quality = 70) {
    $info = getimagesize($source);
    if ($info['mime'] == 'image/jpeg') {
        $image = imagecreatefromjpeg($source);
        imagejpeg($image, $destination, $quality);
    } elseif ($info['mime'] == 'image/png') {
        $image = imagecreatefrompng($source);
        imagepng($image, $destination, round($quality / 10));
    } else {
        move_uploaded_file($source, $destination); // fallback
    }
}

// Handle update form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $home_title = $_POST['home_title'];
    $home_description = $_POST['home_description'];

    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) mkdir($upload_dir);

    $home_img = $landing['home_img']; 
    if (!empty($_FILES['home_img']['tmp_name'])) {
        $home_img = $upload_dir . uniqid() . '_' . $_FILES['home_img']['name'];
        compressImage($_FILES['home_img']['tmp_name'], $home_img, 70);
    }

    $feature_img = $landing['feature_img']; 
    if (!empty($_FILES['feature_img']['tmp_name'])) {
        $feature_img = $upload_dir . uniqid() . '_' . $_FILES['feature_img']['name'];
        compressImage($_FILES['feature_img']['tmp_name'], $feature_img, 70);
    }

    // Update landing_pages
    $stmt = $conn->prepare("
        UPDATE landing_pages 
        SET home_title=?, home_description=?, home_img=?, feature_img=? 
        WHERE id=?");
    $stmt->bind_param("ssssi", $home_title, $home_description, $home_img, $feature_img, $landing_id);
    $stmt->execute();
    $stmt->close();

    // Insert new Features
    if (!empty($_POST['feature_title'])) {
        foreach ($_POST['feature_title'] as $i => $ftitle) {
            $fdesc = $_POST['feature_description'][$i];
            if ($ftitle && $fdesc) {
                $stmt = $conn->prepare("INSERT INTO features (product_id, feature_title, feature_description) VALUES (?, ?, ?)");
                $stmt->bind_param("iss", $product_id, $ftitle, $fdesc);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    // Insert new Reviews
    if (!empty($_FILES['review_image']['name'][0])) {
        foreach ($_FILES['review_image']['tmp_name'] as $i => $tmp_name) {
            if ($tmp_name) {
                $review_img = $upload_dir . uniqid() . '_' . $_FILES['review_image']['name'][$i];
                compressImage($tmp_name, $review_img, 70);
                $stmt = $conn->prepare("INSERT INTO reviews (product_id, review_image) VALUES (?, ?)");
                $stmt->bind_param("is", $product_id, $review_img);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    // Insert new Gallery
    if (!empty($_FILES['gallery_image']['name'][0])) {
        foreach ($_FILES['gallery_image']['tmp_name'] as $i => $tmp_name) {
            if ($tmp_name) {
                $gallery_img = $upload_dir . uniqid() . '_' . $_FILES['gallery_image']['name'][$i];
                compressImage($tmp_name, $gallery_img, 70);
                $stmt = $conn->prepare("INSERT INTO gallery (product_id, gallery_image) VALUES (?, ?)");
                $stmt->bind_param("is", $product_id, $gallery_img);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    echo '<div class="alert alert-success">Landing page updated successfully!</div>';
    // Refresh page
    echo "<meta http-equiv='refresh' content='1'>";
}
?>

<div class="content-wrapper">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="mb-4">Edit Landing Page</h1>
                <span class="display-5 text-primary"><?= htmlspecialchars($landing['product_slug']) ?></span>
            </div>
            <div>
                <a href="landing-page-list.php" class="btn btn-dark">Back</a>
            </div>
        </div>
        <br><br>

        <form method="POST" enctype="multipart/form-data">

            <!-- Landing Info -->
            <div class="card mb-4 rounded-0">
                <div class="card-header bg-dark text-white">Landing Page Info</div>
                <div class="card-body row g-3 p-3">
                    <div class="col-md-6">
                        <label class="form-label">Home Title</label>
                        <input type="text" name="home_title" value="<?= htmlspecialchars($landing['home_title']) ?>" class="form-control" required>
                        <br>
                        <label class="form-label">Home Description</label>
                        <textarea name="home_description" class="form-control" rows="4" required><?= htmlspecialchars($landing['home_description']) ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Home Image</label><br>
                        <?php if ($landing['home_img']): ?><img src="<?= $landing['home_img'] ?>" width="120" class="mb-2"><br><?php endif; ?>
                        <input type="file" name="home_img" accept="image/*" class="form-control">
                        <br>
                        <label class="form-label">Feature Image</label><br>
                        <?php if ($landing['feature_img']): ?><img src="<?= $landing['feature_img'] ?>" width="120" class="mb-2"><br><?php endif; ?>
                        <input type="file" name="feature_img" accept="image/*" class="form-control">
                    </div>
                </div>
            </div>

            <!-- Features -->
            <div class="card mb-4 rounded-0">
                <div class="card-header bg-dark text-white">Features</div>
                <div class="card-body p-3" id="features-section">
                    <?php foreach ($features as $f): ?>
                        <div class="feature-group row g-3 mb-2">
                            <div class="col-md-5"><input type="text" class="form-control" value="<?= htmlspecialchars($f['feature_title']) ?>" readonly></div>
                            <div class="col-md-5"><input type="text" class="form-control" value="<?= htmlspecialchars($f['feature_description']) ?>" readonly></div>
                            <div class="col-md-2"><a href="delete_feature.php?id=<?= $f['feature_id'] ?>" class="btn btn-danger">Delete</a></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="btn btn-outline-success" id="add-feature"><b>Add Feature</b></button>
            </div>

            <!-- Reviews -->
            <div class="card mb-4 rounded-0">
                <div class="card-header bg-dark text-white">Reviews</div>
                <div class="card-body p-3" id="reviews-section">
                    <?php foreach ($reviews as $r): ?>
                        <div class="review-group row g-3 mb-2">
                            <div class="col-md-10"><img src="<?= $r['review_image'] ?>" width="100"></div>
                            <div class="col-md-2"><a href="delete_review.php?id=<?= $r['review_id'] ?>" class="btn btn-danger">Delete</a></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="btn btn-outline-info" id="add-review"><b>Add Review Image</b></button>
            </div>

            <!-- Gallery -->
            <div class="card mb-4 rounded-0">
                <div class="card-header bg-dark text-white">Gallery</div>
                <div class="card-body p-3" id="gallery-section">
                    <?php foreach ($gallery as $g): ?>
                        <div class="gallery-group row g-3 mb-2">
                            <div class="col-md-10"><img src="<?= $g['gallery_image'] ?>" width="100"></div>
                            <div class="col-md-2"><a href="delete_gallery.php?id=<?= $g['image_id'] ?>" class="btn btn-danger">Delete</a></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="btn btn-outline-primary" id="add-gallery"><b>Add Gallery Image</b></button>
            </div>

            <br><hr><br>
            <button type="submit" class="btn btn-dark btn-lg w-100"><b>Update Landing Page</b></button>
        </form>
    </div>
</div>

<!-- JS for dynamic new fields -->
<script>
document.getElementById('add-feature').onclick = function() {
    let section = document.getElementById('features-section');
    let group = document.createElement('div');
    group.className = 'feature-group row g-3 mb-2';
    group.innerHTML = `
        <div class="col-md-5"><input type="text" name="feature_title[]" class="form-control" placeholder="Feature Title" required></div>
        <div class="col-md-5"><input type="text" name="feature_description[]" class="form-control" placeholder="Feature Description" required></div>
        <div class="col-md-2"><button type="button" class="btn btn-danger remove-feature">Remove</button></div>
    `;
    section.appendChild(group);
};
document.getElementById('features-section').onclick = e => { if (e.target.classList.contains('remove-feature')) e.target.closest('.feature-group').remove(); };

document.getElementById('add-review').onclick = function() {
    let section = document.getElementById('reviews-section');
    let group = document.createElement('div');
    group.className = 'review-group row g-3 mb-2';
    group.innerHTML = `
        <div class="col-md-10"><input type="file" name="review_image[]" accept="image/*" class="form-control" required></div>
        <div class="col-md-2"><button type="button" class="btn btn-danger remove-review">Remove</button></div>
    `;
    section.appendChild(group);
};
document.getElementById('reviews-section').onclick = e => { if (e.target.classList.contains('remove-review')) e.target.closest('.review-group').remove(); };

document.getElementById('add-gallery').onclick = function() {
    let section = document.getElementById('gallery-section');
    let group = document.createElement('div');
    group.className = 'gallery-group row g-3 mb-2';
    group.innerHTML = `
        <div class="col-md-10"><input type="file" name="gallery_image[]" accept="image/*" class="form-control" required></div>
        <div class="col-md-2"><button type="button" class="btn btn-danger remove-gallery">Remove</button></div>
    `;
    section.appendChild(group);
};
document.getElementById('gallery-section').onclick = e => { if (e.target.classList.contains('remove-gallery')) e.target.closest('.gallery-group').remove(); };
</script>

<?php require 'footer.php'; ?>