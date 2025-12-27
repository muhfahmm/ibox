<?php
session_start();
require_once '../../db.php';

// Jika belum login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../../auth/login.php?error=not_logged_in');
    exit();
}

$admin_username = $_SESSION['admin_username'] ?? 'Admin';

// Get ID
if (!isset($_GET['id'])) {
    header('Location: ipad.php');
    exit();
}

$product_id = mysqli_real_escape_string($db, $_GET['id']);
$product = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM admin_produk_ipad WHERE id = '$product_id'"));

if (!$product) {
    header('Location: ipad.php?error=not_found');
    exit();
}

// Fetch Existing Data
$images_query = mysqli_query($db, "SELECT * FROM admin_produk_ipad_gambar WHERE produk_id='$product_id'");
$colors_data = [];
while($row = mysqli_fetch_assoc($images_query)) {
    $colors_data[] = [
        'nama' => $row['warna'],
        'thumbnail' => $row['foto_thumbnail'],
        'images' => json_decode($row['foto_produk'], true) ?? []
    ];
}

$combinations_query = mysqli_query($db, "SELECT * FROM admin_produk_ipad_kombinasi WHERE produk_id='$product_id'");
$combinations = mysqli_fetch_all($combinations_query, MYSQLI_ASSOC);

$storages_map = [];
$connectivities_map = [];
$stocks_map = [];

foreach($combinations as $c) {
    if (!isset($storages_map[$c['penyimpanan']])) {
        $storages_map[$c['penyimpanan']] = [
            'size' => $c['penyimpanan'],
            'harga' => $c['harga'],
            'harga_diskon' => $c['harga_diskon']
        ];
    }
    $connectivities_map[$c['konektivitas']] = true;
    
    // Create a key for stock lookup
    $key = $c['warna'] . '|' . $c['penyimpanan'] . '|' . $c['konektivitas'];
    $stocks_map[$key] = $c['jumlah_stok'];
}

