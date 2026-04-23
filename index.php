<?php
session_start();
require_once 'config/db.php';

$isLoggedIn = isset($_SESSION['user']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost & Found - Find Your Lost Items</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #EFF6FF;
        }

        /* Navbar */
        nav {
            background: #1A2B5F;
            padding: 15px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            color: white;
            font-size: 24px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-buttons {
            display: flex;
            gap: 15px;
        }

        .btn-outline, .btn-primary {
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-outline {
            color: white;
            background: transparent;
            border: 1px solid white;
        }

        .btn-outline:hover {
            background: white;
            color: #1A2B5F;
        }

        .btn-primary {
            background: #2563EB;
            color: white;
        }

        .btn-primary:hover {
            background: #1D4ED8;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #1A2B5F 0%, #2563EB 100%);
            color: white;
            padding: 100px 20px;
            text-align: center;
        }

        .hero-content {
            max-width: 600px;
            margin: 0 auto;
        }

        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero p {
            font-size: 18px;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        .hero-btn {
            background: white;
            color: #1A2B5F;
            padding: 14px 40px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
            transition: all 0.3s;
            font-size: 16px;
        }

        .hero-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        /* Features Section */
        .features {
            max-width: 1200px;
            margin: 60px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            text-align: center;
        }

        .feature-card {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            font-size: 40px;
            color: #2563EB;
            margin-bottom: 15px;
        }

        .feature-card h3 {
            color: #1A2B5F;
            margin-bottom: 10px;
            font-size: 18px;
        }

        .feature-card p {
            color: #6B7280;
            font-size: 14px;
            line-height: 1.6;
        }

        /* Footer */
        footer {
            background: #1A2B5F;
            color: white;
            padding: 40px 20px;
            text-align: center;
            margin-top: 60px;
        }

        footer p {
            margin: 0;
            font-size: 14px;
            opacity: 0.9;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar-container {
                flex-direction: column;
                gap: 15px;
            }

            .hero h1 {
                font-size: 32px;
            }

            .hero p {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav>
        <div class="navbar-container">
            <div class="logo">
                <i class="fas fa-map-marker-alt"></i>
                Lost & Found
            </div>
            <div class="nav-buttons">
                <?php if ($isLoggedIn): ?>
                    <a href="home.php" class="btn-outline">
                        <i class="fas fa-list"></i>Browse Items
                    </a>
                    <a href="logout.php" class="btn-primary">
                        <i class="fas fa-sign-out-alt"></i>Logout
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn-outline">
                        <i class="fas fa-sign-in-alt"></i>Login
                    </a>
                    <a href="register.php" class="btn-primary">
                        <i class="fas fa-user-plus"></i>Register
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Find Lost Items Faster</h1>
            <p>A clean, organized system to report and find lost or found belongings in your community.</p>
            <?php if ($isLoggedIn): ?>
                <a href="home.php" class="hero-btn">
                    <i class="fas fa-arrow-right"></i>Browse Items Now
                </a>
            <?php else: ?>
                <a href="register.php" class="hero-btn">
                    <i class="fas fa-arrow-right"></i>Get Started
                </a>
            <?php endif; ?>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-search"></i>
            </div>
            <h3>Browse Items</h3>
            <p>Explore all reported lost and found items in your community with ease.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-plus-circle"></i>
            </div>
            <h3>Report Items</h3>
            <p>Quickly report a lost or found item with photos and detailed descriptions.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-handshake"></i>
            </div>
            <h3>Connect</h3>
            <p>Reach out to other community members to reunite items with their owners.</p>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>📍 Lost & Found System — Securely manage lost and found items across your community.</p>
    </footer>
</body>
</html>
