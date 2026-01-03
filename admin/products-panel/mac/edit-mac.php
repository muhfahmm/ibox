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
    header('Location: mac.php?error=invalid_id');
    exit();
}

$product_id = mysqli_real_escape_string($db, $_GET['id']);

// Fetch main product data
$query_product = "SELECT * FROM admin_produk_mac WHERE id = '$product_id'";
$result_product = mysqli_query($db, $query_product);

if (mysqli_num_rows($result_product) === 0) {
    header('Location: mac.php?error=product_not_found');
    exit();
}

$product = mysqli_fetch_assoc($result_product);

// Fetch all color images
$query_images = "SELECT * FROM admin_produk_mac_gambar WHERE produk_id = '$product_id'";
$result_images = mysqli_query($db, $query_images);
$color_images = mysqli_fetch_all($result_images, MYSQLI_ASSOC);

// Fetch all combinations
$query_combinations = "SELECT * FROM admin_produk_mac_kombinasi WHERE produk_id = '$product_id'";
$result_combinations = mysqli_query($db, $query_combinations);
$combinations = mysqli_fetch_all($result_combinations, MYSQLI_ASSOC);

// Extract unique values from combinations
$unique_colors = [];
$unique_processors = [];
$unique_storages = [];
$unique_rams = [];

foreach ($combinations as $combination) {
    $trimmed_color = trim($combination['warna']);
    $trimmed_processor = trim($combination['processor']);
    $trimmed_storage = trim($combination['penyimpanan']);
    $trimmed_ram = trim($combination['ram']);
    
    if (!in_array($trimmed_color, $unique_colors)) {
        $unique_colors[] = $trimmed_color;
    }
    if (!in_array($trimmed_processor, $unique_processors)) {
        $unique_processors[] = $trimmed_processor;
    }
    if (!in_array($trimmed_storage, $unique_storages)) {
        $unique_storages[] = $trimmed_storage;
    }
    if (!in_array($trimmed_ram, $unique_rams)) {
        $unique_rams[] = $trimmed_ram;
    }
}

// Group combinations by storage for easier editing
$storage_data = [];
foreach ($combinations as $combination) {
    $storage = trim($combination['penyimpanan']);
    if (!isset($storage_data[$storage])) {
        $storage_data[$storage] = [
            'size' => $storage,
            'harga' => $combination['harga'],
            'harga_diskon' => (!empty($combination['harga_diskon']) && $combination['harga_diskon'] > 0) ? $combination['harga_diskon'] : '',
            'diskon_persen' => (!empty($combination['harga_diskon']) && $combination['harga_diskon'] > 0 && $combination['harga'] > 0) ? round((($combination['harga'] - $combination['harga_diskon']) / $combination['harga']) * 100) : ''
        ];
    }
}

// Group combinations by RAM
$ram_data = [];
$processor_data = [];
foreach ($combinations as $combination) {
    $ram = trim($combination['ram']);
    if (!isset($ram_data[$ram])) {
        // Ambil harga dari kombinasi ini sebagai referensi
        $ram_data[$ram] = [
            'size' => $ram,
            'harga' => $combination['harga'],
            'harga_diskon' => (!empty($combination['harga_diskon']) && $combination['harga_diskon'] > 0) ? $combination['harga_diskon'] : '',
            'diskon_persen' => (!empty($combination['harga_diskon']) && $combination['harga_diskon'] > 0 && $combination['harga'] > 0) ? round((($combination['harga'] - $combination['harga_diskon']) / $combination['harga']) * 100) : ''
        ];
    }
    
    $proc = trim($combination['processor']);
    if (!isset($processor_data[$proc])) {
        // Ambil harga dari kombinasi ini sebagai referensi
        $processor_data[$proc] = [
            'name' => $proc,
            'harga' => $combination['harga'],
            'harga_diskon' => (!empty($combination['harga_diskon']) && $combination['harga_diskon'] > 0) ? $combination['harga_diskon'] : '',
            'diskon_persen' => (!empty($combination['harga_diskon']) && $combination['harga_diskon'] > 0 && $combination['harga'] > 0) ? round((($combination['harga'] - $combination['harga_diskon']) / $combination['harga']) * 100) : ''
        ];
    }
}

