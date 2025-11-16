<?php
require '../database/dbConnection.php'; // DB connection

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid request");
}

$review_id = intval($_GET['id']);

// Delete review
$stmt = $conn->prepare("DELETE FROM reviews WHERE review_id=?");
$stmt->bind_param("i", $review_id);
$stmt->execute();
$stmt->close();

header("Location: editLanding.php?id=" . intval($_GET['landing_id']) . "&msg=review_deleted");
exit;
