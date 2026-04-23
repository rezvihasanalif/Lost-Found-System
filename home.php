<?php
session_start();
require_once 'config/db.php';

if(!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];
$type = sanitize($_GET['type'] ?? '', $conn);
$category = sanitize($_GET['category'] ?? '', $conn);
$search = sanitize($_GET['search'] ?? '', $conn);
$page = (int)($_GET['page'] ?? 1);
$page = max($page, 1);

// Build query
$where = [];
if (!empty($type) && in_array($type, ['lost', 'found'])) {
    $where[] = "items.type = '$type'";
}
if (!empty($category) && array_key_exists($category, CATEGORIES)) {
    $where[] = "items.category = '$category'";
}
if (!empty($search)) {
    $where[] = "(items.title LIKE '%$search%' OR items.description LIKE '%$search%' OR items.location LIKE '%$search%')";
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Get total count
$countRes = mysqli_query($conn, "SELECT COUNT(*) as total FROM items $whereClause");
$countRow = mysqli_fetch_assoc($countRes);
$total = $countRow['total'];
$pages = ceil($total / ITEMS_PER_PAGE);

$offset = ($page - 1) * ITEMS_PER_PAGE;

// Get items
$query = "SELECT items.*, users.fullName, users.email FROM items 
          JOIN users ON items.reportedBy = users.id 
          $whereClause 
          ORDER BY items.createdAt DESC 
          LIMIT " . ITEMS_PER_PAGE . " OFFSET $offset";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Items - Lost & Found</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container">
        <!-- Hero Banner -->
        <div class="hero">
            <span class="hero-tag">All Items</span>
            <h1>Welcome back, <?php echo esc_html(explode(' ', $user['fullName'])[0]); ?>!</h1>
            <p>Browse all reported lost and found items from the community.</p>
        </div>

        <!-- Search Bar -->
        <div class="search-section">
            <form method="GET" action="" class="search-form">
                <input type="text" name="search" class="search-input" 
                       placeholder="Search items by title, description, location..."
                       value="<?php echo esc_html($search); ?>">
                <button type="submit" class="btn-search">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>

        <!-- Filters -->
        <div class="search-section">
            <div class="filter-tabs">
                <a href="home.php" class="filter-tab <?php echo empty($type) ? 'active' : ''; ?>">
                    All Items
                </a>
                <a href="home.php?type=lost" class="filter-tab <?php echo $type === 'lost' ? 'active' : ''; ?>">
                    Lost Items
                </a>
                <a href="home.php?type=found" class="filter-tab <?php echo $type === 'found' ? 'active' : ''; ?>">
                    Found Items
                </a>
            </div>

            <div class="category-filter">
                <select onchange="window.location.href='home.php?category=' + this.value + '<?php echo $type ? '&type=' . urlencode($type) : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>'" class="category-select">
                    <option value="">All Categories</option>
                    <?php foreach(CATEGORIES as $key => $label): ?>
                        <option value="<?php echo esc_html($key); ?>" <?php echo $category === $key ? 'selected' : ''; ?>>
                            <?php echo esc_html($label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Items Grid -->
        <div class="items-grid">
            <?php if(mysqli_num_rows($result) === 0): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>No items found</h3>
                    <p>Try adjusting your filters or search terms.</p>
                    <a href="report.php" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i> Report an Item
                    </a>
                </div>
            <?php else: ?>
                <?php while($item = mysqli_fetch_assoc($result)): ?>
                    <a href="item-detail.php?id=<?php echo $item['id']; ?>" class="item-card">
                        <?php if($item['photo']): ?>
                            <img src="uploads/<?php echo esc_html($item['photo']); ?>" alt="Item" class="item-image">
                        <?php else: ?>
                            <div class="item-placeholder">
                                <i class="fas fa-image"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="item-body">
                            <div class="item-badges">
                                <span class="badge badge-<?php echo $item['type']; ?>">
                                    <?php echo strtoupper($item['type']); ?>
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
                                <span class="item-category"><?php echo esc_html(CATEGORIES[$item['category']] ?? $item['category']); ?></span>
                                <span><?php echo formatDate($item['createdAt']); ?></span>
                            </div>
                        </div>
                    </a>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if($pages > 1): ?>
            <div class="pagination">
                <?php if($page > 1): ?>
                    <a href="home.php?page=1<?php echo $type ? '&type=' . urlencode($type) : ''; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?>" class="page-link">
                        <i class="fas fa-chevron-left"></i> First
                    </a>
                    <a href="home.php?page=<?php echo $page - 1; ?><?php echo $type ? '&type=' . urlencode($type) : ''; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?>" class="page-link">
                        Previous
                    </a>
                <?php endif; ?>

                <span class="page-info">Page <?php echo $page; ?> of <?php echo $pages; ?></span>

                <?php if($page < $pages): ?>
                    <a href="home.php?page=<?php echo $page + 1; ?><?php echo $type ? '&type=' . urlencode($type) : ''; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?>" class="page-link">
                        Next
                    </a>
                    <a href="home.php?page=<?php echo $pages; ?><?php echo $type ? '&type=' . urlencode($type) : ''; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?>" class="page-link">
                        Last <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
