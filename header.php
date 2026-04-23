<!DOCTYPE html>
<html>
<head>
    <title>Lost & Found System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="/project/style.css" rel="stylesheet">
</head>
<body>

<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
?>

<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="/project/dashboard.php">Lost & Found</a>

        <div class="d-flex align-items-center gap-2">
            <button id="theme-toggle" class="theme-toggle" title="Toggle theme">
                <i class="fas fa-moon"></i>
            </button>
            
            <a href="/project/dashboard.php" class="btn btn-outline-light btn-sm me-2">
                <i class="fas fa-list me-1"></i>All Items
            </a>

            <?php if($user){ ?>
                <div class="dropdown">
                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-1"></i><?php echo htmlspecialchars($user['username']); ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                        <li><h6 class="dropdown-header">Account</h6></li>
                        <li><a class="dropdown-item" href="/project/user/manage_account.php"><i class="fas fa-cog me-2"></i>Manage Account</a></li>
                        <?php if($user['role'] == 'admin'){ ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/project/admin/dashboard.php"><i class="fas fa-shield-alt me-2"></i>Admin Panel</a></li>
                        <?php } ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="/project/authentication/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            <?php } else { ?>
                <a href="/project/authentication/login.php" class="btn btn-outline-light btn-sm me-2">
                    <i class="fas fa-sign-in-alt me-1"></i>Login
                </a>
                <a href="/project/authentication/register.php" class="btn btn-primary btn-sm">
                    <i class="fas fa-user-plus me-1"></i>Register
                </a>
            <?php } ?>
        </div>
    </div>
</nav>

<div class="container mt-4">
