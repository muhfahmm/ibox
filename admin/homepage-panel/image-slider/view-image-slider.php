<?php
session_start();
require_once '../../db.php';

// Redirect jika belum login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../../../auth/login.php");
    exit();
}

$admin_username = $_SESSION['admin_username'] ?? 'Admin';

// Hitung jumlah produk untuk sidebar
$iphone_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM admin_produk_iphone"))['total'];
$ipad_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM admin_produk_ipad"))['total'];
$mac_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM admin_produk_mac"))['total'];
$watch_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM admin_produk_watch"))['total'];
$music_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM admin_produk_music"))['total'];
$aksesoris_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM admin_produk_aksesoris"))['total'];
$airtag_count = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) as total FROM admin_produk_airtag"))['total'];

// Get slider data by ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$slider = null;

if ($id > 0) {
    $query = "SELECT * FROM home_image_slider WHERE id = $id";
    $result = mysqli_query($db, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $slider = mysqli_fetch_assoc($result);
    } else {
        header("Location: image-slider.php");
        exit();
    }
} else {
    header("Location: image-slider.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Image Slider - Admin iBox</title>
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
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        @media (max-width: 992px) {
            .detail-container {
                grid-template-columns: 1fr;
            }
        }

        /* Image Section */
        .image-section {
            text-align: center;
        }

        .slider-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #e0e0e0;
        }

        .image-info {
            margin-top: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
        }

        .image-info h4 {
            color: #495057;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .image-info p {
            color: #6c757d;
            font-size: 12px;
            word-break: break-all;
        }

        /* Info Section */
        .info-section {
            padding: 10px 0;
        }

        .info-group {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-group:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .info-label {
            display: block;
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .info-value {
            color: #212529;
            font-size: 16px;
            line-height: 1.5;
        }

        .info-value.desc {
            white-space: pre-line;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #4a6cf7;
        }

        /* Buttons */
        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
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

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
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

            .button-group {
                flex-direction: column;
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
                            <a href="../../../index.php">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="../../../products-panel/ipad/ipad.php">
                                <i class="fas fa-tablet-alt"></i>
                                <span>iPad</span>
                                <span class="badge"><?php echo $ipad_count; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../../products-panel/iphone/iphone.php">
                                <i class="fas fa-mobile-alt"></i>
                                <span>iPhone</span>
                                <span class="badge"><?php echo $iphone_count; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../../products-panel/mac/mac.php">
                                <i class="fas fa-laptop"></i>
                                <span>Mac</span>
                                <span class="badge"><?php echo $mac_count; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../../products-panel/music/music.php">
                                <i class="fas fa-headphones-alt"></i>
                                <span>Music</span>
                                <span class="badge"><?php echo $music_count; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../../products-panel/watch/watch.php">
                                <i class="fas fa-clock"></i>
                                <span>Watch</span>
                                <span class="badge"><?php echo $watch_count; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../../products-panel/aksesoris/aksesoris.php">
                                <i class="fas fa-toolbox"></i>
                                <span>Aksesoris</span>
                                <span class="badge"><?php echo $aksesoris_count; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="../../../products-panel/airtag/airtag.php">
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
                            <a href="../image-slider.php">
                                <i class="fas fa-images"></i>
                                <span>Image slider</span>
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
                                <span class="badge badge-warning">5</span>
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
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin_username); ?>&background=4a6cf7&color=fff" alt="Admin">
                    <div class="user-info">
                        <h4><?php echo htmlspecialchars($admin_username); ?></h4>
                        <p>Admin iBox</p>
                    </div>
                    <a href="../../../auth/logout.php" class="logout-btn" title="Logout">
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
                    <h1><i class="fas fa-eye"></i> Detail Image Slider</h1>
                    <p>Lihat detail slider yang ditampilkan di halaman utama</p>
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
                    <?php if ($slider['gambar_produk']): ?>
                        <?php 
                        $image_path = '../../../uploads/slider/' . $slider['gambar_produk'];
                        $image_exists = file_exists($image_path);
                        ?>
                        <img src="<?php echo $image_exists ? $image_path : '../../../uploads/default-slider.jpg'; ?>" 
                             class="slider-image" 
                             alt="<?php echo htmlspecialchars($slider['nama_produk']); ?>">
                        
                        <div class="image-info">
                            <h4>Informasi Gambar:</h4>
                            <p>Nama file: <?php echo htmlspecialchars($slider['gambar_produk']); ?></p>
                            <?php if ($image_exists): ?>
                                <?php
                                $image_size = filesize($image_path);
                                $image_dimensions = getimagesize($image_path);
                                ?>
                                <p>Ukuran: <?php echo round($image_size / 1024, 2); ?> KB</p>
                                <?php if ($image_dimensions): ?>
                                    <p>Dimensi: <?php echo $image_dimensions[0]; ?> x <?php echo $image_dimensions[1]; ?> px</p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-image"></i>
                            </div>
                            <h3>Tidak ada gambar</h3>
                            <p>Slider ini belum memiliki gambar</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Info Section -->
                <div class="info-section">
                    <div class="info-group">
                        <span class="info-label">ID Slider</span>
                        <div class="info-value">#<?php echo $slider['id']; ?></div>
                    </div>

                    <div class="info-group">
                        <span class="info-label">Nama Produk</span>
                        <div class="info-value"><?php echo htmlspecialchars($slider['nama_produk']); ?></div>
                    </div>

                    <div class="info-group">
                        <span class="info-label">Deskripsi Produk</span>
                        <div class="info-value desc">
                            <?php 
                            if (!empty($slider['deskripsi_produk'])) {
                                echo nl2br(htmlspecialchars($slider['deskripsi_produk']));
                            } else {
                                echo '<span style="color: #999; font-style: italic;">Tidak ada deskripsi</span>';
                            }
                            ?>
                        </div>
                    </div>

                    <div class="info-group">
                        <span class="info-label">Tanggal Ditambahkan</span>
                        <div class="info-value">
                            <?php
                            // Check if there's a created_at column, if not show current date
                            echo date('d F Y H:i:s');
                            ?>
                        </div>
                    </div>

                    <div class="button-group">
                        <a href="edit-image-slider.php?id=<?php echo $slider['id']; ?>" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Slider
                        </a>
                        <a href="image-slider.php?action=delete&id=<?php echo $slider['id']; ?>" 
                           class="btn btn-secondary"
                           onclick="return confirm('Yakin ingin menghapus slider ini?')"
                           style="background-color: #dc3545; color: white;">
                            <i class="fas fa-trash"></i> Hapus Slider
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Image zoom functionality
        document.addEventListener('DOMContentLoaded', function() {
            const image = document.querySelector('.slider-image');
            if (image) {
                image.addEventListener('click', function() {
                    const overlay = document.createElement('div');
                    overlay.style.position = 'fixed';
                    overlay.style.top = '0';
                    overlay.style.left = '0';
                    overlay.style.width = '100%';
                    overlay.style.height = '100%';
                    overlay.style.backgroundColor = 'rgba(0,0,0,0.8)';
                    overlay.style.display = 'flex';
                    overlay.style.alignItems = 'center';
                    overlay.style.justifyContent = 'center';
                    overlay.style.zIndex = '9999';
                    overlay.style.cursor = 'zoom-out';
                    
                    const zoomedImage = document.createElement('img');
                    zoomedImage.src = this.src;
                    zoomedImage.style.maxWidth = '90%';
                    zoomedImage.style.maxHeight = '90%';
                    zoomedImage.style.objectFit = 'contain';
                    zoomedImage.style.borderRadius = '8px';
                    
                    overlay.appendChild(zoomedImage);
                    document.body.appendChild(overlay);
                    
                    overlay.addEventListener('click', function() {
                        document.body.removeChild(overlay);
                    });
                });
            }
        });

        // Session timeout warning
        setTimeout(function() {
            alert('Session akan segera berakhir. Silakan login kembali.');
        }, 25 * 60 * 1000);

        // Auto logout after 30 minutes
        setTimeout(function() {
            window.location.href = '../../../auth/logout.php';
        }, 30 * 60 * 1000);
    </script>
</body>
</html>