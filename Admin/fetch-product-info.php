<?php
require 'database/dbConnection.php'; // or your connection file
$product_id = intval($_GET['product_id']);
$sizes = [];
$price = 0;

// Get sizes
$size_query = $conn->prepare("SELECT size FROM product_size_list WHERE product_id = ?");
$size_query->bind_param("i", $product_id);
$size_query->execute();
$size_result = $size_query->get_result();
while ($row = $size_result->fetch_assoc()) {
    $sizes[] = $row['size'];
}
$size_query->close();

// Get price
$price_query = $conn->query("SELECT product_price FROM product_info WHERE product_id = $product_id LIMIT 1");
if ($price_row = $price_query->fetch_assoc()) {
    $price = intval($price_row['product_price']);
}

echo json_encode(['sizes' => $sizes, 'price' => $price]);