<?php
session_start();
require_once '../../db.php';

// Jika belum login, redirect ke login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../../auth/login.php?error=not_logged_in');
    exit();
}

$admin_username = $_SESSION['admin_username'] ?? 'Admin';

// Ambil data produk AirTag dengan kombinasi
$query = "SELECT p.*, 
                 COUNT(DISTINCT k.id) as total_kombinasi,
                 COUNT(DISTINCT g.id) as total_warna,
                 MIN(k.harga) as harga_terendah,
                 MAX(k.harga) as harga_tertinggi,
                 SUM(k.jumlah_stok) as total_stok
          FROM admin_produk_airtag p
          LEFT JOIN admin_produk_airtag_kombinasi k ON p.id = k.produk_id
          LEFT JOIN admin_produk_airtag_gambar g ON p.id = g.produk_id
          GROUP BY p.id
          ORDER BY p.id DESC";
$result = mysqli_query($db, $query);

// Hitung jumlah produk AirTag
$airtag_count = mysqli_num_rows($result);

// Hitung jumlah produk kategori lain untuk sidebar DAN AMBIL ID PERTAMA
$tables = [
    'iphone' => 'admin_produk_iphone',
    'ipad' => 'admin_produk_ipad',
    'mac' => 'admin_produk_mac',
    'watch' => 'admin_produk_watch',
    'music' => 'admin_produk_music',
    'aksesoris' => 'admin_produk_aksesoris'
];

// Array untuk menyimpan jumlah dan ID pertama setiap kategori
$category_data = [];

foreach ($tables as $key => $table_name) {
    $count_query = "SELECT COUNT(*) as total, MIN(id) as first_id FROM $table_name";
    $count_result = mysqli_query($db, $count_query);
    $data = mysqli_fetch_assoc($count_result);
    
    $category_data[$key] = [
        'count' => $data['total'],
        'first_id' => $data['first_id']
    ];
}

