<?php
session_start();
require_once '../../db.php';

// Jika belum login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../../auth/login.php?error=not_logged_in');
    exit();
}

$admin_username = $_SESSION['admin_username'] ?? 'Admin';

// Get product ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: music.php?error=invalid_id');
    exit();
}

$product_id = mysqli_real_escape_string($db, $_GET['id']);

// Fetch main product data
$query_product = "SELECT * FROM admin_produk_music WHERE id = '$product_id'";
$result_product = mysqli_query($db, $query_product);

if (mysqli_num_rows($result_product) === 0) {
    header('Location: music.php?error=product_not_found');
    exit();
}

$product = mysqli_fetch_assoc($result_product);

// Handle deskripsi produk - replace '0' with empty string
$deskripsi_produk = $product['deskripsi_produk'];
if ($deskripsi_produk === '0' || $deskripsi_produk === 0) {
    $deskripsi_produk = '';
}

// Fetch all color images
$query_images = "SELECT * FROM admin_produk_music_gambar WHERE produk_id = '$product_id'";
$result_images = mysqli_query($db, $query_images);
$color_images = mysqli_fetch_all($result_images, MYSQLI_ASSOC);

// Fetch all combinations
$query_combinations = "SELECT * FROM admin_produk_music_kombinasi WHERE produk_id = '$product_id'";
$result_combinations = mysqli_query($db, $query_combinations);
$combinations = mysqli_fetch_all($result_combinations, MYSQLI_ASSOC);

// Extract unique values from combinations
$unique_colors = [];
$unique_tipes = [];
$unique_konektivitases = [];

foreach ($combinations as $combination) {
    if (!in_array($combination['warna'], $unique_colors)) {
        $unique_colors[] = $combination['warna'];
    }
    if (!in_array($combination['tipe'], $unique_tipes)) {
        $unique_tipes[] = $combination['tipe'];
    }
    if (!in_array($combination['konektivitas'], $unique_konektivitases)) {
        $unique_konektivitases[] = $combination['konektivitas'];
    }
}

// Prepare initial data for JavaScript
$initialData = [
    'colors' => [],
    'tipes' => $unique_tipes,
    'konektivitases' => $unique_konektivitases,
    'stocks' => [],
    'prices' => []
];

foreach($color_images as $color) {
    $photos = json_decode($color['foto_produk'], true) ?? [];
    $initialData['colors'][] = [
        'nama' => $color['warna'],
        'hex_code' => $color['hex_code'] ?? '',
        'thumbnail' => $color['foto_thumbnail'],
        'images' => $photos
    ];
}

