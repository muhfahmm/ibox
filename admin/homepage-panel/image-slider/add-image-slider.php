<?php
session_start();
require_once '../../db.php';

// Redirect jika belum login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../../../auth/login.php");
    exit();
}

// Handle form submission
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi input
    if (empty($_FILES['gambar']['name'])) {
        $error = 'Gambar slider harus diupload';
    } else {
        // Konfigurasi upload
        $upload_dir = '../../../uploads/slider/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB

        // Validasi file
        $file = $_FILES['gambar'];
        if (!in_array($file['type'], $allowed_types)) {
            $error = 'Format file tidak didukung. Gunakan JPG, PNG, GIF, atau WebP';
        } elseif ($file['size'] > $max_size) {
            $error = 'Ukuran file terlalu besar. Maksimal 5MB';
        } else {
            // Generate nama file unik
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '_' . time() . '.' . $extension;

            // Upload file
            if (move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
                // Prepare data untuk database
                $judul = mysqli_real_escape_string($db, $_POST['judul'] ?? '');
                $deskripsi = mysqli_real_escape_string($db, $_POST['deskripsi'] ?? '');
                $link = mysqli_real_escape_string($db, $_POST['link'] ?? '');
                $urutan = intval($_POST['urutan'] ?? 1);
                $status = mysqli_real_escape_string($db, $_POST['status'] ?? 'active');

                // Insert ke database
                $query = "INSERT INTO admin_homepage_slider 
                          (judul, deskripsi, gambar, link, urutan, status, created_at, updated_at) 
                          VALUES ('$judul', '$deskripsi', '$filename', '$link', $urutan, '$status', NOW(), NOW())";

                if (mysqli_query($db, $query)) {
                    $success = 'Slider berhasil ditambahkan!';
                    $_SESSION['success_message'] = $success;
                    header("Location: image-slider.php");
                    exit();
                } else {
                    // Hapus file yang sudah diupload
                    unlink($upload_dir . $filename);
                    $error = 'Gagal menyimpan data ke database: ' . mysqli_error($db);
                }
            } else {
                $error = 'Gagal mengupload gambar';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Slider Baru - Admin iBox</title>
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

        /* Form Container */
        .form-container {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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

        /* Form Styles */
        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #2c3e50;
        }

        .form-group label.required::after {
            content: " *";
            color: #dc3545;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: #4a6cf7;
            outline: none;
            box-shadow: 0 0 0 3px rgba(74, 108, 247, 0.1);
        }

        .form-textarea {
            min-height: 120px;
            resize: vertical;
        }

        /* Image Upload */
        .image-upload-container {
            border: 2px dashed #ddd;
            border-radius: 6px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.3s;
            background-color: #fafafa;
        }

        .image-upload-container:hover {
            border-color: #4a6cf7;
            background-color: #f0f4ff;
        }

        .image-upload-icon {
            font-size: 40px;
            color: #7f8c8d;
            margin-bottom: 10px;
        }

        .image-upload-text {
            color: #7f8c8d;
            margin-bottom: 10px;
        }

        .image-preview {
            margin-top: 15px;
            text-align: center;
        }

        .preview-image {
            max-width: 300px;
            max-height: 200px;
            border-radius: 6px;
            border: 1px solid #ddd;
        }

        /* Form Actions */
        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eaeaea;
        }

        .btn {
            padding: 12px 25px;
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

            .form-container {
                padding: 20px;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .form-actions {
                flex-direction: column;
                gap: 15px;
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
                    <h1><i class="fas fa-plus"></i> Tambah Slider Baru</h1>
                    <p>Tambahkan slider baru untuk ditampilkan di halaman utama</p>
                </div>
                <div class="action-buttons">
                    <a href="image-slider.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>

            <!-- Alert Messages -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <!-- Form Container -->
            <div class="form-container">
                <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                    <div class="form-group">
                        <label for="judul" class="required">Judul Slider</label>
                        <input type="text" id="judul" name="judul" class="form-control" 
                               placeholder="Masukkan judul slider" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi</label>
                        <textarea id="deskripsi" name="deskripsi" class="form-control form-textarea" 
                                  placeholder="Masukkan deskripsi slider"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="gambar" class="required">Gambar Slider</label>
                        <div class="image-upload-container" onclick="document.getElementById('gambar').click()">
                            <div class="image-upload-icon">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <div class="image-upload-text">
                                Klik untuk upload gambar
                            </div>
                            <small>Format: JPG, PNG, GIF, WebP | Maks: 5MB</small>
                            <input type="file" id="gambar" name="gambar" accept="image/*" 
                                   style="display: none;" onchange="previewImage(this)" required>
                        </div>
                        <div id="previewImage" class="image-preview"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="link">Link (URL)</label>
                        <input type="url" id="link" name="link" class="form-control" 
                               placeholder="https://example.com">
                    </div>
                    
                    <div class="form-group">
                        <label for="urutan">Urutan Tampilan</label>
                        <input type="number" id="urutan" name="urutan" class="form-control" 
                               min="1" value="1">
                        <small style="color: #666; font-size: 12px;">Semakin kecil angkanya, semakin awal ditampilkan</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="active" selected>Aktif</option>
                            <option value="inactive">Nonaktif</option>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <a href="image-slider.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Slider
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        // Image preview
        function previewImage(input) {
            const preview = document.getElementById('previewImage');
            preview.innerHTML = '';
            
            if (input.files && input.files[0]) {
                const file = input.files[0];
                
                // Validate file size (5MB max)
                if (file.size > 5 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar. Maksimal 5MB');
                    input.value = '';
                    return;
                }
                
                // Validate file type
                const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!validTypes.includes(file.type)) {
                    alert('Format file tidak didukung. Gunakan JPG, PNG, GIF, atau WebP');
                    input.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'preview-image';
                    preview.appendChild(img);
                }
                reader.readAsDataURL(file);
            }
        }

        // Form validation
        function validateForm() {
            const gambar = document.getElementById('gambar').files[0];
            if (!gambar) {
                alert('Silakan pilih gambar untuk slider');
                return false;
            }
            
            return true;
        }

        // Auto-resize textarea
        document.addEventListener('DOMContentLoaded', function() {
            const textarea = document.getElementById('deskripsi');
            if (textarea) {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = (this.scrollHeight) + 'px';
                });
            }
        });
    </script>
</body>
</html>