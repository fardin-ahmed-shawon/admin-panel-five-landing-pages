<?php
require '../database/dbConnection.php'; // DB connection

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid request");
}

$feature_id = intval($_GET['id']);

// Delete feature
$stmt = $conn->prepare("DELETE FROM features WHERE feature_id=?");
$stmt->bind_param("i", $feature_id);
$stmt->execute();
$stmt->close();

header("Location: editLanding.php?id=" . intval($_GET['landing_id']) . "&msg=feature_deleted");
exit;
