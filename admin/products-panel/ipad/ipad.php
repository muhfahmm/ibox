<?php
session_start();
require_once '../../db.php';

// Jika belum login, redirect ke login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../../auth/login.php?error=not_logged_in');
    exit();
}

$admin_username = $_SESSION['admin_username'] ?? 'Admin';

// Ambil data produk iPad dengan kombinasi
$query = "SELECT p.*, 
                 COUNT(DISTINCT k.id) as total_kombinasi,
                 COUNT(DISTINCT g.id) as total_warna,
                 MIN(k.harga) as harga_terendah,
                 MAX(k.harga) as harga_tertinggi,
                 SUM(k.jumlah_stok) as total_stok
          FROM admin_produk_ipad p
          LEFT JOIN admin_produk_ipad_kombinasi k ON p.id = k.produk_id
          LEFT JOIN admin_produk_ipad_gambar g ON p.id = g.produk_id
          GROUP BY p.id
          ORDER BY p.id DESC";
$result = mysqli_query($db, $query);

// Hitung jumlah produk iPad
$ipad_count = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Kelola iPad</title>
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
            width: 280px;
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
                            <a href="../../products-panel/categories/kategori.php">
                                <i class="fas fa-tags"></i>
                                <span>Kategori</span>
                            </a>
                        </li>
                        <li class="active">
                            <a href="ipad.php">
                                <i class="fas fa-tablet-alt"></i>
                                <span>iPad</span>
                                <span class="badge"><?php echo $ipad_count; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../products-panel/iphone/iphone.php">
                                <i class="fas fa-mobile-alt"></i>
                                <span>iPhone</span>
                                <span class="badge">24</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../products-panel/mac/mac.php">
                                <i class="fas fa-laptop"></i>
                                <span>Mac</span>
                                <span class="badge">12</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../products-panel/music/music.php">
                                <i class="fas fa-headphones-alt"></i>
                                <span>Music</span>
                                <span class="badge">10</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../products-panel/watch/watch.php">
                                <i class="fas fa-clock"></i>
                                <span>Watch</span>
                                <span class="badge">15</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../products-panel/aksesoris/aksesoris.php">
                                <i class="fas fa-toolbox"></i>
                                <span>Aksesoris</span>
                                <span class="badge">15</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../products-panel/airtag/airtag.php">
                                <i class="fas fa-tag"></i>
                                <span>AirTag</span>
                                <span class="badge">15</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Other menu sections... -->
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
                <h1><i class="fas fa-tablet-alt me-2"></i> Kelola Produk iPad</h1>
                <a href="add-ipad.php" class="btn-add">
                    <i class="fas fa-plus"></i> Tambah Produk iPad
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-list me-2"></i> Daftar Produk iPad</h3>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <?php if(mysqli_num_rows($result) > 0): ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Produk</th>
                                        <th>Statistik</th>
                                        <th>Harga Range</th>
                                        <th>Total Stok</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; ?>
                                    <?php while($product = mysqli_fetch_assoc($result)): 
                                        // Ambil thumbnail pertama untuk produk
                                        $query_thumbnail = "SELECT foto_thumbnail FROM admin_produk_ipad_gambar WHERE produk_id = '{$product['id']}' LIMIT 1";
                                        $result_thumbnail = mysqli_query($db, $query_thumbnail);
                                        $thumbnail = mysqli_fetch_assoc($result_thumbnail);
                                        
                                        // Ambil semua warna untuk produk ini
                                        $query_warna = "SELECT DISTINCT warna FROM admin_produk_ipad_kombinasi WHERE produk_id = '{$product['id']}'";
                                        $result_warna = mysqli_query($db, $query_warna);
                                        $warna_list = mysqli_fetch_all($result_warna, MYSQLI_ASSOC);
                                        
                                        // Check if product has stock
                                        $has_stock = $product['total_stok'] > 0;
                                    ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
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
                                                        <?php if(count($warna_list) > 0): ?>
                                                        <span class="stat-badge">
                                                            <i class="fas fa-tags"></i>
                                                            <?php 
                                                            $warna_names = array_column($warna_list, 'warna');
                                                            echo implode(', ', array_slice($warna_names, 0, 2));
                                                            if(count($warna_names) > 2) echo '...';
                                                            ?>
                                                        </span>
                                                        <?php endif; ?>
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
                                                <span class="stat-badge">
                                                    <i class="fas fa-palette"></i>
                                                    <?php echo $product['total_warna']; ?> Warna
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
                                                <a href="view-ipad.php?id=<?php echo $product['id']; ?>" class="btn-view">
                                                    <i class="fas fa-eye"></i> Lihat
                                                </a>
                                                <a href="edit-ipad.php?id=<?php echo $product['id']; ?>" class="btn-edit">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <a href="delete-ipad.php?id=<?php echo $product['id']; ?>" class="btn-delete" 
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
                                <i class="fas fa-box-open"></i>
                                <h4>Belum ada produk iPad</h4>
                                <p>Mulai dengan menambahkan produk iPad pertama Anda</p>
                                <a href="add-ipad.php" class="btn-add mt-3" style="display: inline-flex;">
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
        // Session timeout warning (30 minutes)
        setTimeout(function() {
            alert('Session akan segera berakhir. Silakan login kembali.');
        }, 25 * 60 * 1000);

        // Auto logout after 30 minutes
        setTimeout(function() {
            window.location.href = '../../auth/logout.php';
        }, 30 * 60 * 1000);
    </script>
</body>
</html>