<?php
session_start();
require_once 'config/db.php';

if(!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];
$itemId = (int)($_GET['id'] ?? 0);

if($itemId === 0) {
    header('Location: my-reports.php');
    exit;
}

// Mark active item as resolved for any authenticated user and record who resolved it
$query = "UPDATE items SET status = 'resolved', resolvedBy = {$user['id']}, resolvedAt = NOW() WHERE id = $itemId AND status = 'active'";

$redirectPage = isset($_GET['return']) ? basename($_GET['return']) : 'item-detail.php';
if(mysqli_query($conn, $query) && mysqli_affected_rows($conn) > 0) {
    header('Location: ' . $redirectPage . '?id=' . $itemId . '&resolved=1');
} else {
    header('Location: ' . $redirectPage . '?id=' . $itemId . '&error=1');
}
exit;
?>
