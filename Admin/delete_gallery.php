<?php
require '../database/dbConnection.php'; // DB connection

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid request");
}

$image_id = intval($_GET['id']);

// Delete gallery image
$stmt = $conn->prepare("DELETE FROM gallery WHERE image_id=?");
$stmt->bind_param("i", $image_id);
$stmt->execute();
$stmt->close();

header("Location: editLanding.php?id=" . intval($_GET['landing_id']) . "&msg=gallery_deleted");
exit;
