<?php
session_start();
include("config/db.php");

$res = mysqli_query($conn,"SELECT * FROM items ORDER BY id DESC");
?>
<?php include("header.php"); ?>

<div class="dashboard-header">
    <div class="d-flex align-items-center justify-content-between gap-3">
        <div>
            <h2 class="text-center mb-0">All Lost & Found Items</h2>
            <p class="text-center text-muted mb-0">Browse all reported lost and found items from the community.</p>
        </div>

        <div class="d-flex gap-2">
            <a href="dashboard.php" class="btn btn-outline-primary">
                <i class="fas fa-sync me-1"></i>Refresh
            </a>
        </div>
    </div>
</div>

<div class="search-container">
    <div class="search-wrapper">
        <input type="text" id="search-input" class="search-input" placeholder="Search items by title, description, location, or type..." onkeyup="searchItems()">
        <i class="fas fa-search search-icon"></i>
        <button id="clear-search" class="clear-search" title="Clear search">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="search-filters">
        <button class="filter-btn active" data-filter="all">All Items</button>
        <button class="filter-btn" data-filter="lost">Lost Items</button>
        <button class="filter-btn" data-filter="found">Found Items</button>
        <select id="category-filter" class="category-filter">
            <option value="all">All Categories</option>
            <option value="stationery">📚 Stationery</option>
            <option value="documents">📄 Documents</option>
            <option value="academic">🎓 Academic</option>
            <option value="gadgets">📱 Gadgets</option>
            <option value="others">📦 Others</option>
        </select>
    </div>
    <div id="search-results" class="search-results"></div>
</div>

<div class="row g-3">
<?php while($row = mysqli_fetch_assoc($res)){ ?>
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm item-card" data-type="<?php echo $row['type']; ?>" data-category="<?php echo isset($row['category']) ? $row['category'] : 'others'; ?>" data-title="<?php echo htmlspecialchars(strtolower($row['title'])); ?>" data-description="<?php echo htmlspecialchars(strtolower($row['description'])); ?>" data-location="<?php echo htmlspecialchars(strtolower($row['location'])); ?>">
            <div class="card-body">
                <?php if($row['photo']){ ?>
                    <img src="uploads/<?php echo htmlspecialchars($row['photo']); ?>" class="card-img-top mb-3" alt="Item photo">
                <?php } else { ?>
                    <div class="card-image-placeholder mb-3">
                        <i class="fas fa-image"></i>
                        <span>No photo provided</span>
                    </div>
                <?php } ?>
                <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                <div class="item-meta mb-2">
                    <span class="badge bg-<?php echo ($row['type']=='lost')?'danger':'success'; ?> me-2">
                        <?php echo strtoupper($row['type']); ?>
                    </span>
                    <span class="badge bg-secondary">
                        <?php
                        $categories = [
                            'stationery' => '📚 Stationery',
                            'documents' => '📄 Documents',
                            'academic' => '🎓 Academic',
                            'gadgets' => '📱 Gadgets',
                            'others' => '📦 Others'
                        ];
                        echo isset($row['category']) ? $categories[$row['category']] ?? '📦 Others' : '📦 Others';
                        ?>
                    </span>
                </div>
                <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>

                <p class="mt-2 text-muted">📍 <?php echo htmlspecialchars($row['location']); ?></p>

                <div class="mt-3">
                    <a href="item-detail.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary btn-sm me-2">
                        <i class="fas fa-eye me-1"></i>View Details
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
</div>

<?php include("footer.php"); ?>