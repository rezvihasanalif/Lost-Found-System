<?php
session_start();
require_once 'config/db.php';

// If already logged in, redirect to home
if(isset($_SESSION['user'])) {
    header('Location: home.php');
    exit;
}

$error = '';
$email = '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '', $conn);
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Email and password are required.';
    } else {
        $query = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            if (password_verify($password, $user['password'])) {
                // Login successful
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'fullName' => $user['fullName'],
                    'email' => $user['email'],
                    'avatar' => $user['avatar']
                ];
                header('Location: home.php');
                exit;
            } else {
                $error = 'Invalid email or password.';
            }
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Lost & Found</title>
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

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #E5E7EB;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #2563EB;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .error {
            background: #FEE2E2;
            color: #DC2626;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .success {
            background: #DCFCE7;
            color: #15803D;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
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

        .forgot {
            text-align: right;
            margin-bottom: 20px;
        }

        .forgot a {
            color: #2563EB;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .forgot a:hover {
            text-decoration: underline;
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
                <p>Sign in to your account</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="error">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo esc_html($error); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="success">
                    <i class="fas fa-check-circle me-2"></i><?php echo esc_html($success); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="<?php echo esc_html($email); ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="forgot">
                    <a href="#" onclick="alert('Password reset not implemented yet'); return false;">Forgot password?</a>
                </div>

                <button type="submit" class="btn">
                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                </button>
            </form>

            <div class="link">
                Don't have an account? <a href="register.php">Create one</a>
            </div>
        </div>
    </div>
</body>
</html>
