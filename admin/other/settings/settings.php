<?php
session_start();
require_once '../../db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../../auth/login.php?error=not_logged_in');
    exit();
}

$admin_username = $_SESSION['admin_username'] ?? 'Admin';

// Hitung jumlah produk untuk sidebar
$iphone_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM admin_produk_iphone"))['total'];
$ipad_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM admin_produk_ipad"))['total'];
$mac_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM admin_produk_mac"))['total'];
$music_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM admin_produk_music"))['total'];
$watch_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM admin_produk_watch"))['total'];
$airtag_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM admin_produk_airtag"))['total'];
$aksesoris_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM admin_produk_aksesoris"))['total'];

// Hitung jumlah untuk Homepage Panel
$populer_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM home_produk_populer"))['total'];
$terbaru_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM home_produk_terbaru"))['total'];
$slider_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM home_image_slider"))['total'];
$grid_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM home_grid"))['total'];
$trade_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM home_trade_in"))['total'];
$aksesori_home_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM home_aksesori"))['total'];
$checkout_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM home_checkout"))['total'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel iBox</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Reset & Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f5f7fb;
            color: #333;
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 200px;
            background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
            color: #fff;
            display: flex;
            flex-direction: column;
            box-shadow: 5px 0 15px rgba(0, 0, 0, 0.1);
            z-index: 100;
        }

        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo i {
            font-size: 28px;
            color: #4a6cf7;
        }

        .logo h2 {
            font-size: 20px;
            font-weight: 600;
        }

        .sidebar-menu {
            flex: 1;
            padding: 20px 0;
            overflow-y: auto;
        }

        .menu-section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, 0.5);
            padding: 0 20px;
            margin-bottom: 15px;
        }

        .sidebar-menu ul {
            list-style: none;
        }

        .sidebar-menu ul li {
            margin-bottom: 5px;
        }

        .sidebar-menu ul li a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
        }

        .sidebar-menu ul li a:hover {
            background-color: rgba(255, 255, 255, 0.05);
            color: #fff;
            padding-left: 25px;
        }

        .sidebar-menu ul li.active a {
            background-color: rgba(74, 108, 247, 0.15);
            color: #fff;
            border-left: 4px solid #4a6cf7;
        }

        .sidebar-menu ul li a i {
            font-size: 18px;
            margin-right: 12px;
            width: 24px;
            text-align: center;
        }

        .sidebar-menu ul li a span {
            font-size: 15px;
            font-weight: 400;
        }

        .badge {
            background-color: #4a6cf7;
            color: white;
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 10px;
            margin-left: auto;
        }

        .badge-warning {
            background-color: #ff9800;
        }

        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-info h4 {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 2px;
        }

        .user-info p {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
        }

        .logout-btn {
            margin-left: auto;
            color: rgba(255, 255, 255, 0.6);
            font-size: 18px;
            transition: color 0.3s;
        }

        .logout-btn:hover {
            color: #fff;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
        }

        .welcome-container {
            text-align: center;
            max-width: 600px;
            padding: 40px;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .welcome-container h1 {
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .welcome-container p {
            font-size: 18px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .welcome-icon {
            font-size: 80px;
            color: #4a6cf7;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .admin-container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
            }

            .main-content {
                padding: 30px 20px;
            }

            .welcome-container h1 {
                font-size: 32px;
            }

            .welcome-icon {
                font-size: 60px;
            }
        }
    </style>
</head>

<body>
    <div class="admin-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <i class="fas fa-apple-alt"></i>
                    <h2>iBox Admin</h2>
                </div>
            </div>

            <div class="sidebar-menu">
                <div class="menu-section">
                    <h3 class="section-title">Panel Produk</h3>
                    <ul>
                        <li>
                            <a href="../../index.php">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../products-panel/ipad/ipad.php">
                                <i class="fas fa-tablet-alt"></i>
                                <span>iPad</span>
                                <span class="badge"><?php echo $ipad_count; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../products-panel/iphone/iphone.php">
                                <i class="fas fa-mobile-alt"></i>
                                <span>iPhone</span>
                                <span class="badge"><?php echo $iphone_count; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../products-panel/mac/mac.php">
                                <i class="fas fa-laptop"></i>
                                <span>Mac</span>
                                <span class="badge"><?php echo $mac_count; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../products-panel/music/music.php">
                                <i class="fas fa-headphones-alt"></i>
                                <span>Music</span>
                                <span class="badge"><?php echo $music_count; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../products-panel/watch/watch.php">
                                <i class="fas fa-clock"></i>
                                <span>Watch</span>
                                <span class="badge"><?php echo $watch_count; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../products-panel/aksesoris/aksesoris.php">
                                <i class="fas fa-toolbox"></i>
                                <span>Aksesoris</span>
                                <span class="badge"><?php echo $aksesoris_count; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../products-panel/airtag/airtag.php">
                                <i class="fas fa-tag"></i>
                                <span>AirTag</span>
                                <span class="badge"><?php echo $airtag_count; ?></span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="menu-section">
                    <h3 class="section-title">Homepage Panel</h3>
                    <ul>
                        <li>
                            <a href="../../homepage-panel/image-slider/image-slider.php">
                                <i class="fas fa-images"></i>
                                <span>Image slider</span>
                                <span class="badge"><?php echo $slider_count; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../homepage-panel/produk-populer/produk-populer.php">
                                <i class="fas fa-fire"></i>
                                <span>Produk Apple Populer</span>
                                <span class="badge"><?php echo $populer_count; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../homepage-panel/produk-terbaru/produk-terbaru.php">
                                <i class="fas fa-bolt"></i>
                                <span>Produk Terbaru</span>
                                <span class="badge"><?php echo $terbaru_count; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../homepage-panel/image-grid/image-grid.php">
                                <i class="fas fa-th"></i>
                                <span>Image grid</span>
                                <span class="badge"><?php echo $grid_count; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../homepage-panel/trade-in/trade-in.php">
                                <i class="fas fa-exchange-alt"></i>
                                <span>Trade in</span>
                                <span class="badge"><?php echo $trade_count; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../homepage-panel/aksesori-unggulan/aksesori-unggulan.php">
                                <i class="fas fa-gem"></i>
                                <span>Aksesori unggulan</span>
                                <span class="badge"><?php echo $aksesori_home_count; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../homepage-panel/checkout-sekarang/chekout-sekarang.php">
                                <i class="fas fa-shopping-bag"></i>
                                <span>Checkout sekarang</span>
                                <span class="badge"><?php echo $checkout_count; ?></span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="menu-section">
                    <h3 class="section-title">Lainnya</h3>
                    <ul>
                        <li>
                            <a href="../../other/users/users.php">
                                <i class="fas fa-users"></i>
                                <span>Pengguna</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../other/orders/order.php">
                                <i class="fas fa-shopping-cart"></i>
                                <span>Pesanan</span>
                                <span class="badge badge-warning">5</span>
                            </a>
                        </li>
                        <li class="active">
                            <a href="../../other/settings/settings.php">
                                <i class="fas fa-cog"></i>
                                <span>Pengaturan</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="sidebar-footer">
                <div class="user-profile">
                    <img src="https://ui-avatars.com/api/?name=Admin+iBox&background=4a6cf7&color=fff" alt="Admin">
                    <div class="user-info">
                        <h4>Admin iBox</h4>
                        <p>admin@ibox.co.id</p>
                    </div>
                    <a href="auth/logout.php" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="welcome-container">
                <div class="welcome-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h1>Selamat datang admin</h1>
                <p>Anda telah berhasil login ke panel administrasi iBox. Gunakan menu di sebelah kiri untuk mengelola sistem.</p>
            </div>
        </main>
    </div>
</body>

</html>