// Prepare initial data for JavaScript
$initialData = [
    'colors' => [],
    'processors' => array_values($processor_data),
    'storages' => array_values($storage_data),
    'rams' => array_values($ram_data),
    'stocks' => []
];

foreach($color_images as $color) {
    $photos = json_decode($color['foto_produk'], true) ?? [];
    $initialData['colors'][] = [
        'nama' => trim($color['warna']),
        'thumbnail' => $color['foto_thumbnail'],
        'images' => $photos
    ];
}

foreach($combinations as $c) {
    // Create key with trim untuk mencocokkan dengan JavaScript
    $key = trim($c['warna']) . '|' . trim($c['processor']) . '|' . trim($c['penyimpanan']) . '|' . trim($c['ram']);
    $initialData['stocks'][$key] = $c['jumlah_stok'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk Mac - <?php echo htmlspecialchars($product['nama_produk']); ?></title>
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
        
        .form-control, textarea.form-control {
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
        
        .form-control:focus, textarea.form-control:focus {
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
        .color-option, .processor-option, .storage-option, .ram-option {
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
            width: 100px;
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
                <h2><i class="fas fa-edit"></i> Edit Produk Mac</h2>
            </div>
            <div class="card-body">
                <form id="editMacForm" action="api/api-edit-mac.php" method="POST" enctype="multipart/form-data" autocomplete="off">
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                    
                    <!-- Informasi Produk -->
                    <div class="form-section">
                        <h4><i class="fas fa-info-circle"></i> Informasi Dasar</h4>
                        <div class="mb-3">
                            <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_produk" value="<?php echo htmlspecialchars($product['nama_produk']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <input type="text" class="form-control" name="kategori" value="<?php echo htmlspecialchars($product['kategori'] ?? ''); ?>" placeholder="Contoh: MacBook Pro, MacBook Air, iMac" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi Produk</label>
                            <textarea class="form-control" name="deskripsi_produk" rows="4" placeholder="Masukkan deskripsi lengkap produk..."><?php echo htmlspecialchars($product['deskripsi_produk']); ?></textarea>
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
                    
                    <!-- Processor -->
                    <div class="form-section">
                        <h4><i class="fas fa-microchip"></i> Processor</h4>
                        
                        <div id="processorsContainer">
                            <!-- Processor akan di-generate oleh Javascript -->
                        </div>
                        
                        <button type="button" class="add-option-btn" onclick="addProcessor()">
                            <i class="fas fa-plus"></i> Tambah Processor Lain
                        </button>
                    </div>
                    
                    <!-- Penyimpanan dengan Harga -->
                    <div class="form-section">
                        <h4><i class="fas fa-hdd"></i> Penyimpanan & Harga</h4>
                        
                        <div id="storagesContainer">
                            <!-- Penyimpanan akan di-generate oleh Javascript -->
                        </div>
                        
                        <button type="button" class="add-option-btn" onclick="addStorage()">
                            <i class="fas fa-plus"></i> Tambah Penyimpanan Lain
                        </button>
                    </div>
                    
                    <!-- RAM -->
                    <div class="form-section">
                        <h4><i class="fas fa-memory"></i> RAM</h4>
                        
                        <div id="ramsContainer">
                            <!-- RAM akan di-generate oleh Javascript -->
                        </div>
                        
                        <button type="button" class="add-option-btn" onclick="addRam()">
                            <i class="fas fa-plus"></i> Tambah RAM Lain
                        </button>
                    </div>
                    
                    <!-- Tabel Kombinasi & Stok -->
                    <div class="form-section">
                        <h4><i class="fas fa-table"></i> Kombinasi & Stok</h4>
                        <div class="alert-info">
                            <i class="fas fa-info-circle"></i>
                            Sistem akan membuat semua kombinasi dari Warna, Processor, Penyimpanan, dan RAM. 
                            Untuk kombinasi yang sudah ada, stok akan ditampilkan.
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
                        <a href="view-mac.php?id=<?php echo $product_id; ?>" class="btn-back">
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
        let processorCount = 0;
        let storageCount = 0;
        let ramCount = 0;
        
        // Initialize Form
        window.addEventListener('DOMContentLoaded', () => {
            // Colors
            if (initialData.colors && initialData.colors.length > 0) {
                initialData.colors.forEach(color => addColor(color));
            } else {
                addColor(); // Default empty
            }
            
            // Processors
            if (initialData.processors && initialData.processors.length > 0) {
                initialData.processors.forEach(processor => addProcessor(processor));
            } else {
                addProcessor();
            }
            
            // Storages
            if (initialData.storages && initialData.storages.length > 0) {
                initialData.storages.forEach(storage => addStorage(storage));
            } else {
                addStorage();
            }
            
            // RAMs
            if (initialData.rams && initialData.rams.length > 0) {
                initialData.rams.forEach(ram => addRam(ram));
            } else {
                addRam();
            }
            
            // Generate Combinations with slight delay to ensure DOM is ready
            setTimeout(() => {
                generateCombinations();
            }, 100);
        });

        // Form Submission
        document.getElementById('editMacForm').addEventListener('submit', function(e) {
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
                    window.location.href = 'view-mac.php?id=<?php echo $product_id; ?>';
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
                        <input type="hidden" name="warna[${newIndex}][existing_thumbnail]" value="${data.thumbnail}">
                    `;
                }
                
                if (data.images && data.images.length > 0) {
                    productImagesPreview = `<div class="preview-container">`;
                    data.images.forEach(img => {
                        productImagesPreview += `
                            <div class="preview-item">
                                <img src="${uploadPath}${img}" alt="Product Image">
                                <input type="hidden" name="warna[${newIndex}][existing_images][]" value="${img}">
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
                               placeholder="Nama Warna (Contoh: Space Gray)" required value="${data ? data.nama : ''}" onchange="generateCombinations()">
                    </div>
                    <div class="form-col col-9">
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
        
        // Processor
        function addProcessor(data = null) {
            const container = document.getElementById('processorsContainer');
            const newIndex = processorCount;
            
            const newProcessor = document.createElement('div');
            newProcessor.className = 'processor-option';
            newProcessor.dataset.processorIndex = newIndex;
            newProcessor.innerHTML = `
                <div class="form-row">
                    <div class="form-col col-3">
                        <label class="form-label">Processor</label>
                        <input type="text" class="form-control processor-value" name="processor[${newIndex}][name]" 
                               placeholder="Contoh: M3 Pro" required value="${data ? data.name : ''}" onchange="generateCombinations()">
                    </div>
                    <div class="form-col col-4">
                        <label class="form-label">Harga Normal <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="processor[${newIndex}][harga]" 
                               placeholder="Harga" min="0" required value="${data ? data.harga : ''}" onchange="generateCombinations()">
                    </div>
                    <div class="form-col col-4">
                        <label class="form-label">Diskon (%)</label>
                        <input type="number" class="form-control" name="processor[${newIndex}][diskon_persen]" 
                               placeholder="Diskon (%)" min="0" max="100" value="${data ? data.diskon_persen : ''}" onchange="generateCombinations()">
                    </div>
                </div>
                <button type="button" class="btn-danger-sm" onclick="removeProcessor(${newIndex})">
                    ×
                </button>
            `;
            
            container.appendChild(newProcessor);
            processorCount++;
            generateCombinations();
        }
        
        function removeProcessor(index) {
            const processorElements = document.querySelectorAll('.processor-option');
            if (processorElements.length <= 1) {
                alert('Minimal harus ada satu processor');
                return;
            }
            
            const processorElement = document.querySelector(`.processor-option[data-processor-index="${index}"]`);
            if (processorElement) {
                processorElement.remove();
                generateCombinations();
            }
        }
        
        // Penyimpanan
        function addStorage(data = null) {
            const container = document.getElementById('storagesContainer');
            const newIndex = storageCount;
            
            const newStorage = document.createElement('div');
            newStorage.className = 'storage-option';
            newStorage.dataset.storageIndex = newIndex;
            
            newStorage.innerHTML = `
                <div class="form-row">
                    <div class="form-col col-3">
                        <label class="form-label">Ukuran</label>
                        <input type="text" class="form-control" name="penyimpanan[${newIndex}][size]" 
                               placeholder="Ukuran (Contoh: 512GB)" required value="${data && data.size ? data.size : ''}" onchange="generateCombinations()">
                    </div>
                    <div class="form-col col-4">
                        <label class="form-label">Harga Normal <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="penyimpanan[${newIndex}][harga]" 
                               placeholder="Harga" min="0" required value="${data && data.harga ? data.harga : ''}" onchange="generateCombinations()">
                    </div>
                    <div class="form-col col-4">
                        <label class="form-label">Diskon (%)</label>
                        <input type="number" class="form-control" name="penyimpanan[${newIndex}][diskon_persen]" 
                               placeholder="Diskon (%)" min="0" max="100" value="${data && data.diskon_persen ? data.diskon_persen : ''}" onchange="generateCombinations()">
                    </div>
                </div>
                <button type="button" class="btn-danger-sm" onclick="removeStorage(${newIndex})">
                    ×
                </button>
            `;
            
            container.appendChild(newStorage);
            storageCount++;
            generateCombinations();
        }
        
        function removeStorage(index) {
            const storageElements = document.querySelectorAll('.storage-option');
            if (storageElements.length <= 1) {
                alert('Minimal harus ada satu penyimpanan');
                return;
            }
            
            const storageElement = document.querySelector(`.storage-option[data-storage-index="${index}"]`);
            if (storageElement) {
                storageElement.remove();
                generateCombinations();
            }
        }
        
        // RAM
        function addRam(data = null) {
            const container = document.getElementById('ramsContainer');
            const newIndex = ramCount;
            
            const newRam = document.createElement('div');
            newRam.className = 'ram-option';
            newRam.dataset.ramIndex = newIndex;
            
            newRam.innerHTML = `
                <div class="form-row">
                    <div class="form-col col-3">
                        <label class="form-label">RAM</label>
                        <input type="text" class="form-control ram-value" name="ram[${newIndex}][size]" 
                               placeholder="Contoh: 16GB" required value="${data ? data.size : ''}" onchange="generateCombinations()">
                    </div>
                    <div class="form-col col-4">
                        <label class="form-label">Harga Normal <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="ram[${newIndex}][harga]" 
                               placeholder="Harga" min="0" required value="${data ? data.harga : ''}" onchange="generateCombinations()">
                    </div>
                    <div class="form-col col-4">
                        <label class="form-label">Diskon (%)</label>
                        <input type="number" class="form-control" name="ram[${newIndex}][diskon_persen]" 
                               placeholder="Diskon (%)" min="0" max="100" value="${data ? data.diskon_persen : ''}" onchange="generateCombinations()">
                    </div>
                </div>
                <button type="button" class="btn-danger-sm" onclick="removeRam(${newIndex})">
                    ×
                </button>
            `;
            
            container.appendChild(newRam);
            ramCount++;
            generateCombinations();
        }
        
        function removeRam(index) {
            const ramElements = document.querySelectorAll('.ram-option');
            if (ramElements.length <= 1) {
                alert('Minimal harus ada satu RAM');
                return;
            }
            
            const ramElement = document.querySelector(`.ram-option[data-ram-index="${index}"]`);
            if (ramElement) {
                ramElement.remove();
                generateCombinations();
            }
        }
        
        // Generate Combinations Table
        function generateCombinations() {
            const colors = Array.from(document.querySelectorAll('input[name^="warna"][name$="[nama]"]')).map(i => i.value).filter(v => v);
            
            const processors = Array.from(document.querySelectorAll('.processor-option')).map(row => {
                return {
                    name: row.querySelector('input[name*="[name]"]')?.value || '',
                    price: Number(row.querySelector('input[name*="[harga]"]')?.value) || 0,
                    discount: Number(row.querySelector('input[name*="[diskon_persen]"]')?.value) || 0
                };
            }).filter(p => p.name);

            const storages = Array.from(document.querySelectorAll('.storage-option')).map(row => {
                return {
                    size: row.querySelector('input[name*="[size]"]').value,
                    price: Number(row.querySelector('input[name*="[harga]"]').value) || 0,
                    discount: Number(row.querySelector('input[name*="[diskon_persen]"]').value) || 0
                };
            }).filter(s => s.size);
            
            const rams = Array.from(document.querySelectorAll('.ram-option')).map(row => {
                return {
                    size: row.querySelector('input[name*="[size]"]').value,
                    price: Number(row.querySelector('input[name*="[harga]"]').value) || 0,
                    discount: Number(row.querySelector('input[name*="[diskon_persen]"]').value) || 0
                };
            }).filter(r => r.size);
            
            const tbody = document.getElementById('combinationsContainer');
            const totalInfo = document.getElementById('totalCombinations');
            
            if (colors.length === 0 || processors.length === 0 || storages.length === 0 || rams.length === 0) {
                tbody.innerHTML = '<div class="text-center p-4 text-muted">Lengkapi data di atas untuk melihat kombinasi</div>';
                totalInfo.textContent = 'Menunggu data lengkap...';
                return;
            }

            const totalCount = colors.length * processors.length * storages.length * rams.length;
            
            let html = `
                <table class="combination-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Warna</th>
                            <th>Processor</th>
                            <th>Penyimpanan</th>
                            <th>RAM</th>
                            <th>Harga</th>
                            <th>Diskon</th>
                            <th>Jumlah Stok</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            let counter = 1;
            
            colors.forEach((color, cIdx) => {
                processors.forEach((processor, pIdx) => {
                    storages.forEach((storage, sIdx) => {
                        rams.forEach((ram, rIdx) => {
                            const uniqueId = `${cIdx}_${pIdx}_${sIdx}_${rIdx}`;
                            
                            const stockKey = `${color}|${processor.name}|${storage.size}|${ram.size}`;
                            const existingStock = initialData.stocks[stockKey] !== undefined ? initialData.stocks[stockKey] : 0;
                            
                            const totalPrice = storage.price + ram.price + processor.price;
                            const sPrice = storage.price - (storage.price * (storage.discount / 100));
                            const rPrice = ram.price - (ram.price * (ram.discount / 100));
                            const pPrice = processor.price - (processor.price * (processor.discount / 100));
                            totalDiscount = Math.round(sPrice + rPrice + pPrice);

                            if (totalDiscount >= totalPrice) {
                                totalDiscount = null;
                            }

                            // Calculate total percent for display
                            let totalPercent = 0;
                            if (totalPrice > 0 && totalDiscount !== null) {
                                totalPercent = Math.round(((totalPrice - totalDiscount) / totalPrice) * 100);
                            }
                            
                            html += `
                                <tr>
                                    <td>${counter}</td>
                                    <td>${color}<input type="hidden" name="combinations[${uniqueId}][warna]" value="${color}"></td>
                                    <td>${processor.name}<input type="hidden" name="combinations[${uniqueId}][processor]" value="${processor.name}"></td>
                                    <td>${storage.size}<input type="hidden" name="combinations[${uniqueId}][penyimpanan]" value="${storage.size}"></td>
                                    <td>${ram.size}<input type="hidden" name="combinations[${uniqueId}][ram]" value="${ram.size}"></td>
                                    <td><input type="number" name="combinations[${uniqueId}][harga]" value="${totalPrice}" required></td>
                                    <td>
                                        <input type="number" name="combinations[${uniqueId}][harga_diskon]" value="${totalDiscount || ''}" placeholder="Total Diskon (Rp)">
                                        ${totalPercent > 0 ? `<small style="color: green; margin-left: 5px;">${totalPercent}% Off</small>` : ''}
                                    </td>
                                    <td><input type="number" name="combinations[${uniqueId}][jumlah_stok]" value="${existingStock}" placeholder="0"></td>
                                </tr>
                            `;
                            counter++;
                        });
                    });
                });
            });
            
            html += `</tbody></table>`;
            tbody.innerHTML = html;
            totalInfo.textContent = `Total Kombinasi: ${totalCount}`;
        }
        
        // Listen for input changes to update combinations
        document.addEventListener('input', function(e) {
            if (e.target.matches('input[name*="[nama]"], input[name*="[size]"], input[name*="[harga]"], input[name*="processor"]')) {
               if (!e.target.name.includes('jumlah_stok')) {
                   generateCombinations();
               }
            }
        });
    </script>
</body>
</html>