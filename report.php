<?php
session_start();
require_once 'config/db.php';

if(!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];
$errors = [];
$success = false;

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
    } else {
        // Validate date format
        if(!DateTime::createFromFormat('Y-m-d\TH:i', $date)) {
            $errors[] = 'Invalid date format.';
        }
    }

    // Handle file upload
    $photo = null;
    if(!empty($_FILES['photo']['name'])) {
        $file = $_FILES['photo'];
        $ext = getFileExtension($file['name']);

        if(!in_array($ext, ALLOWED_EXTENSIONS)) {
            $errors[] = 'Only JPG, JPEG, PNG, and GIF files are allowed.';
        } elseif($file['size'] > MAX_FILE_SIZE) {
            $errors[] = 'File size must not exceed 5MB.';
        } else {
            // Create uploads directory if it doesn't exist
            if(!is_dir(UPLOAD_DIR)) {
                mkdir(UPLOAD_DIR, 0755, true);
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

    // Insert into database if no errors
    if(empty($errors)) {
        $datetime = DateTime::createFromFormat('Y-m-d\TH:i', $date)->format('Y-m-d H:i:00');
        $photoVal = $photo ? "'$photo'" : 'NULL';

        $query = "INSERT INTO items (title, description, type, category, location, date, photo, reportedBy) 
                  VALUES ('$title', '$description', '$type', '$category', '$location', '$datetime', $photoVal, {$user['id']})";

        if(mysqli_query($conn, $query)) {
            $success = true;
            // Reset form
            $_POST = [];
            header('Location: home.php?success=1');
            exit;
        } else {
            $errors[] = 'Database error: ' . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Item - Lost & Found</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container">
        <div class="form-container">
            <h1>Report a Lost or Found Item</h1>
            <p>Help the community by reporting an item you've lost or found.</p>

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
                        <input type="radio" id="type_lost" name="type" value="lost" <?php echo (($_POST['type'] ?? '') === 'lost') ? 'checked' : ''; ?> required>
                        <label for="type_lost">
                            <i class="fas fa-magnifying-glass me-2"></i>Lost
                        </label>
                    </div>
                    <div class="type-option found">
                        <input type="radio" id="type_found" name="type" value="found" <?php echo (($_POST['type'] ?? '') === 'found') ? 'checked' : ''; ?> required>
                        <label for="type_found">
                            <i class="fas fa-gift me-2"></i>Found
                        </label>
                    </div>
                </div>

                <!-- Title -->
                <div class="form-group">
                    <label for="title">Item Title</label>
                    <input type="text" id="title" name="title" placeholder="e.g., Silver iPhone 14 Pro" 
                           value="<?php echo esc_html($_POST['title'] ?? ''); ?>" required>
                    <small style="color: #6B7280;">Brief description of the item</small>
                </div>

                <!-- Category -->
                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category" class="form-select" required>
                        <option value="">-- Select a category --</option>
                        <?php foreach(CATEGORIES as $key => $label): ?>
                            <option value="<?php echo esc_html($key); ?>" <?php echo (($_POST['category'] ?? '') === $key) ? 'selected' : ''; ?>>
                                <?php echo esc_html($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" placeholder="Provide detailed information about the item, condition, color, brand, etc." required><?php echo esc_html($_POST['description'] ?? ''); ?></textarea>
                    <small style="color: #6B7280;">At least 10 characters</small>
                </div>

                <!-- Location -->
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" placeholder="e.g., Dhaka University Gate, Sector 8"
                           value="<?php echo esc_html($_POST['location'] ?? ''); ?>" required>
                    <small style="color: #6B7280;">Where the item was lost or found</small>
                </div>

                <!-- Date & Time -->
                <div class="form-group">
                    <label for="date">Date & Time</label>
                    <input type="datetime-local" id="date" name="date" 
                           value="<?php echo esc_html($_POST['date'] ?? ''); ?>" required>
                    <small style="color: #6B7280;">When the item was lost or found</small>
                </div>

                <!-- Photo Upload -->
                <div class="form-group">
                    <label for="photo">Item Photo (Optional)</label>
                    <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/gif" >
                    <small style="color: #6B7280;">JPG, PNG, or GIF up to 5MB</small>
                </div>

                <!-- Buttons -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check-circle me-2"></i>Submit Report
                    </button>
                    <a href="home.php" class="btn btn-outline">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        // Type toggle functionality
        document.querySelectorAll('.type-option input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                // Remove selected class from all options
                document.querySelectorAll('.type-option').forEach(option => {
                    option.classList.remove('selected');
                });
                // Add selected class to the checked option's parent
                if (this.checked) {
                    this.parentElement.classList.add('selected');
                }
            });
        });

        // Set initial state
        document.querySelectorAll('.type-option input[type="radio"]:checked').forEach(radio => {
            radio.parentElement.classList.add('selected');
        });
    </script>
</body>
</html>
