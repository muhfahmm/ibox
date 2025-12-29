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

// Handle form submission
$success = false;
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_produk = mysqli_real_escape_string($db, $_POST['nama_produk']);
    $deskripsi_produk = mysqli_real_escape_string($db, $_POST['deskripsi_produk']);
    
    // Handle file upload
    $target_dir = "../../../uploads/slider/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $gambar_produk = '';
    if (isset($_FILES['gambar_produk']) && $_FILES['gambar_produk']['error'] === 0) {
        $file_name = basename($_FILES["gambar_produk"]["name"]);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = array('jpg', 'jpeg', 'png', 'gif', 'webp');
        
        if (in_array($file_ext, $allowed_ext)) {
            // Generate unique filename
            $new_filename = uniqid() . '_' . time() . '.' . $file_ext;
            $target_file = $target_dir . $new_filename;
            
            if (move_uploaded_file($_FILES["gambar_produk"]["tmp_name"], $target_file)) {
                $gambar_produk = $new_filename;
            } else {
                $error = "Gagal mengupload gambar.";
            }
        } else {
            $error = "Format file tidak didukung. Gunakan JPG, JPEG, PNG, GIF, atau WebP.";
        }
    }
    
    if (empty($error) && !empty($gambar_produk)) {
        $query = "INSERT INTO home_image_slider (gambar_produk, nama_produk, deskripsi_produk) 
                  VALUES ('$gambar_produk', '$nama_produk', '$deskripsi_produk')";
        
        if (mysqli_query($db, $query)) {
            $success = true;
            $_SESSION['success_message'] = "Slider berhasil ditambahkan!";
            header("Location: image-slider.php");
            exit();
        } else {
            $error = "Gagal menyimpan data: " . mysqli_error($db);
        }
    } elseif (empty($gambar_produk)) {
        $error = "Harap pilih gambar untuk slider.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Image Slider - Admin iBox</title>
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

        /* Form Styles */
        .form-container {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #2c3e50;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: border 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #4a6cf7;
            box-shadow: 0 0 0 2px rgba(74, 108, 247, 0.2);
        }

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }

        .file-upload {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .file-upload input[type="file"] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-upload-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            border: 2px dashed #ddd;
            border-radius: 6px;
            background-color: #f9f9f9;
            cursor: pointer;
            transition: all 0.3s;
        }

        .file-upload-label:hover {
            border-color: #4a6cf7;
            background-color: #f0f4ff;
        }

        .file-upload-label i {
            font-size: 40px;
            color: #4a6cf7;
            margin-bottom: 10px;
        }

        .file-upload-label span {
            color: #666;
            font-size: 14px;
        }

        .file-preview {
            margin-top: 15px;
            display: none;
        }

        .file-preview img {
            max-width: 200px;
            max-height: 150px;
            border-radius: 6px;
            border: 1px solid #ddd;
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
        }

        /* Alert Messages */
        .alert {
            padding: 15px;
            border-radius: 6px;
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
        <!-- Sidebar (sama dengan image-slider.php) -->
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
                    <h1><i class="fas fa-plus-circle"></i> Tambah Image Slider</h1>
                    <p>Tambahkan slider baru untuk ditampilkan di halaman utama</p>
                </div>
                <div class="action-buttons">
                    <a href="image-slider.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>

            <!-- Alert Messages -->
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- Form Container -->
            <div class="form-container">
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nama_produk">Nama Produk <span style="color: red;">*</span></label>
                        <input type="text" 
                               id="nama_produk" 
                               name="nama_produk" 
                               class="form-control" 
                               required
                               placeholder="Masukkan nama produk (contoh: iPhone 15 Pro Max)">
                    </div>

                    <div class="form-group">
                        <label for="deskripsi_produk">Deskripsi Produk</label>
                        <textarea id="deskripsi_produk" 
                                  name="deskripsi_produk" 
                                  class="form-control" 
                                  rows="4"
                                  placeholder="Masukkan deskripsi produk (opsional)"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="gambar_produk">Gambar Slider <span style="color: red;">*</span></label>
                        <div class="file-upload">
                            <input type="file" 
                                   id="gambar_produk" 
                                   name="gambar_produk" 
                                   accept="image/*" 
                                   required
                                   onchange="previewImage(event)">
                            <label for="gambar_produk" class="file-upload-label">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>Klik untuk memilih gambar atau drag & drop</span>
                                <small style="color: #999; margin-top: 5px;">Format: JPG, JPEG, PNG, GIF, WebP</small>
                            </label>
                        </div>
                        <div class="file-preview" id="filePreview">
                            <img id="previewImage" src="#" alt="Preview">
                        </div>
                    </div>

                    <div class="button-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Slider
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset Form
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        // Image preview function
        function previewImage(event) {
            const preview = document.getElementById('previewImage');
            const previewContainer = document.getElementById('filePreview');
            const file = event.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.style.display = 'block';
                }
                
                reader.readAsDataURL(file);
            } else {
                previewContainer.style.display = 'none';
                preview.src = '#';
            }
        }

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const fileInput = document.getElementById('gambar_produk');
            const nameInput = document.getElementById('nama_produk');
            
            if (!nameInput.value.trim()) {
                e.preventDefault();
                alert('Nama produk harus diisi!');
                nameInput.focus();
                return false;
            }
            
            if (!fileInput.files || fileInput.files.length === 0) {
                e.preventDefault();
                alert('Harap pilih gambar untuk slider!');
                return false;
            }
            
            // Check file size (max 5MB)
            const maxSize = 5 * 1024 * 1024; // 5MB in bytes
            if (fileInput.files[0].size > maxSize) {
                e.preventDefault();
                alert('Ukuran gambar terlalu besar! Maksimal 5MB.');
                return false;
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