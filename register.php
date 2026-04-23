<?php
session_start();
require_once 'config/db.php';

// If already logged in, redirect to home
if(isset($_SESSION['user'])) {
    header('Location: home.php');
    exit;
}

$errors = [];
$fullName = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = sanitize($_POST['fullName'] ?? '', $conn);
    $email = sanitize($_POST['email'] ?? '', $conn);
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['passwordConfirm'] ?? '';

    // Validation
    if (empty($fullName)) {
        $errors[] = 'Full Name is required.';
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required.';
    }
    if (empty($password) || strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }
    if ($password !== $passwordConfirm) {
        $errors[] = 'Passwords do not match.';
    }

    // Check if email already exists
    if (empty($errors)) {
        $checkEmail = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
        if (mysqli_num_rows($checkEmail) > 0) {
            $errors[] = 'Email already registered.';
        }
    }

    // Register user if no errors
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $avatar = generateInitials($fullName);
        
        $query = "INSERT INTO users (fullName, email, password, avatar) 
                  VALUES ('$fullName', '$email', '$hashedPassword', '$avatar')";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = 'Account created successfully! Please login.';
            header('Location: login.php');
            exit;
        } else {
            $errors[] = 'Registration failed: ' . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Lost & Found</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1A2B5F 0%, #2563EB 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 420px;
        }

        .card {
            background: #FFFFFF;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 40px;
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo h1 {
            color: #1A2B5F;
            font-size: 28px;
            margin-bottom: 5px;
        }

        .logo p {
            color: #6B7280;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #1A2B5F;
            font-weight: 500;
            font-size: 14px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #E5E7EB;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #2563EB;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .errors {
            background: #FEE2E2;
            color: #DC2626;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .errors li {
            margin-bottom: 8px;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: #2563EB;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #1D4ED8;
        }

        .link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #6B7280;
        }

        .link a {
            color: #2563EB;
            text-decoration: none;
            font-weight: 600;
        }

        .link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="logo">
                <h1>📍 Lost & Found</h1>
                <p>Create your account</p>
            </div>

            <?php if (!empty($errors)): ?>
                <ul class="errors">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo esc_html($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="fullName">Full Name</label>
                    <input type="text" id="fullName" name="fullName" value="<?php echo esc_html($fullName); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="<?php echo esc_html($email); ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <small style="color: #6B7280; display: block; margin-top: 5px;">Minimum 6 characters</small>
                </div>

                <div class="form-group">
                    <label for="passwordConfirm">Confirm Password</label>
                    <input type="password" id="passwordConfirm" name="passwordConfirm" required>
                </div>

                <button type="submit" class="btn">
                    <i class="fas fa-user-plus me-2"></i>Create Account
                </button>
            </form>

            <div class="link">
                Already have an account? <a href="login.php">Login</a>
            </div>
        </div>
    </div>
</body>
</html>
