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
    $fullName = sanitize($_POST['fullName'] ?? '', $conn);
    $email = sanitize($_POST['email'] ?? '', $conn);
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['passwordConfirm'] ?? '';

    // Validation
    if(empty($fullName)) {
        $errors[] = 'Full Name is required.';
    }
    if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required.';
    }

    // Check if email already exists (and is not current user's email)
    if($email !== $user['email']) {
        $checkEmail = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
        if(mysqli_num_rows($checkEmail) > 0) {
            $errors[] = 'Email already registered by another user.';
        }
    }

    // Check password if provided
    if(!empty($password)) {
        if(strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters.';
        }
        if($password !== $passwordConfirm) {
            $errors[] = 'Passwords do not match.';
        }
    }

    // Update if no errors
    if(empty($errors)) {
        $avatar = generateInitials($fullName);
        $updateFields = [
            "fullName = '$fullName'",
            "email = '$email'",
            "avatar = '$avatar'"
        ];

        if(!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $updateFields[] = "password = '$hashedPassword'";
        }

        $updateQuery = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = {$user['id']}";

        if(mysqli_query($conn, $updateQuery)) {
            // Update session
            $_SESSION['user'] = [
                'id' => $user['id'],
                'fullName' => $fullName,
                'email' => $email,
                'avatar' => $avatar
            ];
            $user = $_SESSION['user'];
            $success = true;
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
    <title>Settings - Lost & Found</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container">
        <div class="form-container" style="max-width: 500px;">
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 30px;">
                <div style="width: 60px; height: 60px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: bold;">
                    <?php echo esc_html($user['avatar']); ?>
                </div>
                <div>
                    <h1 style="margin: 0; font-size: 24px;"><?php echo esc_html($user['fullName']); ?></h1>
                    <p style="margin: 0; color: var(--muted-text);">Settings</p>
                </div>
            </div>

            <?php if($success): ?>
                <div class="form-success">
                    <i class="fas fa-check-circle me-2"></i>Settings updated successfully!
                </div>
            <?php endif; ?>

            <?php if(!empty($errors)): ?>
                <div class="form-error">
                    <ul>
                        <?php foreach($errors as $error): ?>
                            <li><?php echo esc_html($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST">
                <!-- Full Name -->
                <div class="form-group">
                    <label for="fullName">Full Name</label>
                    <input type="text" id="fullName" name="fullName" value="<?php echo esc_html($user['fullName']); ?>" required>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="<?php echo esc_html($user['email']); ?>" required>
                </div>

                <!-- Password Section -->
                <div style="margin-top: 30px; padding-top: 30px; border-top: 1px solid var(--border-light);">
                    <h2 style="font-size: 18px; margin-bottom: 20px;">Change Password (Optional)</h2>

                    <!-- New Password -->
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" id="password" name="password" placeholder="Leave blank to keep current password">
                        <small style="color: var(--muted-text);">Minimum 6 characters</small>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label for="passwordConfirm">Confirm Password</label>
                        <input type="password" id="passwordConfirm" name="passwordConfirm" placeholder="Repeat new password">
                    </div>
                </div>

                <!-- Buttons -->
                <div class="form-actions" style="margin-top: 30px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                    <a href="home.php" class="btn btn-outline">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                </div>
            </form>

            <!-- Account Section -->
            <div style="margin-top: 30px; padding-top: 30px; border-top: 1px solid var(--border-light);">
                <h2 style="font-size: 18px; margin-bottom: 20px;">Account</h2>
                <p style="color: var(--muted-text); font-size: 14px; margin-bottom: 15px;">
                    <strong>Member Since:</strong> <?php echo formatDate($user['createdAt'] ?? date('Y-m-d')); ?>
                </p>
                <a href="logout.php" class="btn" style="background: #DC2626; color: white; width: 100%; justify-content: center;">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
