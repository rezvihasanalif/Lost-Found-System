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

// Verify ownership and delete
$query = "DELETE FROM items WHERE id = $itemId AND reportedBy = {$user['id']}";

if(mysqli_query($conn, $query)) {
    header('Location: my-reports.php?deleted=1');
} else {
    header('Location: my-reports.php?error=1');
}
exit;
?>
