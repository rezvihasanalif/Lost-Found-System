<?php 
session_start();
include("config/db.php");

// Redirect to login if not authenticated
if(!isset($_SESSION['user'])){
    header("Location: authentication/login.php");
    exit;
}

$user = $_SESSION['user'];
$message = '';
$messageType = '';

if(isset($_POST['add'])){
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $desc = mysqli_real_escape_string($conn, trim($_POST['description']));
    $type = $_POST['type'];
    $category = $_POST['category'];
    $loc = mysqli_real_escape_string($conn, trim($_POST['location']));

    // Validate required fields
    if(empty($title) || empty($desc) || empty($type) || empty($category) || empty($loc)){
        $message = "Please fill in all required fields.";
        $messageType = "danger";
    } else {
        $photo = '';
        if(isset($_FILES['photo']) && $_FILES['photo']['error'] == 0){
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['photo']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if(in_array($ext, $allowed)){
                $newname = uniqid() . '.' . $ext;
                $destination = 'uploads/' . $newname;
                if(move_uploaded_file($_FILES['photo']['tmp_name'], $destination)){
                    $photo = $newname;
                }
            } else {
                $message = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
                $messageType = "danger";
            }
        }

        if($messageType != "danger"){
            $query = "INSERT INTO items (title, description, type, category, photo, location, user_id)
                      VALUES ('$title', '$desc', '$type', '$category', '$photo', '$loc', {$user['id']})";

            if(mysqli_query($conn, $query)){
                $message = "Item reported successfully!";
                $messageType = "success";
                echo "<script>
                setTimeout(function(){
                    window.location.href = 'user/dashboard.php';
                }, 2000);
                </script>";
            } else {
                $message = "Error reporting item: " . mysqli_error($conn);
                $messageType = "danger";
            }
        }
    }
}

include("header.php");
?>

<div class="dashboard-header">
    <div class="d-flex align-items-center justify-content-between gap-3">
        <a href="user/dashboard.php" class="btn btn-secondary btn-lg">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>

        <div>
            <h2 class="text-center mb-0">Report Lost or Found Item</h2>
            <p class="text-center text-muted mb-0">Help the community by reporting lost or found items</p>
        </div>

        <div></div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <?php if(!empty($message)){ ?>
                    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                        <i class="fas fa-<?php echo ($messageType=='success')?'check-circle':'exclamation-circle'; ?> me-2"></i>
                        <?php echo htmlspecialchars($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php } ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="title" class="form-label">Item Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="e.g., Blue Backpack, iPhone 14" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" required>
                        <small class="form-text text-muted">Be specific about what the item is</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                            <select class="form-control" id="type" name="type" required>
                                <option value="">-- Select Type --</option>
                                <option value="lost" <?php echo (isset($_POST['type']) && $_POST['type']=='lost')?'selected':''; ?>>Lost Item</option>
                                <option value="found" <?php echo (isset($_POST['type']) && $_POST['type']=='found')?'selected':''; ?>>Found Item</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-control" id="category" name="category" required>
                                <option value="">-- Select Category --</option>
                                <option value="stationery" <?php echo (isset($_POST['category']) && $_POST['category']=='stationery')?'selected':''; ?>>📚 Stationery Products</option>
                                <option value="documents" <?php echo (isset($_POST['category']) && $_POST['category']=='documents')?'selected':''; ?>>📄 Documents</option>
                                <option value="academic" <?php echo (isset($_POST['category']) && $_POST['category']=='academic')?'selected':''; ?>>🎓 Academic Equipment</option>
                                <option value="gadgets" <?php echo (isset($_POST['category']) && $_POST['category']=='gadgets')?'selected':''; ?>>📱 Gadgets & Electronics</option>
                                <option value="others" <?php echo (isset($_POST['category']) && $_POST['category']=='others')?'selected':''; ?>>📦 Others</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="4" placeholder="Describe the item in detail (color, brand, condition, etc.)..." required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="location" name="location" placeholder="e.g., Library, Main Building, Room 201" value="<?php echo isset($_POST['location']) ? htmlspecialchars($_POST['location']) : ''; ?>" required>
                        <small class="form-text text-muted">Where was the item lost/found?</small>
                    </div>

                    <div class="mb-3">
                        <label for="photo" class="form-label">Upload Photo (Optional)</label>
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                        <small class="form-text text-muted">Supported formats: JPG, JPEG, PNG, GIF (Max 5MB)</small>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> Your contact information will be automatically included from your account profile. Other users can contact you through your registered email.
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" name="add" class="btn btn-success btn-lg">
                            <i class="fas fa-check-circle me-2"></i>Report Item
                        </button>
                        <a href="user/dashboard.php" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include("footer.php"); ?>
