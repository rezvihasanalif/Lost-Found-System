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
    header('Location: home.php');
    exit;
}

$query = "SELECT items.*, users.fullName, users.email, resolver.fullName AS resolvedByName, resolver.email AS resolvedByEmail FROM items 
          JOIN users ON items.reportedBy = users.id 
          LEFT JOIN users AS resolver ON items.resolvedBy = resolver.id 
          WHERE items.id = $itemId";
$result = mysqli_query($conn, $query);
$item = mysqli_fetch_assoc($result);

if(!$item) {
    header('Location: home.php');
    exit;
}

$isOwner = ($item['reportedBy'] === $user['id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html($item['title']); ?> - Lost & Found</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container">
        <a href="home.php" class="btn-outline" style="margin-top: 30px; margin-bottom: 30px;">
            <i class="fas fa-arrow-left"></i> Back to Items
        </a>

        <div class="item-detail-container">
            <!-- Image -->
            <?php if($item['photo']): ?>
                <img src="uploads/<?php echo esc_html($item['photo']); ?>" alt="<?php echo esc_html($item['title']); ?>" class="item-detail-image">
            <?php else: ?>
                <div class="item-detail-placeholder">
                    <i class="fas fa-image"></i>
                </div>
            <?php endif; ?>

            <!-- Details -->
            <div class="item-detail-content">
                <div class="item-detail-title">
                    <h1><?php echo esc_html($item['title']); ?></h1>
                    <span class="badge badge-<?php echo $item['type']; ?>">
                        <?php echo strtoupper($item['type']); ?>
                    </span>
                    <?php if($item['status'] === 'resolved'): ?>
                        <span class="badge badge-resolved">RESOLVED</span>
                    <?php endif; ?>
                </div>

                <div class="item-detail-info">
                    <div class="info-row">
                        <span class="info-label">Category</span>
                        <span class="info-value"><?php echo esc_html(CATEGORIES[$item['category']] ?? $item['category']); ?></span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Location</span>
                        <span class="info-value"><?php echo esc_html($item['location']); ?></span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Date</span>
                        <span class="info-value"><?php echo formatDateTime($item['date']); ?></span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Description</span>
                        <span class="info-value"><?php echo nl2br(esc_html($item['description'])); ?></span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Reported By</span>
                        <span class="info-value">
                            <?php echo esc_html($item['fullName']); ?>
                            <br>
                            <small><?php echo esc_html($item['email']); ?></small>
                        </span>
                    </div>

                    <?php if($item['status'] === 'resolved' && !empty($item['resolvedByName'])): ?>
                        <div class="info-row">
                            <span class="info-label">Resolved By</span>
                            <span class="info-value">
                                <?php echo esc_html($item['resolvedByName']); ?>
                                <br>
                                <small><?php echo esc_html($item['resolvedByEmail']); ?></small>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Resolved On</span>
                            <span class="info-value"><?php echo formatDateTime($item['resolvedAt']); ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="info-row">
                        <span class="info-label">Posted</span>
                        <span class="info-value"><?php echo formatDate($item['createdAt']); ?></span>
                    </div>
                </div>

                </div>

                <!-- Actions -->
                <div class="item-actions">
                    <?php if(!$isOwner && $item['status'] === 'active'): ?>
                        <a href="mailto:<?php echo urlencode($item['email']); ?>?subject=Regarding: <?php echo urlencode($item['title']); ?>" class="btn-view">
                            <i class="fas fa-envelope"></i> Contact Reporter
                        </a>
                    <?php endif; ?>

                    <?php if($item['status'] === 'active'): ?>
                        <?php if($isOwner): ?>
                            <a href="edit-item.php?id=<?php echo $item['id']; ?>" class="btn-sm">Edit</a>
                            <a href="delete-item.php?id=<?php echo $item['id']; ?>" class="btn-sm" style="background: #DC2626;">Delete</a>
                        <?php endif; ?>
                        <a href="resolve-item.php?id=<?php echo $item['id']; ?>" class="btn-sm" style="background: var(--resolved-color);">
                            <i class="fas fa-check"></i>
                            <?php echo $isOwner ? 'Mark Resolved' : ($item['type'] === 'found' ? 'Return to Owner' : 'I Found This'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
