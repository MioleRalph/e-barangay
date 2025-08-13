<?php
include '../connection.php';
$connection->exec("UPDATE official_notifications SET is_read = 1 WHERE is_read = 0");
header("Location: official_dashboard.php"); // change to your dashboard filename
exit;
?>
