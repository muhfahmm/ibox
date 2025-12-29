<?php
session_start();
require_once '../../db.php';

// Redirect jika belum login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../../../auth/login.php");
    exit();
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $delete_query = "DELETE FROM admin_produk_populer WHERE id = $id";
    if (mysqli_query($db, $delete_query)) {
        $_SESSION['success_message'] = "Produk berhasil dihapus dari daftar populer!";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus produk: " . mysqli_error($db);
    }
    
    header("Location: produk-populer.php");
    exit();
}

// Handle update urutan
if (isset($_POST['update_urutan'])) {
    foreach ($_POST['urutan'] as $id => $urutan) {
        $id = intval($id);
        $urutan = intval($urutan);
        mysqli_query($db, "UPDATE admin_produk_populer SET urutan = $urutan WHERE id = $id");
    }
    $_SESSION['success_message'] = "Urutan produk populer berhasil diperbarui!";
    header("Location: produk-populer.php");
    exit();
}

// Hitung jumlah produk per kategori untuk sidebar
$mac_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM admin_produk_mac"))['total'];
$iphone_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM admin_produk_iphone"))['total'];
$ipad_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM admin_produk_ipad"))['total'];
$watch_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM admin_produk_watch"))['total'];
$music_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM admin_produk_music"))['total'];
$aksesoris_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM admin_produk_aksesoris"))['total'];
$airtag_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM admin_produk_airtag"))['total'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk Populer - Admin iBox</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Gunakan style yang sama dengan image-slider.php */
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
            width: 280px;
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
            padding: 30px;
            display: flex;
            flex-direction: column;
        }

        /* Page Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        .page-title h1 {
            font-size: 28px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .page-title p {
            color: #7f8c8d;
            font-size: 14px;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-primary {
            background-color: #4a6cf7;
            color: white;
        }

        .btn-primary:hover {
            background-color: #3a5ce5;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(74, 108, 247, 0.3);
        }

        .btn-secondary {
            background-color: #f8f9fa;
            color: #495057;
            border: 1px solid #dee2e6;
        }

        .btn-secondary:hover {
            background-color: #e9ecef;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .btn-warning:hover {
            background-color: #e0a800;
        }

        /* Alert Messages */
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .stat-icon.total {
            background-color: rgba(74, 108, 247, 0.1);
            color: #4a6cf7;
        }

        .stat-icon.ipad {
            background-color: rgba(33, 150, 243, 0.1);
            color: #2196f3;
        }

        .stat-icon.iphone {
            background-color: rgba(233, 30, 99, 0.1);
            color: #e91e63;
        }

        .stat-icon.mac {
            background-color: rgba(0, 150, 136, 0.1);
            color: #009688;
        }

        .stat-info h3 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .stat-info p {
            color: #7f8c8d;
            font-size: 14px;
        }

        /* Table Styles */
        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table thead {
            background: linear-gradient(135deg, #4a6cf7 0%, #6a11cb 100%);
            color: white;
        }

        table thead th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        table tbody tr {
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s ease;
        }

        table tbody tr:hover {
            background-color: #f8f9fa;
        }

        table tbody td {
            padding: 15px;
            font-size: 15px;
            vertical-align: middle;
        }

        /* Product Image */
        .product-image {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid #e0e0e0;
        }

        /* Category Badge */
        .category-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }

        .badge-iphone {
            background-color: rgba(233, 30, 99, 0.1);
            color: #e91e63;
        }

        .badge-ipad {
            background-color: rgba(33, 150, 243, 0.1);
            color: #2196f3;
        }

        .badge-mac {
            background-color: rgba(0, 150, 136, 0.1);
            color: #009688;
        }

        .badge-music {
            background-color: rgba(156, 39, 176, 0.1);
            color: #9c27b0;
        }

        .badge-watch {
            background-color: rgba(255, 152, 0, 0.1);
            color: #ff9800;
        }

        .badge-aksesoris {
            background-color: rgba(121, 85, 72, 0.1);
            color: #795548;
        }

        .badge-airtag {
            background-color: rgba(96, 125, 139, 0.1);
            color: #607d8b;
        }

        /* Urutan Input */
        .urutan-input {
            width: 60px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: center;
        }

        /* Action Buttons in Table */
        .action-buttons-cell {
            display: flex;
            gap: 8px;
        }

        .btn-icon {
            width: 36px;
            height: 36px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            text-decoration: none;
        }

        .btn-view {
            background-color: rgba(76, 175, 80, 0.1);
            color: #4caf50;
        }

        .btn-view:hover {
            background-color: #4caf50;
            color: white;
        }

        .btn-edit {
            background-color: rgba(33, 150, 243, 0.1);
            color: #2196f3;
        }

        .btn-edit:hover {
            background-color: #2196f3;
            color: white;
        }

        .btn-delete {
            background-color: rgba(244, 67, 54, 0.1);
            color: #f44336;
        }

        .btn-delete:hover {
            background-color: #f44336;
            color: white;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
        }

        .empty-icon {
            font-size: 60px;
            color: #bdc3c7;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            color: #7f8c8d;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #95a5a6;
            margin-bottom: 20px;
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

        .badge-success {
            background-color: #28a745;
        }

        .badge-danger {
            background-color: #dc3545;
        }

        .badge-info {
            background-color: #17a2b8;
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
                padding: 20px;
            }

            .stats-container {
                grid-template-columns: 1fr;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            table {
                display: block;
                overflow-x: auto;
            }

            .action-buttons-cell {
                flex-wrap: wrap;
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
                            <a href="../../products-panel/categories/kategori.php">
                                <i class="fas fa-tags"></i>
                                <span>Kategori</span>
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
                            </a>
                        </li>
                        <li class="active">
                            <a href="produk-populer.php">
                                <i class="fas fa-fire"></i>
                                <span>Produk Apple Populer</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../homepage-panel/produk-terbaru/produk-terbaru.php">
                                <i class="fas fa-bolt"></i>
                                <span>Produk Terbaru</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../homepage-panel/image-grid/image-grid.php">
                                <i class="fas fa-th"></i>
                                <span>Image grid</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../homepage-panel/trade-in/trade-in.php">
                                <i class="fas fa-exchange-alt"></i>
                                <span>Trade in</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../homepage-panel/aksesori-unggulan/aksesori-unggulan.php">
                                <i class="fas fa-gem"></i>
                                <span>Aksesori unggulan</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../homepage-panel/checkout-sekarang/chekout-sekarang.php">
                                <i class="fas fa-shopping-bag"></i>
                                <span>Checkout sekarang</span>
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
                        <li>
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
                    <a href="../../../auth/logout.php" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Page Header -->
            <div class="page-header">
                <div class="page-title">
                    <h1><i class="fas fa-fire"></i> Kelola Produk Populer</h1>
                    <p>Kelola produk yang ditampilkan di halaman utama sebagai produk populer</p>
                </div>
                <div class="action-buttons">
                    <a href="produk-populer.php" class="btn btn-secondary">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </a>
                    <a href="add-produk-populer.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Produk Populer
                    </a>
                </div>
            </div>

            <!-- Alert Messages -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php
                    echo $_SESSION['success_message'];
                    unset($_SESSION['success_message']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php
                    echo $_SESSION['error_message'];
                    unset($_SESSION['error_message']);
                    ?>
                </div>
            <?php endif; ?>

            <!-- Stats Cards -->
            <div class="stats-container">
                <?php
                // Hitung statistik produk populer
                $total_query = "SELECT COUNT(*) as total FROM admin_produk_populer";
                $query = "
                    SELECT 
                        tipe_produk,
                        COUNT(*) as jumlah
                    FROM admin_produk_populer 
                    GROUP BY tipe_produk
                ";
                
                $total_result = mysqli_query($db, $total_query);
                $type_result = mysqli_query($db, $query);
                
                $total = mysqli_fetch_assoc($total_result)['total'];
                $type_counts = array();
                while ($row = mysqli_fetch_assoc($type_result)) {
                    $type_counts[$row['tipe_produk']] = $row['jumlah'];
                }
                ?>
                <div class="stat-card">
                    <div class="stat-icon total">
                        <i class="fas fa-fire"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $total; ?></h3>
                        <p>Total Produk Populer</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon iphone">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $type_counts['iphone'] ?? 0; ?></h3>
                        <p>iPhone Populer</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon ipad">
                        <i class="fas fa-tablet-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $type_counts['ipad'] ?? 0; ?></h3>
                        <p>iPad Populer</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon mac">
                        <i class="fas fa-laptop"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $type_counts['mac'] ?? 0; ?></h3>
                        <p>Mac Populer</p>
                    </div>
                </div>
            </div>

            <!-- Table Container -->
            <div class="table-container">
                <form method="POST" action="">
                <?php
                // Ambil data produk populer dengan detail produk
                $query = "
                    SELECT 
                        pop.*,
                        CASE pop.tipe_produk
                            WHEN 'iphone' THEN (SELECT nama_produk FROM admin_produk_iphone WHERE id = pop.produk_id)
                            WHEN 'ipad' THEN (SELECT nama_produk FROM admin_produk_ipad WHERE id = pop.produk_id)
                            WHEN 'mac' THEN (SELECT nama_produk FROM admin_produk_mac WHERE id = pop.produk_id)
                            WHEN 'music' THEN (SELECT nama_produk FROM admin_produk_music WHERE id = pop.produk_id)
                            WHEN 'watch' THEN (SELECT nama_produk FROM admin_produk_watch WHERE id = pop.produk_id)
                            WHEN 'aksesoris' THEN (SELECT nama_produk FROM admin_produk_aksesoris WHERE id = pop.produk_id)
                            WHEN 'airtag' THEN (SELECT nama_produk FROM admin_produk_airtag WHERE id = pop.produk_id)
                        END as nama_produk,
                        CASE pop.tipe_produk
                            WHEN 'iphone' THEN (SELECT MIN(harga) FROM admin_produk_iphone_kombinasi WHERE produk_id = pop.produk_id)
                            WHEN 'ipad' THEN (SELECT MIN(harga) FROM admin_produk_ipad_kombinasi WHERE produk_id = pop.produk_id)
                            WHEN 'mac' THEN (SELECT MIN(harga) FROM admin_produk_mac_kombinasi WHERE produk_id = pop.produk_id)
                            WHEN 'music' THEN (SELECT MIN(harga) FROM admin_produk_music_kombinasi WHERE produk_id = pop.produk_id)
                            WHEN 'watch' THEN (SELECT MIN(harga) FROM admin_produk_watch_kombinasi WHERE produk_id = pop.produk_id)
                            WHEN 'aksesoris' THEN (SELECT MIN(harga) FROM admin_produk_aksesoris_kombinasi WHERE produk_id = pop.produk_id)
                            WHEN 'airtag' THEN (SELECT MIN(harga) FROM admin_produk_airtag_kombinasi WHERE produk_id = pop.produk_id)
                        END as harga_mulai,
                        CASE pop.tipe_produk
                            WHEN 'iphone' THEN (SELECT foto_thumbnail FROM admin_produk_iphone_gambar WHERE produk_id = pop.produk_id LIMIT 1)
                            WHEN 'ipad' THEN (SELECT foto_thumbnail FROM admin_produk_ipad_gambar WHERE produk_id = pop.produk_id LIMIT 1)
                            WHEN 'mac' THEN (SELECT foto_thumbnail FROM admin_produk_mac_gambar WHERE produk_id = pop.produk_id LIMIT 1)
                            WHEN 'music' THEN (SELECT foto_thumbnail FROM admin_produk_music_gambar WHERE produk_id = pop.produk_id LIMIT 1)
                            WHEN 'watch' THEN (SELECT foto_thumbnail FROM admin_produk_watch_gambar WHERE produk_id = pop.produk_id LIMIT 1)
                            WHEN 'aksesoris' THEN (SELECT foto_thumbnail FROM admin_produk_aksesoris_gambar WHERE produk_id = pop.produk_id LIMIT 1)
                            WHEN 'airtag' THEN (SELECT foto_thumbnail FROM admin_produk_airtag_gambar WHERE produk_id = pop.produk_id LIMIT 1)
                        END as foto_thumbnail
                    FROM admin_produk_populer pop
                    ORDER BY pop.urutan ASC
                ";
                
                $result = mysqli_query($db, $query);

                if (mysqli_num_rows($result) > 0) {
                ?>
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Gambar</th>
                                <th>Produk</th>
                                <th>Kategori</th>
                                <th>Harga Mulai</th>
                                <th>Urutan</th>
                                <th>Ditambahkan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($result)) {
                                $image_path = $row['foto_thumbnail'] ? '../../../uploads/' . $row['foto_thumbnail'] : '../../../uploads/default-product.jpg';
                                $category_class = 'badge-' . $row['tipe_produk'];
                                $category_name = ucfirst($row['tipe_produk']);
                                $price = 'Rp ' . number_format($row['harga_mulai'], 0, ',', '.');
                                $date = date('d/m/Y', strtotime($row['created_at']));
                            ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td>
                                        <img src="<?php echo $image_path; ?>"
                                            class="product-image"
                                            alt="<?php echo htmlspecialchars($row['nama_produk']); ?>"
                                            title="<?php echo htmlspecialchars($row['nama_produk']); ?>">
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($row['nama_produk']); ?></strong><br>
                                        <small>ID: <?php echo $row['produk_id']; ?></small>
                                    </td>
                                    <td>
                                        <span class="category-badge <?php echo $category_class; ?>">
                                            <?php echo $category_name; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong><?php echo $price; ?></strong>
                                    </td>
                                    <td>
                                        <input type="number" 
                                               name="urutan[<?php echo $row['id']; ?>]" 
                                               value="<?php echo $row['urutan']; ?>" 
                                               class="urutan-input"
                                               min="1"
                                               max="100">
                                    </td>
                                    <td><?php echo $date; ?></td>
                                    <td>
                                        <div class="action-buttons-cell">
                                            <a href="view-produk-populer.php?id=<?php echo $row['id']; ?>"
                                                class="btn-icon btn-view"
                                                title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="edit-produk-populer.php?id=<?php echo $row['id']; ?>"
                                                class="btn-icon btn-edit"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="produk-populer.php?action=delete&id=<?php echo $row['id']; ?>"
                                                class="btn-icon btn-delete"
                                                title="Hapus"
                                                onclick="return confirm('Yakin ingin menghapus produk ini dari daftar populer?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    
                    <div style="margin-top: 20px; text-align: right;">
                        <button type="submit" name="update_urutan" class="btn btn-success">
                            <i class="fas fa-save"></i> Simpan Urutan
                        </button>
                    </div>
                <?php } else { ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-fire"></i>
                        </div>
                        <h3>Belum ada produk populer</h3>
                        <p>Tambahkan produk pertama ke daftar populer untuk ditampilkan di halaman utama</p>
                        <a href="add-produk-populer.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Tambah Produk Pertama
                        </a>
                    </div>
                <?php } ?>
                </form>
            </div>
        </main>
    </div>

    <script>
        // Confirm before delete
        function confirmDelete(event) {
            if (!confirm('Yakin ingin menghapus produk ini dari daftar populer?')) {
                event.preventDefault();
            }
        }

        // Validate urutan input
        document.querySelectorAll('.urutan-input').forEach(input => {
            input.addEventListener('change', function() {
                if (this.value < 1) {
                    this.value = 1;
                } else if (this.value > 100) {
                    this.value = 100;
                }
            });
        });
    </script>
</body>

</html>