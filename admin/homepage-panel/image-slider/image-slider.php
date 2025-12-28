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
    
    // Ambil data slider untuk mendapatkan nama file gambar
    $query = "SELECT * FROM admin_homepage_slider WHERE id = $id";
    $result = mysqli_query($db, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $slider = mysqli_fetch_assoc($result);
        $image_file = $slider['gambar'];
        
        // Hapus file gambar jika ada
        if ($image_file) {
            $upload_dir = '../../../uploads/slider/';
            $image_path = $upload_dir . $image_file;
            
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        // Hapus dari database
        $delete_query = "DELETE FROM admin_homepage_slider WHERE id = $id";
        if (mysqli_query($db, $delete_query)) {
            $_SESSION['success_message'] = "Slider berhasil dihapus!";
        } else {
            $_SESSION['error_message'] = "Gagal menghapus slider: " . mysqli_error($db);
        }
    }
    
    header("Location: image-slider.php");
    exit();
}

// Handle status toggle
if (isset($_GET['action']) && $_GET['action'] == 'toggle_status' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Get current status
    $query = "SELECT status FROM admin_homepage_slider WHERE id = $id";
    $result = mysqli_query($db, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $slider = mysqli_fetch_assoc($result);
        $new_status = $slider['status'] == 'active' ? 'inactive' : 'active';
        
        $update_query = "UPDATE admin_homepage_slider SET status = '$new_status', updated_at = NOW() WHERE id = $id";
        if (mysqli_query($db, $update_query)) {
            $_SESSION['success_message'] = "Status slider berhasil diubah!";
        } else {
            $_SESSION['error_message'] = "Gagal mengubah status: " . mysqli_error($db);
        }
    }
    
    header("Location: image-slider.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Image Slider - Admin iBox</title>
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

        .btn-info {
            background-color: #17a2b8;
            color: white;
        }

        .btn-info:hover {
            background-color: #138496;
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

        .stat-icon.active {
            background-color: rgba(76, 175, 80, 0.1);
            color: #4caf50;
        }

        .stat-icon.inactive {
            background-color: rgba(244, 67, 54, 0.1);
            color: #f44336;
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
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: #f8f9fa;
        }

        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #2c3e50;
            border-bottom: 2px solid #e0e0e0;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        /* Image Preview */
        .slider-image {
            width: 120px;
            height: 60px;
            border-radius: 6px;
            object-fit: cover;
            border: 1px solid #e0e0e0;
            transition: transform 0.3s;
        }

        .slider-image:hover {
            transform: scale(1.5);
            z-index: 10;
            position: relative;
        }

        /* Status Badge */
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }

        .status-active {
            background-color: rgba(76, 175, 80, 0.1);
            color: #4caf50;
        }

        .status-inactive {
            background-color: rgba(244, 67, 54, 0.1);
            color: #f44336;
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

        .btn-toggle {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }

        .btn-toggle:hover {
            background-color: #ffc107;
            color: #212529;
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
                            <a href="../../../index.php">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../../products-panel/categories/kategori.php">
                                <i class="fas fa-tags"></i>
                                <span>Kategori</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../../products-panel/ipad/ipad.php">
                                <i class="fas fa-tablet-alt"></i>
                                <span>iPad</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../../products-panel/iphone/iphone.php">
                                <i class="fas fa-mobile-alt"></i>
                                <span>iPhone</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../../products-panel/mac/mac.php">
                                <i class="fas fa-laptop"></i>
                                <span>Mac</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../../products-panel/music/music.php">
                                <i class="fas fa-headphones-alt"></i>
                                <span>Music</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../../products-panel/watch/watch.php">
                                <i class="fas fa-clock"></i>
                                <span>Watch</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../../products-panel/aksesoris/aksesoris.php">
                                <i class="fas fa-toolbox"></i>
                                <span>Aksesoris</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../../products-panel/airtag/airtag.php">
                                <i class="fas fa-tag"></i>
                                <span>AirTag</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="menu-section">
                    <h3 class="section-title">Homepage Panel</h3>
                    <ul>
                        <li class="active">
                            <a href="#">
                                <i class="fas fa-images"></i>
                                <span>Image slider</span>
                            </a>
                        </li>
                        <li>
                            <a href="../produk-populer/produk-populer.php">
                                <i class="fas fa-fire"></i>
                                <span>Produk Populer</span>
                            </a>
                        </li>
                        <li>
                            <a href="../produk-terbaru/produk-terbaru.php">
                                <i class="fas fa-bolt"></i>
                                <span>Produk Terbaru</span>
                            </a>
                        </li>
                        <li>
                            <a href="../image-grid/image-grid.php">
                                <i class="fas fa-th"></i>
                                <span>Image grid</span>
                            </a>
                        </li>
                        <li>
                            <a href="../trade-in/trade-in.php">
                                <i class="fas fa-exchange-alt"></i>
                                <span>Trade in</span>
                            </a>
                        </li>
                        <li>
                            <a href="../aksesori-unggulan/aksesori-unggulan.php">
                                <i class="fas fa-gem"></i>
                                <span>Aksesori Unggulan</span>
                            </a>
                        </li>
                        <li>
                            <a href="../chekout-sekarang/chekout-sekarang.php">
                                <i class="fas fa-shopping-bag"></i>
                                <span>Checkout Sekarang</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="menu-section">
                    <h3 class="section-title">Lainnya</h3>
                    <ul>
                        <li>
                            <a href="../../../other/users/users.php">
                                <i class="fas fa-users"></i>
                                <span>Pengguna</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../../other/orders/order.php">
                                <i class="fas fa-shopping-cart"></i>
                                <span>Pesanan</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../../other/settings/settings.php">
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
                    <h1><i class="fas fa-images"></i> Kelola Image Slider</h1>
                    <p>Kelola slider yang ditampilkan di halaman utama</p>
                </div>
                <div class="action-buttons">
                    <a href="image-slider.php" class="btn btn-secondary">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </a>
                    <a href="add-image-slider.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Slider
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
                // Hitung statistik slider
                $total_query = "SELECT COUNT(*) as total FROM admin_homepage_slider";
                $active_query = "SELECT COUNT(*) as active FROM admin_homepage_slider WHERE status = 'active'";
                $inactive_query = "SELECT COUNT(*) as inactive FROM admin_homepage_slider WHERE status = 'inactive'";
                
                $total_result = mysqli_query($db, $total_query);
                $active_result = mysqli_query($db, $active_query);
                $inactive_result = mysqli_query($db, $inactive_query);
                
                $total = mysqli_fetch_assoc($total_result)['total'];
                $active = mysqli_fetch_assoc($active_result)['active'];
                $inactive = mysqli_fetch_assoc($inactive_result)['inactive'];
                ?>
                <div class="stat-card">
                    <div class="stat-icon total">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $total; ?></h3>
                        <p>Total Slider</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon active">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $active; ?></h3>
                        <p>Slider Aktif</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon inactive">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $inactive; ?></h3>
                        <p>Slider Nonaktif</p>
                    </div>
                </div>
            </div>

            <!-- Table Container -->
            <div class="table-container">
                <?php
                $query = "SELECT * FROM admin_homepage_slider ORDER BY urutan ASC, created_at DESC";
                $result = mysqli_query($db, $query);
                
                if (mysqli_num_rows($result) > 0) {
                ?>
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Gambar</th>
                                <th>Judul</th>
                                <th>Deskripsi</th>
                                <th>Link</th>
                                <th>Urutan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($result)) {
                                $image_path = '../../../uploads/slider/' . $row['gambar'];
                                $image_exists = file_exists($image_path) ? $image_path : '../../../uploads/default-slider.jpg';
                            ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td>
                                        <img src="<?php echo $image_exists; ?>" 
                                             class="slider-image" 
                                             alt="<?php echo htmlspecialchars($row['judul']); ?>"
                                             title="Klik untuk memperbesar">
                                    </td>
                                    <td><?php echo htmlspecialchars($row['judul']); ?></td>
                                    <td>
                                        <?php 
                                        if (!empty($row['deskripsi'])) {
                                            echo strlen($row['deskripsi']) > 50 
                                                ? substr(htmlspecialchars($row['deskripsi']), 0, 50) . '...' 
                                                : htmlspecialchars($row['deskripsi']);
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($row['link'])): ?>
                                            <a href="<?php echo htmlspecialchars($row['link']); ?>" 
                                               target="_blank" 
                                               style="color: #4a6cf7; text-decoration: none;">
                                                <i class="fas fa-external-link-alt"></i> Link
                                            </a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $row['urutan']; ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $row['status']; ?>">
                                            <?php echo $row['status'] == 'active' ? 'Aktif' : 'Nonaktif'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons-cell">
                                            <a href="view-image-slider.php?id=<?php echo $row['id']; ?>" 
                                               class="btn-icon btn-view" 
                                               title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="edit-image-slider.php?id=<?php echo $row['id']; ?>" 
                                               class="btn-icon btn-edit" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="image-slider.php?action=toggle_status&id=<?php echo $row['id']; ?>" 
                                               class="btn-icon btn-toggle" 
                                               title="<?php echo $row['status'] == 'active' ? 'Nonaktifkan' : 'Aktifkan'; ?>"
                                               onclick="return confirm('Yakin ingin mengubah status slider ini?')">
                                                <i class="fas fa-power-off"></i>
                                            </a>
                                            <a href="image-slider.php?action=delete&id=<?php echo $row['id']; ?>" 
                                               class="btn-icon btn-delete" 
                                               title="Hapus"
                                               onclick="return confirm('Yakin ingin menghapus slider ini? Tindakan ini tidak dapat dibatalkan.')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-images"></i>
                        </div>
                        <h3>Belum ada slider</h3>
                        <p>Tambahkan slider pertama untuk ditampilkan di halaman utama</p>
                        <a href="add-image-slider.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Tambah Slider Pertama
                        </a>
                    </div>
                <?php } ?>
            </div>
        </main>
    </div>

    <script>
        // Confirm before delete
        function confirmDelete(event) {
            if (!confirm('Yakin ingin menghapus slider ini? Tindakan ini tidak dapat dibatalkan.')) {
                event.preventDefault();
            }
        }

        // Confirm before toggle status
        function confirmToggle(event) {
            if (!confirm('Yakin ingin mengubah status slider ini?')) {
                event.preventDefault();
            }
        }

        // Image zoom on hover
        document.addEventListener('DOMContentLoaded', function() {
            const images = document.querySelectorAll('.slider-image');
            images.forEach(img => {
                img.addEventListener('mouseenter', function() {
                    this.style.zIndex = '100';
                    this.style.boxShadow = '0 10px 20px rgba(0,0,0,0.3)';
                });
                
                img.addEventListener('mouseleave', function() {
                    this.style.zIndex = '';
                    this.style.boxShadow = '';
                });
            });
        });
    </script>
</body>
</html>