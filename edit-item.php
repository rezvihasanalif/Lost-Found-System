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

// Verify ownership
$query = "SELECT * FROM items WHERE id = $itemId AND reportedBy = {$user['id']}";
$result = mysqli_query($conn, $query);
$item = mysqli_fetch_assoc($result);

if(!$item) {
    header('Location: my-reports.php');
    exit;
}

$errors = [];

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = sanitize($_POST['type'] ?? '', $conn);
    $title = sanitize($_POST['title'] ?? '', $conn);
    $category = sanitize($_POST['category'] ?? '', $conn);
    $description = sanitize($_POST['description'] ?? '', $conn);
    $location = sanitize($_POST['location'] ?? '', $conn);
    $date = sanitize($_POST['date'] ?? '', $conn);

    // Validation
    if(empty($type) || !in_array($type, ['lost', 'found'])) {
        $errors[] = 'Please select whether the item is Lost or Found.';
    }
    if(empty($title) || strlen($title) < 3) {
        $errors[] = 'Title must be at least 3 characters.';
    }
    if(empty($category) || !array_key_exists($category, CATEGORIES)) {
        $errors[] = 'Please select a valid category.';
    }
    if(empty($description) || strlen($description) < 10) {
        $errors[] = 'Description must be at least 10 characters.';
    }
    if(empty($location)) {
        $errors[] = 'Location is required.';
    }
    if(empty($date)) {
        $errors[] = 'Date and time are required.';
    }

    // Handle file upload
    $photo = $item['photo'];
    if(!empty($_FILES['photo']['name'])) {
        $file = $_FILES['photo'];
        $ext = getFileExtension($file['name']);

        if(!in_array($ext, ALLOWED_EXTENSIONS)) {
            $errors[] = 'Only JPG, JPEG, PNG, and GIF files are allowed.';
        } elseif($file['size'] > MAX_FILE_SIZE) {
            $errors[] = 'File size must not exceed 5MB.';
        } else {
            // Delete old photo
            if($item['photo'] && file_exists(UPLOAD_DIR . $item['photo'])) {
                unlink(UPLOAD_DIR . $item['photo']);
            }

            $newFilename = generateUniqueFilename($file['name']);
            $uploadPath = UPLOAD_DIR . $newFilename;

            if(move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $photo = $newFilename;
            } else {
                $errors[] = 'Failed to upload photo.';
            }
        }
    }

    // Update if no errors
    if(empty($errors)) {
        $datetime = DateTime::createFromFormat('Y-m-d\TH:i', $date)->format('Y-m-d H:i:00');
        $photoVal = $photo ? "'$photo'" : 'NULL';

        $updateQuery = "UPDATE items SET title = '$title', description = '$description', type = '$type', 
                        category = '$category', location = '$location', date = '$datetime', photo = $photoVal 
                        WHERE id = $itemId AND reportedBy = {$user['id']}";

        if(mysqli_query($conn, $updateQuery)) {
            header('Location: my-reports.php?updated=1');
            exit;
        } else {
            $errors[] = 'Database error: ' . mysqli_error($conn);
        }
    }
}

// Format datetime for input
$dateTimeValue = date('Y-m-d\TH:i', strtotime($item['date']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item - Lost & Found</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container">
        <div class="form-container">
            <h1>Edit Item Report</h1>
            <p>Update the details of your reported item.</p>

            <?php if(!empty($errors)): ?>
                <div class="form-error">
                    <ul>
                        <?php foreach($errors as $error): ?>
                            <li><?php echo esc_html($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <!-- Type Toggle -->
                <div class="type-toggle">
                    <div class="type-option lost">
                        <input type="radio" id="type_lost" name="type" value="lost" <?php echo ($item['type'] === 'lost') ? 'checked' : ''; ?> required>
                        <label for="type_lost">
                            <i class="fas fa-magnifying-glass me-2"></i>Lost
                        </label>
                    </div>
                    <div class="type-option found">
                        <input type="radio" id="type_found" name="type" value="found" <?php echo ($item['type'] === 'found') ? 'checked' : ''; ?> required>
                        <label for="type_found">
                            <i class="fas fa-gift me-2"></i>Found
                        </label>
                    </div>
                </div>

                <!-- Title -->
                <div class="form-group">
                    <label for="title">Item Title</label>
                    <input type="text" id="title" name="title" value="<?php echo esc_html($item['title']); ?>" required>
                </div>

                <!-- Category -->
                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category" class="form-select" required>
                        <option value="">-- Select a category --</option>
                        <?php foreach(CATEGORIES as $key => $label): ?>
                            <option value="<?php echo esc_html($key); ?>" <?php echo ($item['category'] === $key) ? 'selected' : ''; ?>>
                                <?php echo esc_html($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" required><?php echo esc_html($item['description']); ?></textarea>
                </div>

                <!-- Location -->
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" value="<?php echo esc_html($item['location']); ?>" required>
                </div>

                <!-- Date & Time -->
                <div class="form-group">
                    <label for="date">Date & Time</label>
                    <input type="datetime-local" id="date" name="date" value="<?php echo esc_html($dateTimeValue); ?>" required>
                </div>

                <!-- Photo Upload -->
                <div class="form-group">
                    <label for="photo">Item Photo (Optional)</label>
                    <?php if($item['photo']): ?>
                        <div style="margin-bottom: 15px;">
                            <img src="uploads/<?php echo esc_html($item['photo']); ?>" alt="Current photo" style="max-width: 200px; border-radius: 6px;">
                            <p style="margin-top: 10px; font-size: 12px; color: #6B7280;">Current photo - Upload a new one to replace</p>
                        </div>
                    <?php endif; ?>
                    <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/gif">
                    <small style="color: #6B7280;">JPG, PNG, or GIF up to 5MB</small>
                </div>

                <!-- Buttons -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Item
                    </button>
                    <a href="my-reports.php" class="btn btn-outline">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