foreach($combinations as $c) {
    $key = $c['warna'] . '|' . $c['tipe'] . '|' . $c['konektivitas'];
    $initialData['stocks'][$key] = $c['jumlah_stok'];
    $initialData['prices'][$key] = [
        'harga' => $c['harga'],
        'harga' => $c['harga'],
        'harga_diskon' => $c['harga_diskon'] ?? '',
        'diskon_persen' => ($c['harga_diskon'] > 0 && $c['harga'] > 0) ? round((($c['harga'] - $c['harga_diskon'])/$c['harga'])*100) : ''
    ];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk Music - <?php echo htmlspecialchars($product['nama_produk']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }
        
        .container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 20px;
        }
        
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border: none;
            background: white;
            overflow: hidden;
        }
        
        .card-header {
            background: linear-gradient(135deg, #4a6cf7 0%, #6a11cb 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 25px;
        }
        
        .card-header h2 {
            margin: 0;
            font-weight: 600;
            font-size: 24px;
        }
        
        .card-body {
            padding: 30px;
        }
        
        .form-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
        }
        
        .form-section h4 {
            color: #4a6cf7;
            border-bottom: 2px solid #eaeaea;
            padding-bottom: 10px;
            margin: 0 0 20px 0;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-label {
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
            display: block;
        }
        
        .form-control, textarea.form-control, select.form-control {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 12px 15px;
            transition: all 0.3s;
            width: 100%;
            box-sizing: border-box;
            font-family: inherit;
            font-size: 14px;
        }
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        
        .form-control:focus, textarea.form-control:focus, select.form-control:focus {
            border-color: #4a6cf7;
            outline: none;
            box-shadow: 0 0 0 3px rgba(74, 108, 247, 0.25);
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #4a6cf7 0%, #6a11cb 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            font-size: 16px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        
        .btn-submit:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(74, 108, 247, 0.3);
        }
        
        .btn-back {
            background-color: #6c757d;
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        
        .btn-back:hover {
            background-color: #5a6268;
            color: white;
        }
        
        .preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }
        
        .preview-item {
            position: relative;
            width: 80px;
            height: 80px;
            margin-bottom: 10px;
        }
        
        .preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        
        /* Custom styles for dynamic sections */
        .color-option, .tipe-option, .konektivitas-option {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #eaeaea;
            margin-bottom: 10px;
            position: relative;
        }
        
        .btn-danger-sm {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #dc3545;
            color: white;
            border: none;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
        }
        
        .add-option-btn {
            background-color: transparent;
            border: 2px solid #4a6cf7;
            color: #4a6cf7;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 10px;
        }
        
        .add-option-btn:hover {
            background-color: #4a6cf7;
            color: white;
        }
        
        .combination-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .combination-table th {
            background: #4a6cf7;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        
        .combination-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        
        .combination-table tr:hover {
            background-color: #f8f9fa;
        }
        
        .combination-table input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        .total-combinations {
            background: #e3f2fd;
            padding: 12px 15px;
            border-radius: 8px;
            margin-top: 15px;
            font-weight: 500;
        }
        
        .alert-info {
            background-color: #e7f3ff;
            border: 1px solid #b6d4fe;
            color: #084298;
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .file-upload {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background-color: #fafafa;
            margin-bottom: 15px;
        }
        
        .file-upload:hover {
            border-color: #4a6cf7;
            background-color: #f0f4ff;
        }
        
        .file-upload i {
            font-size: 24px;
            color: #4a6cf7;
            margin-bottom: 10px;
        }
        
        .file-upload p {
            margin: 0;
            font-size: 14px;
            color: #666;
        }
        
        #loadingOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        
        .spinner-border {
            width: 3rem;
            height: 3rem;
            border: 0.25em solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: spinner-border 0.75s linear infinite;
            color: #4a6cf7;
        }
        
        @keyframes spinner-border {
            to { transform: rotate(360deg); }
        }
        
        /* Grid System Replacement */
        .form-row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -10px 15px -10px;
        }
        
        .form-col {
            padding: 0 10px;
            box-sizing: border-box;
        }
        
        .col-3 { width: 25%; }
        .col-4 { width: 33.333%; }
        .col-6 { width: 50%; }
        .col-9 { width: 75%; }
        .col-12 { width: 100%; }
        
        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eaeaea;
        }
        
        .mb-3 { margin-bottom: 15px; }
        .mt-2 { margin-top: 10px; }
        .mt-5 { margin-top: 30px; }
        .pt-3 { padding-top: 15px; }
        .pt-4 { padding-top: 20px; }
        .text-danger { color: #dc3545; }
        .text-muted { color: #6c757d; }
        .text-center { text-align: center; }
        .p-4 { padding: 20px; }
        
        /* Responsive */
        @media (max-width: 1200px) {
            .container {
                max-width: 95%;
                padding: 15px;
            }
            
            .form-row {
                margin: 0 -8px 15px -8px;
            }
            
            .form-col {
                padding: 0 8px;
            }
        }
        
        @media (max-width: 768px) {
            .col-3, .col-4, .col-6, .col-9, .col-12 {
                width: 100%;
            }
            
            .form-actions {
                flex-direction: column;
                gap: 15px;
            }
            
            .btn-submit, .btn-back {
                width: 100%;
                justify-content: center;
            }
            
            .combination-table {
                display: block;
                overflow-x: auto;
            }
            
            .combination-table input {
                width: 100px;
            }
        }
        
        /* Color Radio Button Styles */
        .color-radio-group {
            display: flex;
            gap: 12px;
            align-items: center;
            margin-top: 10px;
        }
        
        .color-radio-item {
            position: relative;
        }
        
        .color-radio-item input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .color-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #ddd;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .color-radio-item input[type="radio"]:checked + .color-circle {
            border: 3px solid #4a6cf7;
            box-shadow: 0 0 0 3px rgba(74, 108, 247, 0.2);
            transform: scale(1.1);
        }
        
        .color-circle:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .color-label-text {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
            text-align: center;
        }
        
        /* Hex Input with # prefix */
        .hex-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        
        .hex-input-wrapper::before {
            content: '#';
            position: absolute;
            left: 12px;
            color: #666;
            font-weight: 500;
            pointer-events: none;
            z-index: 1;
        }
        
        .hex-input-wrapper input {
            padding-left: 28px !important;
        }
        
        /* Color Picker Integration */
        .color-picker-input {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
            border: none;
        }
    </style>
</head>
<body>
    <div id="loadingOverlay">
        <div class="spinner-border" role="status"></div>
        <div style="margin-top: 10px; font-weight: bold;">Menyimpan perubahan...</div>
    </div>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-edit"></i> Edit Produk Music</h2>
            </div>
            <div class="card-body">
                <form id="editMusicForm" action="api/api-edit-music.php" method="POST" enctype="multipart/form-data" autocomplete="off">
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                    
                    <!-- Informasi Produk -->
                    <div class="form-section">
                        <h4><i class="fas fa-info-circle"></i> Informasi Dasar</h4>
                        <div class="mb-3">
                            <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_produk" value="<?php echo htmlspecialchars($product['nama_produk']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Kategori Music</label>
                            <input type="text" class="form-control" name="kategori" value="<?php echo htmlspecialchars($product['kategori'] ?? ''); ?>" placeholder="Contoh: AirPods, HomePod, Beats, Aksesoris Audio" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Deskripsi Produk</label>
                            <textarea class="form-control" name="deskripsi_produk" rows="4" placeholder="Masukkan deskripsi lengkap produk..."><?php echo htmlspecialchars($deskripsi_produk); ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Warna dengan Gambar -->
                    <div class="form-section">
                        <h4><i class="fas fa-palette"></i> Warna Produk</h4>
                        <div class="alert-info">
                            <i class="fas fa-info-circle"></i>
                            Anda bisa membiarkan field upload kosong jika tidak ingin mengubah gambar.
                        </div>
                        
                        <div id="colorsContainer">
                            <!-- Warna akan di-generate oleh Javascript -->
                        </div>
                        
                        <button type="button" class="add-option-btn" onclick="addColor()">
                            <i class="fas fa-plus"></i> Tambah Warna Lain
                        </button>
                    </div>
                    
                    <!-- Tipe -->
                    <div class="form-section">
                        <h4><i class="fas fa-headphones"></i> Tipe</h4>
                        
                        <div id="tipesContainer">
                            <!-- Tipe akan di-generate oleh Javascript -->
                        </div>
                        
                        <button type="button" class="add-option-btn" onclick="addTipe()">
                            <i class="fas fa-plus"></i> Tambah Tipe Lain
                        </button>
                    </div>
                    
                    <!-- Konektivitas -->
                    <div class="form-section">
                        <h4><i class="fas fa-bluetooth"></i> Konektivitas</h4>
                        
                        <div id="konektivitasesContainer">
                            <!-- Konektivitas akan di-generate oleh Javascript -->
                        </div>
                        
                        <button type="button" class="add-option-btn" onclick="addKonektivitas()">
                            <i class="fas fa-plus"></i> Tambah Konektivitas Lain
                        </button>
                    </div>
                    
                    <!-- Tabel Kombinasi & Stok -->
                    <div class="form-section">
                        <h4><i class="fas fa-table"></i> Kombinasi & Stok</h4>
                        <div class="alert-info">
                            <i class="fas fa-info-circle"></i>
                            Sistem akan membuat semua kombinasi dari Warna, Tipe, dan Konektivitas.
                        </div>
                        
                        <div id="combinationsContainer">
                            <!-- Table generated by JS -->
                        </div>
                        
                        <div class="total-combinations" id="totalCombinations">
                            Loading data...
                        </div>
                    </div>
                    
                    <!-- Tombol Aksi -->
                    <div class="form-actions">
                        <a href="view-music.php?id=<?php echo $product_id; ?>" class="btn-back">
                            <i class="fas fa-arrow-left"></i> Kembali ke Detail
                        </a>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Data dari PHP -->
    <script>
        const initialData = <?php echo json_encode($initialData); ?>;
        const uploadPath = '../../uploads/';
        
        let colorCount = 0;
        let tipeCount = 0;
        let konektivitasCount = 0;
        
        // Initialize Form
        window.addEventListener('DOMContentLoaded', () => {
            // Colors
            if (initialData.colors && initialData.colors.length > 0) {
                initialData.colors.forEach(color => addColor(color));
            } else {
                addColor(); // Default empty
            }
            
            // Tipes
            if (initialData.tipes && initialData.tipes.length > 0) {
                initialData.tipes.forEach(tipe => addTipe(tipe));
            } else {
                addTipe();
            }
            
            // Konektivitases
            if (initialData.konektivitases && initialData.konektivitases.length > 0) {
                initialData.konektivitases.forEach(konektivitas => addKonektivitas(konektivitas));
            } else {
                addKonektivitas();
            }
            
            // Generate Combinations with slight delay to ensure DOM is ready
            setTimeout(() => {
                generateCombinations();
            }, 100);
        });

        // Form Submission
        document.getElementById('editMusicForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const overlay = document.getElementById('loadingOverlay');
            overlay.style.display = 'flex';
            
            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Berhasil: ' + data.message);
                    window.location.href = 'view-music.php?id=<?php echo $product_id; ?>';
                } else {
                    alert('Gagal: ' + data.message);
                    overlay.style.display = 'none';
                }
            })
            .catch(err => {
                alert('Error sistem');
                console.error(err);
                overlay.style.display = 'none';
            });
        });

        // Warna
        function addColor(data = null) {
            const container = document.getElementById('colorsContainer');
            const newIndex = colorCount;
            
            const newColor = document.createElement('div');
            newColor.className = 'color-option';
            newColor.dataset.colorIndex = newIndex;
            
            let thumbnailPreview = '';
            let productImagesPreview = '';
            let isExisting = false;
            
            if (data) {
                isExisting = true;
                if (data.thumbnail) {
                    thumbnailPreview = `
                        <div id="thumbnailPreview-${newIndex}" class="preview-item">
                            <img id="thumbnailImg-${newIndex}" src="${uploadPath}${data.thumbnail}" alt="Thumbnail Preview">
                        </div>
                        <input type="hidden" name="warna[${newIndex}][existing]" value="1">
                        <input type="hidden" name="warna[${newIndex}][old_thumbnail]" value="${data.thumbnail}">
                    `;
                }
                
                if (data.images && data.images.length > 0) {
                    productImagesPreview = `<div class="preview-container">`;
                    data.images.forEach(img => {
                        productImagesPreview += `
                            <div class="preview-item">
                                <img src="${uploadPath}${img}" alt="Product Image">
                            </div>
                        `;
                    });
                    productImagesPreview += `</div>`;
                }
            }
            
            
            newColor.innerHTML = `
                <div class="form-row">
                    <div class="form-col col-3">
                        <label class="form-label">Nama Warna</label>
                        <input type="text" class="form-control" name="warna[${newIndex}][nama]" 
                               placeholder="Nama Warna (Contoh: White)" required value="${data ? data.nama : ''}" onchange="generateCombinations()">
                    </div>
                    <div class="form-col col-2">
                        <label class="form-label">Kode Warna (Hex)</label>
                        <div class="hex-input-wrapper">
                            <input type="text" class="form-control color-hex" name="warna[${newIndex}][hex_code]" 
                                   placeholder="000000" value="${data ? (data.hex_code || '') : ''}" oninput="updateColorPreview(this, ${newIndex})">
                        </div>
                        <small class="text-muted">Contoh: 2c3e50</small>
                    </div>
                    <div class="form-col col-1">
                        <label class="form-label">Preview</label>
                        <div class="color-radio-group">
                            <label class="color-radio-item">
                                <input type="radio" name="color_preview_${newIndex}" checked>
                                <div class="color-circle" id="color-preview-${newIndex}" style="background-color: ${data && data.hex_code ? '#' + data.hex_code.replace('#', '') : '#cccccc'};" title="Klik untuk memilih warna">
                                    <input type="color" class="color-picker-input" id="color-picker-${newIndex}" value="${data && data.hex_code ? '#' + data.hex_code.replace('#', '') : '#cccccc'}" onchange="handleColorPicker(this, ${newIndex})">
                                </div>
                            </label>
                        </div>
                    </div>
                    <div class="form-col col-6">
                        <div class="form-row">
                            <div class="form-col col-6">
                                <label class="form-label">Thumbnail Warna ${!isExisting ? '<span class="text-danger">*</span>' : ''}</label>
                                <div class="file-upload" onclick="document.getElementById('thumbnail-${newIndex}').click()">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p>${isExisting ? 'Ubah thumbnail' : 'Upload thumbnail'}</p>
                                    <input type="file" id="thumbnail-${newIndex}" 
                                           name="warna[${newIndex}][thumbnail]" 
                                           accept="image/*" style="display: none;" ${!isExisting ? 'required' : ''} 
                                           onchange="previewThumbnail(${newIndex}, this)">
                                </div>
                                <div class="preview-container">
                                    ${thumbnailPreview}
                                    <div id="thumbnailNewPreview-${newIndex}" class="preview-item" style="display: none;">
                                        <img id="thumbnailNewImg-${newIndex}" src="" alt="New Thumbnail Preview">
                                    </div>
                                </div>
                            </div>
                            <div class="form-col col-6">
                                <label class="form-label">Foto Produk</label>
                                <div class="file-upload" onclick="document.getElementById('productImages-${newIndex}').click()">
                                    <i class="fas fa-images"></i>
                                    <p>${isExisting ? 'Tambah foto' : 'Upload foto produk'}</p>
                                    <input type="file" id="productImages-${newIndex}" 
                                           name="warna[${newIndex}][product_images][]" 
                                           accept="image/*" multiple style="display: none;" 
                                           onchange="previewProductImages(${newIndex}, this)">
                                </div>
                                <div class="preview-container" id="productImagesPreview-${newIndex}">
                                    ${productImagesPreview}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-danger-sm" onclick="removeColor(${newIndex})">
                    ×
                </button>
            `;
            
            container.appendChild(newColor);
            colorCount++;
            generateCombinations();
        }
        
        // Update color preview when hex code changes
        function updateColorPreview(input, idx) {
            let hexValue = input.value.trim();
            const preview = document.getElementById(`color-preview-${idx}`);
            
            // Add # if not present
            if (hexValue && !hexValue.startsWith('#')) {
                hexValue = '#' + hexValue;
            }
            
            // Validate hex color
            const hexRegex = /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/;
            if (hexRegex.test(hexValue)) {
                preview.style.backgroundColor = hexValue;
                // Update color picker
                const picker = document.getElementById(`color-picker-${idx}`);
                if (picker) {
                    picker.value = hexValue;
                }
            } else {
                // If invalid, show gray
                preview.style.backgroundColor = '#cccccc';
            }
        }
        
        // Handle color picker selection
        function handleColorPicker(picker, idx) {
            const selectedColor = picker.value; // Format: #rrggbb
            const preview = document.getElementById(`color-preview-${idx}`);
            const hexInput = document.querySelector(`input[name="warna[${idx}][hex_code]"]`);
            
            // Update preview
            if (preview) {
                preview.style.backgroundColor = selectedColor;
            }
            
            // Update hex input (remove # since we show it as prefix)
            if (hexInput) {
                hexInput.value = selectedColor.substring(1); // Remove # from #rrggbb
            }
        }

        
        function removeColor(index) {
            const colorElements = document.querySelectorAll('.color-option');
            if (colorElements.length <= 1) {
                alert('Minimal harus ada satu warna');
                return;
            }
            
            const colorElement = document.querySelector(`.color-option[data-color-index="${index}"]`);
            if (colorElement) {
                colorElement.remove();
                generateCombinations();
            }
        }
        
        // Tipe
        function addTipe(data = null) {
            const container = document.getElementById('tipesContainer');
            const newIndex = tipeCount;
            
            const newTipe = document.createElement('div');
            newTipe.className = 'tipe-option';
            newTipe.dataset.tipeIndex = newIndex;
            newTipe.innerHTML = `
                <input type="text" class="form-control" name="tipe[${newIndex}]" 
                       placeholder="Tipe (Contoh: Pro, Max, Generation)" required value="${data ? data : ''}" onchange="generateCombinations()">
                <button type="button" class="btn-danger-sm" onclick="removeTipe(${newIndex})">
                    ×
                </button>
            `;
            
            container.appendChild(newTipe);
            tipeCount++;
            generateCombinations();
        }
        
        function removeTipe(index) {
            const tipeElements = document.querySelectorAll('.tipe-option');
            if (tipeElements.length <= 1) {
                alert('Minimal harus ada satu tipe');
                return;
            }
            
            const tipeElement = document.querySelector(`.tipe-option[data-tipe-index="${index}"]`);
            if (tipeElement) {
                tipeElement.remove();
                generateCombinations();
            }
        }
        
        // Konektivitas
        function addKonektivitas(data = null) {
            const container = document.getElementById('konektivitasesContainer');
            const newIndex = konektivitasCount;
            
            const newKonektivitas = document.createElement('div');
            newKonektivitas.className = 'konektivitas-option';
            newKonektivitas.dataset.konektivitasIndex = newIndex;
            newKonektivitas.innerHTML = `
                <input type="text" class="form-control" name="konektivitas[${newIndex}]" 
                       placeholder="Konektivitas (Contoh: Bluetooth, Lightning, USB-C)" required value="${data ? data : ''}" onchange="generateCombinations()">
                <button type="button" class="btn-danger-sm" onclick="removeKonektivitas(${newIndex})">
                    ×
                </button>
            `;
            
            container.appendChild(newKonektivitas);
            konektivitasCount++;
            generateCombinations();
        }
        
        function removeKonektivitas(index) {
            const konektivitasElements = document.querySelectorAll('.konektivitas-option');
            if (konektivitasElements.length <= 1) {
                alert('Minimal harus ada satu konektivitas');
                return;
            }
            
            const konektivitasElement = document.querySelector(`.konektivitas-option[data-konektivitas-index="${index}"]`);
            if (konektivitasElement) {
                konektivitasElement.remove();
                generateCombinations();
            }
        }
        
        // Preview functions
        function previewThumbnail(index, input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewNew = document.getElementById(`thumbnailNewPreview-${index}`);
                    const imgNew = document.getElementById(`thumbnailNewImg-${index}`);
                    if (previewNew && imgNew) {
                        imgNew.src = e.target.result;
                        previewNew.style.display = 'block';
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        function previewProductImages(index, input) {
            const container = document.getElementById(`productImagesPreview-${index}`);
            
            if (input.files) {
                // Remove only previous newly added previews (with class 'new-preview')
                const existingNew = container.querySelectorAll('.new-preview');
                existingNew.forEach(el => el.remove());

                Array.from(input.files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'preview-item new-preview';
                        div.innerHTML = `<img src="${e.target.result}" alt="New Image">`;
                        container.appendChild(div);
                    }
                    reader.readAsDataURL(file);
                });
            }
        }
        
        function calculateRowDiscount(uniqueId) {
            const priceInput = document.querySelector(`input[name="combinations[${uniqueId}][harga]"]`);
            const percentInput = document.querySelector(`input[name="combinations[${uniqueId}][diskon_persen]"]`);
            const discountInput = document.querySelector(`input[name="combinations[${uniqueId}][harga_diskon]"]`);
            
            if (!priceInput || !percentInput || !discountInput) return;
            
            const price = Number(priceInput.value) || 0;
            const percent = Number(percentInput.value) || 0;
            
            if (price > 0 && percent > 0) {
                const discountPrice = Math.round(price - (price * (percent / 100)));
                discountInput.value = discountPrice;
            } else {
                discountInput.value = '';
            }
        }

        // Generate Combinations
        function generateCombinations() {
            const container = document.getElementById('combinationsContainer');
            const totalContainer = document.getElementById('totalCombinations');
            
            // Collect data from DOM inputs
            const colors = [];
            document.querySelectorAll('.color-option input[name*="[nama]"]').forEach(input => {
                if (input.value.trim()) colors.push(input.value.trim());
            });
            
            const tipes = [];
            document.querySelectorAll('.tipe-option input').forEach(input => {
                const val = input.value.trim();
                if (val) tipes.push(val);
            });
            
            const konektivitases = [];
            document.querySelectorAll('.konektivitas-option input').forEach(input => {
                const val = input.value.trim();
                if (val) konektivitases.push(val);
            });
            
            // Generate table
            const totalCombinations = colors.length * tipes.length * konektivitases.length;
            totalContainer.innerHTML = 
                `Total Kombinasi: <strong>${totalCombinations}</strong> (${colors.length} warna × ${tipes.length} tipe × ${konektivitases.length} konektivitas)`;
            
            if (totalCombinations > 0) {
                let tableHTML = `
                    <table class="combination-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Warna</th>
                                <th>Tipe</th>
                                <th>Konektivitas</th>
                                <th>Harga Normal</th>
                                <th>Diskon (%)</th>
                                <th>Jumlah Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                
                let counter = 1;
                colors.forEach((color, cIdx) => {
                    tipes.forEach((tipe, tIdx) => {
                        konektivitases.forEach((konektivitas, kIdx) => {
                            const uniqueId = `${cIdx}_${tIdx}_${kIdx}`; // Just for DOM uniqueness
                            
                            // Try to find existing data
                            const stockKey = `${color}|${tipe}|${konektivitas}`;
                            const existingStock = initialData.stocks[stockKey] !== undefined ? initialData.stocks[stockKey] : 0;
                            const existingPrice = initialData.prices[stockKey] !== undefined ? initialData.prices[stockKey] : { harga: '', harga_diskon: '' };
                            
                            tableHTML += `
                                <tr>
                                    <td>${counter}</td>
                                    <td>${color}</td>
                                    <td>${tipe}</td>
                                    <td>${konektivitas}</td>
                                    <td>
                                        <input type="number" class="form-control" style="width: 120px;" 
                                               name="combinations[${uniqueId}][harga]" 
                                               value="${existingPrice.harga}" min="0" required oninput="calculateRowDiscount('${uniqueId}')">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" style="width: 80px; display: inline-block;" 
                                               name="combinations[${uniqueId}][diskon_persen]" 
                                               value="${existingPrice.diskon_persen}" min="0" max="100" placeholder="0" oninput="calculateRowDiscount('${uniqueId}')">
                                        <input type="hidden" name="combinations[${uniqueId}][harga_diskon]" value="${existingPrice.harga_diskon}">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" style="width: 100px;" 
                                               name="combinations[${uniqueId}][jumlah_stok]" 
                                               value="${existingStock || ''}" placeholder="0" min="0" required>
                                    </td>
                                    <!-- Hidden Inputs -->
                                    <input type="hidden" name="combinations[${uniqueId}][warna]" value="${color}">
                                    <input type="hidden" name="combinations[${uniqueId}][tipe]" value="${tipe}">
                                    <input type="hidden" name="combinations[${uniqueId}][konektivitas]" value="${konektivitas}">
                                </tr>
                            `;
                            counter++;
                        });
                    });
                });
                
                tableHTML += `</tbody></table>`;
                container.innerHTML = tableHTML;
            } else {
                container.innerHTML = '<div class="text-center p-4 text-muted">Lengkapi data warna, tipe, dan konektivitas untuk melihat tabel kombinasi</div>';
            }
        }
        
        // Listen for input changes to update combinations
        document.addEventListener('input', function(e) {
            if (e.target.matches('input[name*="[nama]"], input[name*="tipe"], input[name*="konektivitas"]')) {
               if (!e.target.name.includes('jumlah_stok') && !e.target.name.includes('harga') && !e.target.name.includes('harga_diskon')) {
                   generateCombinations();
               }
            }
        });
    </script>
</body>
</html>