$initialData = [
    'colors' => $colors_data,
    'storages' => array_values($storages_map),
    'connectivities' => array_keys($connectivities_map),
    'stocks' => $stocks_map
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk iPad - <?php echo htmlspecialchars($product['nama_produk']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
        }
        
        .card-header {
            background: linear-gradient(135deg, #4a6cf7 0%, #6a11cb 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 25px;
        }
        
        .card-header h2 {
            margin: 0;
            font-weight: 600;
        }
        
        .card-body {
            padding: 30px;
        }
        
        .form-label {
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px 15px;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #4a6cf7;
            box-shadow: 0 0 0 0.2rem rgba(74, 108, 247, 0.25);
        }
        
        .file-upload {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background-color: #fafafa;
        }
        
        .file-upload:hover {
            border-color: #4a6cf7;
            background-color: #f0f4ff;
        }
        
        .file-upload i {
            font-size: 40px;
            color: #4a6cf7;
            margin-bottom: 10px;
        }
        
        .preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }
        
        .preview-item {
            position: relative;
            width: 100px;
            height: 100px;
        }
        
        .preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #eaeaea;
        }
        
        .remove-btn {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #4a6cf7 0%, #6a11cb 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(74, 108, 247, 0.3);
        }
        
        .btn-back {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-back:hover {
            background-color: #5a6268;
            color: white;
        }
        
        .form-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
        }
        
        .form-section h4 {
            color: #4a6cf7;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eaeaea;
        }
        
        .color-option, .storage-option, .connectivity-option {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            padding: 10px;
            background: white;
            border-radius: 6px;
            border: 1px solid #eaeaea;
        }
        
        .add-option-btn {
            background-color: #17a2b8;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 6px;
            margin-top: 10px;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .add-option-btn:hover {
            background-color: #138496;
        }
        
        .btn-danger-sm {
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        
        .btn-danger-sm:hover {
            background-color: #c82333;
        }
        
        .combination-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .combination-table th {
            background: #4a6cf7;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        
        .combination-table td {
            padding: 10px;
            border-bottom: 1px solid #eaeaea;
            vertical-align: middle;
        }
        
        .combination-table tr:hover {
            background-color: #f8f9fa;
        }
        
        .combination-table input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .total-combinations {
            background: #e3f2fd;
            padding: 10px 15px;
            border-radius: 6px;
            margin-top: 15px;
            font-weight: 500;
        }
        
        .alert-info {
            background-color: #e7f3ff;
            border-color: #b6d4fe;
            color: #084298;
        }

        /* Overlay loader for async actions */
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
    </style>
</head>
<body>
    <div id="loadingOverlay">
        <div class="spinner-border text-primary" role="status"></div>
        <div class="mt-2 fw-bold">Menyimpan perubahan...</div>
    </div>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-edit me-2"></i> Edit Produk iPad</h2>
            </div>
            <div class="card-body">
                <form id="editIpadForm" action="api/api-edit-ipad.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                    
                    <!-- Informasi Produk -->
                    <div class="form-section">
                        <h4><i class="fas fa-info-circle me-2"></i> Informasi Produk</h4>
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_produk" value="<?php echo htmlspecialchars($product['nama_produk']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Deskripsi Produk</label>
                            <textarea class="form-control" name="deskripsi_produk" rows="4" placeholder="Masukkan deskripsi lengkap produk..."><?php echo htmlspecialchars($product['deskripsi_produk']); ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Warna dengan Gambar -->
                    <div class="form-section">
                        <h4><i class="fas fa-palette me-2"></i> Warna Produk</h4>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Anda bisa membiarkan field upload kosong jika tidak ingin mengubah gambar.
                        </div>
                        
                        <div id="colorsContainer">
                            <!-- Warna akan di-generate oleh Javascript -->
                        </div>
                        
                        <button type="button" class="add-option-btn" onclick="addColor()">
                            <i class="fas fa-plus"></i> Tambah Warna Lain
                        </button>
                    </div>
                    
                    <!-- Penyimpanan dengan Harga -->
                    <div class="form-section">
                        <h4><i class="fas fa-hdd me-2"></i> Penyimpanan & Harga</h4>
                        
                        <div id="storagesContainer">
                            <!-- Penyimpanan akan di-generate oleh Javascript -->
                        </div>
                        
                        <button type="button" class="add-option-btn" onclick="addStorage()">
                            <i class="fas fa-plus"></i> Tambah Penyimpanan Lain
                        </button>
                    </div>
                    
                    <!-- Konektivitas -->
                    <div class="form-section">
                        <h4><i class="fas fa-wifi me-2"></i> Konektivitas</h4>
                        
                        <div id="connectivitiesContainer">
                            <!-- Konektivitas akan di-generate oleh Javascript -->
                        </div>
                        
                        <button type="button" class="add-option-btn" onclick="addConnectivity()">
                            <i class="fas fa-plus"></i> Tambah Konektivitas Lain
                        </button>
                    </div>
                    
                    <!-- Tabel Kombinasi & Stok -->
                    <div class="form-section">
                        <h4><i class="fas fa-table me-2"></i> Kombinasi & Stok</h4>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Stok akan di-update otomatis dari data yang ada. Ubah jumlah stok jika ingin melakukan update.
                        </div>
                        
                        <div id="combinationsContainer">
                            <!-- Tabel kombinasi akan di-generate di sini -->
                        </div>
                        
                        <div class="total-combinations" id="totalCombinations">
                            Loading data...
                        </div>
                    </div>
                    
                    <!-- Tombol Aksi -->
                    <div class="d-flex justify-content-between mt-5 pt-4 border-top">
                        <a href="ipad.php" class="btn-back">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save me-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Data dari PHP
        const initialData = <?php echo json_encode($initialData); ?>;
        const uploadPath = '../../uploads/';
        
        let colorCount = 0;
        let storageCount = 0;
        let connectivityCount = 0;
        
        // Initialize Form
        window.addEventListener('DOMContentLoaded', () => {
            // Colors
            if (initialData.colors && initialData.colors.length > 0) {
                initialData.colors.forEach(color => addColor(color));
            } else {
                addColor(); // Default empty
            }
            
            // Storages
            if (initialData.storages && initialData.storages.length > 0) {
                initialData.storages.forEach(storage => addStorage(storage));
            } else {
                addStorage();
            }
            
            // Connectivities
            if (initialData.connectivities && initialData.connectivities.length > 0) {
                initialData.connectivities.forEach(conn => addConnectivity(conn));
            } else {
                addConnectivity();
            }
            
            // Generate Combinations with slight delay to ensure DOM is ready
            setTimeout(() => {
                generateCombinations();
            }, 100);
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
                            <!-- Disembunyikan karena logic update gambar sedikit beda -->
                        </div>
                        <input type="hidden" name="warna[${newIndex}][existing_thumbnail]" value="${data.thumbnail}">
                    `;
                }
                
                if (data.images && data.images.length > 0) {
                    productImagesPreview = `<div class="d-flex flex-wrap gap-2 mt-2">`;
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
                <div class="row g-3 w-100">
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="warna[${newIndex}][nama]" 
                               placeholder="Nama Warna (Contoh: Silver)" required value="${data ? data.nama : ''}">
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Thumbnail Warna ${!isExisting ? '<span class="text-danger">*</span>' : ''}</label>
                                <div class="file-upload p-2" onclick="document.getElementById('thumbnail-${newIndex}').click()">
                                    <i class="fas fa-cloud-upload-alt fs-4"></i>
                                    <p class="mb-0 small">Ubah thumbnail</p>
                                    <input type="file" id="thumbnail-${newIndex}" 
                                           name="warna[${newIndex}][thumbnail]" 
                                           accept="image/*" style="display: none;" ${!isExisting ? 'required' : ''} 
                                           onchange="previewThumbnail(${newIndex}, this)">
                                </div>
                                <div class="preview-container">
                                    ${thumbnailPreview}
                                    <div id="thumbnailNewPreview-${newIndex}" class="preview-item" style="display: none;">
                                        <img id="thumbnailNewImg-${newIndex}" src="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Foto Produk</label>
                                <div class="file-upload p-2" onclick="document.getElementById('productImages-${newIndex}').click()">
                                    <i class="fas fa-images fs-4"></i>
                                    <p class="mb-0 small">Tambah foto</p>
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
                <button type="button" class="btn-danger-sm ms-2" onclick="removeColor(${newIndex})">
                    &times;
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
                // We re-generate combinations, but we don't strictly modify colorCount to avoid index collision
                // actually better to not reuse indices or rely on accurate count if we don't reindex
                generateCombinations();
            }
        }
        
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
            // Don't clear existing previews, append new ones? 
            // Or just show new ones in a separate wrapper? 
            // Let's simplified: add new ones
            
            if (input.files) {
                // remove only prev newly added previews
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

        // Penyimpanan
        function addStorage(data = null) {
            const container = document.getElementById('storagesContainer');
            const newIndex = storageCount;
            
            const newStorage = document.createElement('div');
            newStorage.className = 'storage-option';
            newStorage.dataset.storageIndex = newIndex;
            
            newStorage.innerHTML = `
                <div class="row g-3 w-100">
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="penyimpanan[${newIndex}][size]" 
                               placeholder="Ukuran (Contoh: 128GB)" required value="${data ? data.size : ''}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Harga Normal <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="penyimpanan[${newIndex}][harga]" 
                               placeholder="Harga" min="0" required value="${data ? data.harga : ''}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Harga Diskon</label>
                        <input type="number" class="form-control" name="penyimpanan[${newIndex}][harga_diskon]" 
                               placeholder="Diskon (opsional)" min="0" value="${data && data.harga_diskon ? data.harga_diskon : ''}">
                    </div>
                </div>
                <button type="button" class="btn-danger-sm ms-2" onclick="removeStorage(${newIndex})">
                    &times;
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
        
        // Konektivitas
        function addConnectivity(data = null) {
            const container = document.getElementById('connectivitiesContainer');
            const newIndex = connectivityCount;
            
            const newConnectivity = document.createElement('div');
            newConnectivity.className = 'connectivity-option';
            newConnectivity.dataset.connectivityIndex = newIndex;
            newConnectivity.innerHTML = `
                <input type="text" class="form-control" name="konektivitas[${newIndex}]" 
                       placeholder="Tipe Konektivitas (Contoh: Wi-Fi)" required value="${data ? data : ''}">
                <button type="button" class="btn-danger-sm" onclick="removeConnectivity(${newIndex})">
                    &times;
                </button>
            `;
            
            container.appendChild(newConnectivity);
            connectivityCount++;
            generateCombinations();
        }
        
        function removeConnectivity(index) {
            const connElements = document.querySelectorAll('.connectivity-option');
            if (connElements.length <= 1) {
                alert('Minimal harus ada satu konektivitas');
                return;
            }
            
            const element = document.querySelector(`.connectivity-option[data-connectivity-index="${index}"]`);
            if (element) {
                element.remove();
                generateCombinations();
            }
        }
        
        // Generate Combinations
        function generateCombinations() {
            const container = document.getElementById('combinationsContainer');
            
            // Collect data from DOM inputs
            const colors = [];
            document.querySelectorAll('.color-option input[name*="[nama]"]').forEach(input => {
                if (input.value.trim()) colors.push(input.value.trim());
            });
            
            const storages = [];
            document.querySelectorAll('.storage-option').forEach(option => {
                const sizeInput = option.querySelector('input[name*="[size]"]');
                const hargaInput = option.querySelector('input[name*="[harga]"]');
                const diskonInput = option.querySelector('input[name*="[harga_diskon]"]');
                
                if (sizeInput && hargaInput && sizeInput.value.trim() && hargaInput.value) {
                    storages.push({
                        size: sizeInput.value.trim(),
                        harga: hargaInput.value,
                        harga_diskon: diskonInput ? diskonInput.value : ''
                    });
                }
            });
            
            const connectivities = [];
            document.querySelectorAll('.connectivity-option input').forEach(input => {
                const val = input.value.trim();
                // Avoid capturing hidden inputs if name is just 'konektivitas[]' etc (regex match above was specific enough)
                if (val && input.name.includes('konektivitas[')) connectivities.push(val);
            });
            
            // Generate table
            const totalCombinations = colors.length * storages.length * connectivities.length;
            document.getElementById('totalCombinations').innerHTML = 
                `Total Kombinasi: <strong>${totalCombinations}</strong> (${colors.length} warna × ${storages.length} penyimpanan × ${connectivities.length} konektivitas)`;
            
            if (totalCombinations > 0) {
                let tableHTML = `
                    <table class="combination-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Warna</th>
                                <th>Penyimpanan</th>
                                <th>Konektivitas</th>
                                <th>Harga</th>
                                <th>Jumlah Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                
                let counter = 1;
                colors.forEach((color, cIdx) => {
                    storages.forEach((storage, sIdx) => {
                        connectivities.forEach((connectivity, kIdx) => {
                            const uniqueId = `${cIdx}_${sIdx}_${kIdx}`; // Just for DOM uniqueness
                            
                            // Try to find existing stock
                            const stockKey = `${color}|${storage.size}|${connectivity}`;
                            const existingStock = initialData.stocks[stockKey] !== undefined ? initialData.stocks[stockKey] : 0;
                            
                            tableHTML += `
                                <tr>
                                    <td>${counter}</td>
                                    <td>${color}</td>
                                    <td>${storage.size}</td>
                                    <td>${connectivity}</td>
                                    <td>
                                        <div class="fw-bold text-success">Rp ${parseInt(storage.harga).toLocaleString('id-ID')}</div>
                                        ${storage.harga_diskon ? `<small class="text-danger text-decoration-line-through">Diskon: Rp ${parseInt(storage.harga_diskon).toLocaleString('id-ID')}</small>` : ''}
                                        
                                        <!-- Hidden Inputs for Combination Data -->
                                        <input type="hidden" name="combinations[${uniqueId}][warna]" value="${color}">
                                        <input type="hidden" name="combinations[${uniqueId}][penyimpanan]" value="${storage.size}">
                                        <input type="hidden" name="combinations[${uniqueId}][konektivitas]" value="${connectivity}">
                                        <input type="hidden" name="combinations[${uniqueId}][harga]" value="${storage.harga}">
                                        <input type="hidden" name="combinations[${uniqueId}][harga_diskon]" value="${storage.harga_diskon || ''}">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" style="width: 100px;" 
                                               name="combinations[${uniqueId}][jumlah_stok]" 
                                               value="${existingStock}" min="0" required>
                                    </td>
                                </tr>
                            `;
                            counter++;
                        });
                    });
                });
                
                tableHTML += `</tbody></table>`;
                container.innerHTML = tableHTML;
            } else {
                container.innerHTML = '<div class="text-center p-4 text-muted">Lengkapi data warna, penyimpanan, dan konektivitas untuk melihat tabel kombinasi</div>';
            }
        }

        // Form Submission
        document.getElementById('editIpadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Basic Validation
            const productName = document.querySelector('input[name="nama_produk"]').value;
            if (!productName.trim()) {
                alert('Nama produk harus diisi');
                return;
            }

            // Show Loading
            document.getElementById('loadingOverlay').style.display = 'flex';
            const submitBtn = document.querySelector('.btn-submit');
            submitBtn.disabled = true;

            const formData = new FormData(this);

            fetch('api/api-edit-ipad.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Produk berhasil diperbarui!');
                    window.location.href = 'view-ipad.php?id=<?php echo $product_id; ?>';
                } else {
                    alert('Error: ' + data.message);
                    document.getElementById('loadingOverlay').style.display = 'none';
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan network/server');
                document.getElementById('loadingOverlay').style.display = 'none';
                submitBtn.disabled = false;
            });
        });
        
        // Listen for price changes to update combinations table prices if needed?
        // Actually generateCombinations pulls directly from inputs so we just need to trigger it
        document.addEventListener('input', function(e) {
            if (e.target.matches('input[name*="[nama]"], input[name*="[size]"], input[name*="[harga]"], input[name*="konektivitas"]')) {
               // Debounce or just run? Run is fine.
               // Check if the input is NOT the stock input (which is inside the table)
               if (!e.target.name.includes('jumlah_stok')) {
                   generateCombinations();
               }
            }
        });
    </script>
</body>
</html>