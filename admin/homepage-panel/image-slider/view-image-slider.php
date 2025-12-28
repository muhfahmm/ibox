<?php
session_start();
require_once '../../db.php';

// Redirect jika belum login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../../../auth/login.php");
    exit();
}

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "ID slider tidak valid";
    header("Location: image-slider.php");
    exit();
}

$id = intval($_GET['id']);

// Fetch slider data
$query = "SELECT * FROM admin_homepage_slider WHERE id = $id";
$result = mysqli_query($db, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    $_SESSION['error_message'] = "Slider tidak ditemukan";
    header("Location: image-slider.php");
    exit();
}

$slider = mysqli_fetch_assoc($result);
$image_path = '../../../uploads/slider/' . $slider['gambar'];
$image_exists = file_exists($image_path) ? $image_path : '../../../uploads/default-slider.jpg';

// Format dates
$created_at = date('d/m/Y H:i', strtotime($slider['created_at']));
$updated_at = date('d/m/Y H:i', strtotime($slider['updated_at']));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Slider - Admin iBox</title>
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

        /* Sidebar Styles (sama dengan sebelumnya) */
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

        /* Detail Container */
        .detail-container {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Image Section */
        .image-section {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eaeaea;
        }

        .slider-image-large {
            max-width: 100%;
            max-height: 400px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: transform 0.3s;
        }

        .slider-image-large:hover {
            transform: scale(1.02);
        }

        .image-info {
            margin-top: 10px;
            color: #666;
            font-size: 14px;
        }

        /* Detail Grid */
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .detail-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            border-left: 4px solid #4a6cf7;
        }

        .detail-card h3 {
            font-size: 16px;
            color: #2c3e50;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .detail-card p {
            color: #666;
            line-height: 1.6;
        }

        .detail-card a {
            color: #4a6cf7;
            text-decoration: none;
            word-break: break-all;
        }

        .detail-card a:hover {
            text-decoration: underline;
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

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
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

        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .btn-warning:hover {
            background-color: #e0a800;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
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

            .detail-container {
                padding: 20px;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar (sama dengan sebelumnya) -->
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
                        <li>
                            <a href="image-slider.php">
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
                    <h1><i class="fas fa-eye"></i> Detail Slider</h1>
                    <p>Informasi lengkap tentang slider "<?php echo htmlspecialchars($slider['judul']); ?>"</p>
                </div>
                <div class="action-buttons">
                    <a href="image-slider.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>

            <!-- Detail Container -->
            <div class="detail-container">
                <!-- Image Section -->
                <div class="image-section">
                    <img src="<?php echo $image_exists; ?>" 
                         class="slider-image-large" 
                         alt="<?php echo htmlspecialchars($slider['judul']); ?>"
                         onclick="window.open(this.src, '_blank')">
                    <div class="image-info">
                        <i class="fas fa-info-circle"></i> Klik gambar untuk melihat ukuran penuh
                    </div>
                </div>

                <!-- Detail Grid -->
                <div class="detail-grid">
                    <!-- Judul -->
                    <div class="detail-card">
                        <h3><i class="fas fa-heading"></i> Judul Slider</h3>
                        <p><?php echo htmlspecialchars($slider['judul']); ?></p>
                    </div>

                    <!-- Deskripsi -->
                    <div class="detail-card">
                        <h3><i class="fas fa-align-left"></i> Deskripsi</h3>
                        <p><?php echo !empty($slider['deskripsi']) ? nl2br(htmlspecialchars($slider['deskripsi'])) : '-'; ?></p>
                    </div>

                    <!-- Link -->
                    <div class="detail-card">
                        <h3><i class="fas fa-link"></i> Link (URL)</h3>
                        <p>
                            <?php if (!empty($slider['link'])): ?>
                                <a href="<?php echo htmlspecialchars($slider['link']); ?>" target="_blank">
                                    <?php echo htmlspecialchars($slider['link']); ?>
                                </a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </p>
                    </div>

                    <!-- Urutan -->
                    <div class="detail-card">
                        <h3><i class="fas fa-sort-numeric-down"></i> Urutan Tampilan</h3>
                        <p><?php echo $slider['urutan']; ?></p>
                    </div>

                    <!-- Status -->
                    <div class="detail-card">
                        <h3><i class="fas fa-power-off"></i> Status</h3>
                        <p>
                            <span class="status-badge status-<?php echo $slider['status']; ?>">
                                <?php echo $slider['status'] == 'active' ? 'Aktif' : 'Nonaktif'; ?>
                            </span>
                        </p>
                    </div>

                    <!-- File Name -->
                    <div class="detail-card">
                        <h3><i class="fas fa-file-image"></i> Nama File</h3>
                        <p><?php echo htmlspecialchars($slider['gambar']); ?></p>
                    </div>

                    <!-- Created At -->
                    <div class="detail-card">
                        <h3><i class="fas fa-calendar-plus"></i> Dibuat Pada</h3>
                        <p><?php echo $created_at; ?></p>
                    </div>

                    <!-- Updated At -->
                    <div class="detail-card">
                        <h3><i class="fas fa-calendar-check"></i> Diperbarui Pada</h3>
                        <p><?php echo $updated_at; ?></p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <a href="edit-image-slider.php?id=<?php echo $slider['id']; ?>" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Slider
                    </a>
                    <a href="image-slider.php?action=toggle_status&id=<?php echo $slider['id']; ?>" 
                       class="btn btn-warning"
                       onclick="return confirm('Yakin ingin mengubah status slider ini?')">
                        <i class="fas fa-power-off"></i> 
                        <?php echo $slider['status'] == 'active' ? 'Nonaktifkan' : 'Aktifkan'; ?>
                    </a>
                    <a href="image-slider.php?action=delete&id=<?php echo $slider['id']; ?>" 
                       class="btn btn-danger"
                       onclick="return confirm('Yakin ingin menghapus slider ini? Tindakan ini tidak dapat dibatalkan.')">
                        <i class="fas fa-trash"></i> Hapus Slider
                    </a>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Auto-open image in new tab on double click
        document.querySelector('.slider-image-large').addEventListener('dblclick', function() {
            window.open(this.src, '_blank');
        });

        // Print page function
        function printPage() {
            window.print();
        }
    </script>
</body>
</html>