// Dapatkan ID pertama untuk AirTag
$airtag_first_id_query = "SELECT MIN(id) as first_id FROM admin_produk_airtag";
$airtag_first_id_result = mysqli_query($db, $airtag_first_id_query);
$airtag_first_id_data = mysqli_fetch_assoc($airtag_first_id_result);
$airtag_first_id = $airtag_first_id_data['first_id'];

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
    <title>Admin Panel - Kelola Trade In</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f5f7fb;
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
            text-decoration: none;
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

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eaeaea;
        }

        .page-header h1 {
            font-size: 28px;
            font-weight: 600;
            color: #333;
        }

        .btn-add {
            background: linear-gradient(135deg, #4a6cf7 0%, #6a11cb 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(74, 108, 247, 0.3);
            color: white;
        }

        /* Card Styles */
        .card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid #eaeaea;
            padding: 20px;
            border-radius: 15px 15px 0 0;
        }

        .card-header h3 {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
            color: #333;
        }

        .card-body {
            padding: 20px;
        }

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
            vertical-align: top;
        }

        .product-info {
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .thumbnail-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #4a6cf7;
        }

        .product-details {
            flex: 1;
        }

        .product-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .product-desc {
            font-size: 13px;
            color: #666;
            margin-bottom: 8px;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-stats {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .stat-badge {
            background: #f8f9fa;
            border: 1px solid #eaeaea;
            border-radius: 15px;
            padding: 3px 10px;
            font-size: 12px;
            color: #666;
        }

        .stat-badge i {
            margin-right: 4px;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-edit, .btn-delete, .btn-view {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-edit {
            background-color: #e3f2fd;
            color: #1976d2;
            border: 1px solid #bbdefb;
        }

        .btn-edit:hover {
            background-color: #bbdefb;
        }

        .btn-view {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .btn-view:hover {
            background-color: #ffeaa7;
        }

        .btn-delete {
            background-color: #ffebee;
            color: #d32f2f;
            border: 1px solid #ffcdd2;
        }

        .btn-delete:hover {
            background-color: #ffcdd2;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #777;
        }

        .no-data i {
            font-size: 50px;
            margin-bottom: 15px;
            color: #ddd;
        }

        .price-range {
            font-weight: 600;
            color: #4a6cf7;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }

        .status-tersedia {
            background-color: #d4edda;
            color: #155724;
        }

        .status-habis {
            background-color: #f8d7da;
            color: #721c24;
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

            .action-buttons {
                flex-direction: column;
            }
            
            .product-info {
                flex-direction: column;
            }
            
            .product-stats {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
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
                            <a href="../../products-panel/ipad/ipad.php<?php echo $category_data['ipad']['first_id'] ? '?id=' . $category_data['ipad']['first_id'] : ''; ?>">
                                <i class="fas fa-tablet-alt"></i>
                                <span>iPad</span>
                                <span class="badge"><?php echo $category_data['ipad']['count']; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../products-panel/iphone/iphone.php<?php echo $category_data['iphone']['first_id'] ? '?id=' . $category_data['iphone']['first_id'] : ''; ?>">
                                <i class="fas fa-mobile-alt"></i>
                                <span>iPhone</span>
                                <span class="badge"><?php echo $category_data['iphone']['count']; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../products-panel/mac/mac.php<?php echo $category_data['mac']['first_id'] ? '?id=' . $category_data['mac']['first_id'] : ''; ?>">
                                <i class="fas fa-laptop"></i>
                                <span>Mac</span>
                                <span class="badge"><?php echo $category_data['mac']['count']; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../products-panel/music/music.php<?php echo $category_data['music']['first_id'] ? '?id=' . $category_data['music']['first_id'] : ''; ?>">
                                <i class="fas fa-headphones-alt"></i>
                                <span>Music</span>
                                <span class="badge"><?php echo $category_data['music']['count']; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../products-panel/watch/watch.php<?php echo $category_data['watch']['first_id'] ? '?id=' . $category_data['watch']['first_id'] : ''; ?>">
                                <i class="fas fa-clock"></i>
                                <span>Watch</span>
                                <span class="badge"><?php echo $category_data['watch']['count']; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../products-panel/aksesoris/aksesoris.php<?php echo $category_data['aksesoris']['first_id'] ? '?id=' . $category_data['aksesoris']['first_id'] : ''; ?>">
                                <i class="fas fa-toolbox"></i>
                                <span>Aksesoris</span>
                                <span class="badge"><?php echo $category_data['aksesoris']['count']; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../products-panel/airtag/airtag.php<?php echo $airtag_first_id ? '?id=' . $airtag_first_id : ''; ?>">
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
                        <li class="active">
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
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin_username); ?>&background=4a6cf7&color=fff" alt="Admin">
                    <div class="user-info">
                        <h4><?php echo htmlspecialchars($admin_username); ?></h4>
                        <p>Admin iBox</p>
                    </div>
                    <a href="../../auth/logout.php" class="logout-btn" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <h1><i class="fas fa-tag me-2"></i> Kelola Produk AirTag</h1>
                <a href="add-airtag.php" class="btn-add">
                    <i class="fas fa-plus"></i> Tambah Produk AirTag
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-list me-2"></i> Daftar Produk AirTag</h3>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <?php if(mysqli_num_rows($result) > 0): ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Produk</th>
                                        <th>Statistik</th>
                                        <th>Harga Range</th>
                                        <th>Total Stok</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($product = mysqli_fetch_assoc($result)): 
                                        // Ambil thumbnail pertama untuk produk
                                        $query_thumbnail = "SELECT foto_thumbnail FROM admin_produk_airtag_gambar WHERE produk_id = '{$product['id']}' LIMIT 1";
                                        $result_thumbnail = mysqli_query($db, $query_thumbnail);
                                        $thumbnail = mysqli_fetch_assoc($result_thumbnail);
                                        
                                        // Ambil semua warna untuk produk ini
                                        $query_warna = "SELECT DISTINCT warna FROM admin_produk_airtag_kombinasi WHERE produk_id = '{$product['id']}'";
                                        $result_warna = mysqli_query($db, $query_warna);
                                        $warna_list = mysqli_fetch_all($result_warna, MYSQLI_ASSOC);
                                        
                                        // Check if product has stock
                                        $has_stock = $product['total_stok'] > 0;
                                    ?>
                                    <tr>
                                        <td><strong>#<?php echo $product['id']; ?></strong></td>
                                        <td>
                                            <div class="product-info">
                                                <?php if(!empty($thumbnail['foto_thumbnail'])): ?>
                                                    <img src="../../uploads/<?php echo htmlspecialchars($thumbnail['foto_thumbnail']); ?>" 
                                                         alt="Thumbnail" class="thumbnail-img">
                                                <?php else: ?>
                                                    <div class="thumbnail-img" style="background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-image" style="color: #ccc; font-size: 24px;"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="product-details">
                                                    <div class="product-title">
                                                        <?php echo htmlspecialchars($product['nama_produk']); ?>
                                                    </div>
                                                    <div class="product-desc">
                                                        <?php echo htmlspecialchars(substr($product['deskripsi_produk'] ?? '', 0, 100)) . '...'; ?>
                                                    </div>
                                                    <div class="product-stats">
                                                        <span class="stat-badge">
                                                            <i class="fas fa-palette"></i>
                                                            <?php echo $product['total_warna']; ?> Warna
                                                        </span>
                                                        <span class="stat-badge">
                                                            <i class="fas fa-layer-group"></i>
                                                            <?php echo $product['total_kombinasi']; ?> Kombinasi
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="product-stats">
                                                <span class="stat-badge">
                                                    <i class="fas fa-boxes"></i>
                                                    <?php echo $product['total_kombinasi']; ?> Kombinasi
                                                </span>
                                                <span class="status-badge status-<?php echo $has_stock ? 'tersedia' : 'habis'; ?>">
                                                    <?php echo $has_stock ? 'Tersedia' : 'Habis'; ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if($product['harga_terendah']): ?>
                                                <div class="price-range">
                                                    Rp <?php echo number_format($product['harga_terendah'], 0, ',', '.'); ?>
                                                    <?php if($product['harga_tertinggi'] > $product['harga_terendah']): ?>
                                                        - Rp <?php echo number_format($product['harga_tertinggi'], 0, ',', '.'); ?>
                                                    <?php endif; ?>
                                                </div>
                                                <small class="text-muted">
                                                    Mulai dari
                                                </small>
                                            <?php else: ?>
                                                <span class="text-muted">Belum ada harga</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="fw-bold <?php echo $has_stock ? 'text-success' : 'text-danger'; ?>">
                                                <?php echo number_format($product['total_stok'], 0, ',', '.'); ?> unit
                                            </div>
                                            <small class="text-muted">
                                                Stok total semua kombinasi
                                            </small>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="view-airtag.php?id=<?php echo $product['id']; ?>" class="btn-view">
                                                    <i class="fas fa-eye"></i> Lihat
                                                </a>
                                                <a href="edit-airtag.php?id=<?php echo $product['id']; ?>" class="btn-edit">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <a href="delete-airtag.php?id=<?php echo $product['id']; ?>" class="btn-delete" 
                                                   onclick="return confirm('Yakin ingin menghapus produk ini? Semua kombinasi dan gambar akan terhapus.')">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="no-data">
                                <i class="fas fa-tag" style="font-size: 50px; color: #ddd; margin-bottom: 15px;"></i>
                                <h4>Belum ada produk AirTag</h4>
                                <p>Mulai dengan menambahkan produk AirTag pertama Anda</p>
                                <a href="add-airtag.php" class="btn-add mt-3" style="display: inline-flex;">
                                    <i class="fas fa-plus"></i> Tambah Produk Pertama
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Session timeout warning 
        setTimeout(function() {
            alert('Session akan segera berakhir. Silakan login kembali.');
        }, 25 * 60 * 1000);

        // Auto logout 
        setTimeout(function() {
            window.location.href = '../../auth/logout.php';
        }, 30 * 60 * 1000);
    </script>
</body>
</html>