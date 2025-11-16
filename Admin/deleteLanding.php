<?php
require 'header.php';

// Get product_id from GET
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$product_id) die("Invalid product ID");

// Delete landing page
$stmt = $conn->prepare("DELETE FROM landing_pages WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$stmt->close();

// Delete related features
$stmt = $conn->prepare("DELETE FROM features WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$stmt->close();

// Delete related reviews
$stmt = $conn->prepare("DELETE FROM reviews WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$stmt->close();

// Delete related gallery images
$stmt = $conn->prepare("DELETE FROM gallery WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$stmt->close();

echo '<div class="alert alert-success">Landing page and all related data deleted successfully!</div>';
echo "<meta http-equiv='refresh' content='1;url=landing-page-list.php'>"; // Redirect to list page

require 'footer.php';
?>