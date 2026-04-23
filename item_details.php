<?php
$itemId = (int)($_GET['id'] ?? 0);
if ($itemId === 0) {
    header('Location: home.php');
    exit;
}
header('Location: item-detail.php?id=' . $itemId);
exit;
