<?php
session_start();
require_once 'config/db.php';

if(!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];
$tab = sanitize($_GET['tab'] ?? 'lost', $conn);
if(!in_array($tab, ['lost', 'found'])) {
    $tab = 'lost';
}

// Get user's items
$query = "SELECT * FROM items WHERE reportedBy = {$user['id']} AND type = '$tab' ORDER BY createdAt DESC";
$result = mysqli_query($conn, $query);
$items = [];
while($row = mysqli_fetch_assoc($result)) {
    $items[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reports - Lost & Found</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container">
        <!-- Header -->
        <div class="hero-banner" style="margin: 30px 0;">
            <h1>My Reports</h1>
            <p>Manage your lost and found item reports.</p>
        </div>

        <!-- Messages -->
        <?php if(isset($_GET['deleted'])): ?>
            <div class="form-success">
                <i class="fas fa-check-circle me-2"></i>Item deleted successfully.
            </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['updated'])): ?>
            <div class="form-success">
                <i class="fas fa-check-circle me-2"></i>Item updated successfully.
            </div>
        <?php endif; ?>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab <?php echo $tab === 'lost' ? 'active' : ''; ?>" onclick="switchTab('lost')">
                <i class="fas fa-magnifying-glass me-2"></i>My Lost Items
            </button>
            <button class="tab <?php echo $tab === 'found' ? 'active' : ''; ?>" onclick="switchTab('found')">
                <i class="fas fa-gift me-2"></i>My Found Items
            </button>
        </div>

        <!-- Items Grid -->
        <div class="items-grid">
            <?php if(empty($items)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>No <?php echo $tab; ?> items reported yet</h3>
                    <p>Start by reporting an item that you've lost or found.</p>
                    <a href="report.php" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i> Report an Item
                    </a>
                </div>
            <?php else: ?>
                <?php foreach($items as $item): ?>
                    <div class="item-card" data-type="<?php echo esc_html($item['type']); ?>">
                        <?php if($item['photo']): ?>
                            <img src="uploads/<?php echo esc_html($item['photo']); ?>" alt="Item" class="item-image">
                        <?php else: ?>
                            <div class="item-image-placeholder">
                                <i class="fas fa-image"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="item-body">
                            <div class="item-badges">
                                <span class="badge badge-<?php echo $item['type']; ?>">
                                    <?php echo $item['type'] === 'lost' ? 'LOST' : 'FOUND'; ?>
                                </span>
                                <?php if($item['status'] === 'resolved'): ?>
                                    <span class="badge badge-resolved">RESOLVED</span>
                                <?php endif; ?>
                            </div>

                            <h3 class="item-title"><?php echo esc_html($item['title']); ?></h3>
                            
                            <p class="item-description">
                                <?php echo esc_html(substr($item['description'], 0, 80)); ?>...
                            </p>

                            <div class="item-meta">
                                <span class="category-badge"><?php echo esc_html(CATEGORIES[$item['category']] ?? $item['category']); ?></span>
                                <span class="date-badge"><?php echo formatDate($item['createdAt']); ?></span>
                            </div>

                            <!-- Actions -->
                            <div class="report-actions" style="margin-top: 15px;">
                                <a href="item-detail.php?id=<?php echo $item['id']; ?>" class="btn btn-view" style="width: auto; flex: 1;">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="edit-item.php?id=<?php echo $item['id']; ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <?php if($item['status'] === 'active'): ?>
                                    <a href="resolve-item.php?id=<?php echo $item['id']; ?>" class="btn btn-sm" style="background: var(--resolved-color); color: white; border: none;">
                                        <i class="fas fa-check"></i> Resolve
                                    </a>
                                <?php endif; ?>
                                <a href="delete-item.php?id=<?php echo $item['id']; ?>" class="btn btn-sm" style="background: #DC2626; color: white; border: none;" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        function switchTab(tabName) {
            window.location.href = 'my-reports.php?tab=' + tabName;
        }
    </script>
</body>
</html>
