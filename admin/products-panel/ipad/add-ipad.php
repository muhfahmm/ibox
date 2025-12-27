<?php
session_start();
require_once '../../db.php';

// Jika belum login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../../auth/login.php?error=not_logged_in');
    exit();
}

$admin_username = $_SESSION['admin_username'] ?? 'Admin';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk iPad</title>
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
        
        .color-images-section {
            margin-top: 15px;
            padding: 15px;
            background: white;
            border-radius: 8px;
            border: 1px solid #eaeaea;
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
        
        .price-input-group {
            display: flex;
            gap: 10px;
        }
        
        .price-input-group input {
            flex: 1;
        }
        
        .alert-info {
            background-color: #e7f3ff;
            border-color: #b6d4fe;
            color: #084298;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-plus-circle me-2"></i> Tambah Produk iPad Baru</h2>
            </div>
            <div class="card-body">
                <form id="addIpadForm" action="api/api-add-ipad.php" method="POST" enctype="multipart/form-data">
                    
                    <!-- Informasi Produk -->
                    <div class="form-section">
                        <h4><i class="fas fa-info-circle me-2"></i> Informasi Produk</h4>
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_produk" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Deskripsi Produk</label>
                            <textarea class="form-control" name="deskripsi_produk" rows="4" placeholder="Masukkan deskripsi lengkap produk..."></textarea>
                        </div>
                    </div>
                    
                    <!-- Warna dengan Gambar -->
                    <div class="form-section">
                        <h4><i class="fas fa-palette me-2"></i> Warna Produk</h4>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Untuk setiap warna yang ditambahkan, silakan upload gambar thumbnail dan foto produk.
                        </div>
                        
                        <div id="colorsContainer">
                            <!-- Warna pertama -->
                            <div class="color-option" data-color-index="0">
                                <div class="row g-3 w-100">
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" name="warna[0][nama]" 
                                               placeholder="Nama Warna (Contoh: Silver)" required>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label">Thumbnail Warna <span class="text-danger">*</span></label>
                                                <div class="file-upload" onclick="document.getElementById('thumbnail-0').click()">
                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                    <p class="mb-1">Upload thumbnail</p>
                                                    <small class="text-muted">Max: 2MB</small>
                                                    <input type="file" id="thumbnail-0" 
                                                           name="warna[0][thumbnail]" 
                                                           accept="image/*" style="display: none;" required 
                                                           onchange="previewThumbnail(0, this)">
                                                </div>
                                                <div class="preview-container">
                                                    <div id="thumbnailPreview-0" class="preview-item" style="display: none;">
                                                        <img id="thumbnailImg-0" src="" alt="Thumbnail Preview">
                                                        <button type="button" class="remove-btn" onclick="removeThumbnail(0)">&times;</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Foto Produk (Bisa Lebih dari Satu)</label>
                                                <div class="file-upload" onclick="document.getElementById('productImages-0').click()">
                                                    <i class="fas fa-images"></i>
                                                    <p class="mb-1">Upload foto produk</p>
                                                    <small class="text-muted">Max: 2MB per gambar</small>
                                                    <input type="file" id="productImages-0" 
                                                           name="warna[0][product_images][]" 
                                                           accept="image/*" multiple style="display: none;" 
                                                           onchange="previewProductImages(0, this)">
                                                </div>
                                                <div class="preview-container" id="productImagesPreview-0"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn-danger-sm" onclick="removeColor(0)">
                                    &times;
                                </button>
                            </div>
                        </div>
                        
                        <button type="button" class="add-option-btn" onclick="addColor()">
                            <i class="fas fa-plus"></i> Tambah Warna Lain
                        </button>
                    </div>
                    
                    <!-- Penyimpanan dengan Harga -->
                    <div class="form-section">
                        <h4><i class="fas fa-hdd me-2"></i> Penyimpanan & Harga</h4>
                        
                        <div id="storagesContainer">
                            <!-- Penyimpanan pertama -->
                            <div class="storage-option" data-storage-index="0">
                                <div class="row g-3 w-100">
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" name="penyimpanan[0][size]" 
                                               placeholder="Ukuran (Contoh: 128GB)" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Harga Normal <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="penyimpanan[0][harga]" 
                                               placeholder="Harga" min="0" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Harga Diskon</label>
                                        <input type="number" class="form-control" name="penyimpanan[0][harga_diskon]" 
                                               placeholder="Diskon (opsional)" min="0">
                                    </div>
                                </div>
                                <button type="button" class="btn-danger-sm" onclick="removeStorage(0)">
                                    &times;
                                </button>
                            </div>
                        </div>
                        
                        <button type="button" class="add-option-btn" onclick="addStorage()">
                            <i class="fas fa-plus"></i> Tambah Penyimpanan Lain
                        </button>
                    </div>
                    
                    <!-- Konektivitas -->
                    <div class="form-section">
                        <h4><i class="fas fa-wifi me-2"></i> Konektivitas</h4>
                        
                        <div id="connectivitiesContainer">
                            <!-- Konektivitas pertama -->
                            <div class="connectivity-option" data-connectivity-index="0">
                                <input type="text" class="form-control" name="konektivitas[0]" 
                                       placeholder="Tipe Konektivitas (Contoh: Wi-Fi)" required>
                                <button type="button" class="btn-danger-sm" onclick="removeConnectivity(0)">
                                    &times;
                                </button>
                            </div>
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
                            Sistem akan membuat semua kombinasi dari Warna, Penyimpanan, dan Konektivitas. 
                            Silakan isi stok untuk setiap kombinasi.
                        </div>
                        
                        <div id="combinationsContainer">
                            <!-- Tabel kombinasi akan di-generate di sini -->
                        </div>
                        
                        <div class="total-combinations" id="totalCombinations">
                            Menghitung kombinasi...
                        </div>
                    </div>
                    
                    <!-- Tombol Aksi -->
                    <div class="d-flex justify-content-between mt-5 pt-4 border-top">
                        <a href="ipad.php" class="btn-back">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save me-2"></i> Simpan Produk iPad
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let colorCount = 1;
        let storageCount = 1;
        let connectivityCount = 1;
        
        // Warna
        function addColor() {
            const container = document.getElementById('colorsContainer');
            const newIndex = colorCount;
            
            const newColor = document.createElement('div');
            newColor.className = 'color-option';
            newColor.dataset.colorIndex = newIndex;
            newColor.innerHTML = `
                <div class="row g-3 w-100">
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="warna[${newIndex}][nama]" 
                               placeholder="Nama Warna (Contoh: Black)" required>
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Thumbnail Warna <span class="text-danger">*</span></label>
                                <div class="file-upload" onclick="document.getElementById('thumbnail-${newIndex}').click()">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p class="mb-1">Upload thumbnail</p>
                                    <small class="text-muted">Max: 2MB</small>
                                    <input type="file" id="thumbnail-${newIndex}" 
                                           name="warna[${newIndex}][thumbnail]" 
                                           accept="image/*" style="display: none;" required 
                                           onchange="previewThumbnail(${newIndex}, this)">
                                </div>
                                <div class="preview-container">
                                    <div id="thumbnailPreview-${newIndex}" class="preview-item" style="display: none;">
                                        <img id="thumbnailImg-${newIndex}" src="" alt="Thumbnail Preview">
                                        <button type="button" class="remove-btn" onclick="removeThumbnail(${newIndex})">&times;</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Foto Produk (Bisa Lebih dari Satu)</label>
                                <div class="file-upload" onclick="document.getElementById('productImages-${newIndex}').click()">
                                    <i class="fas fa-images"></i>
                                    <p class="mb-1">Upload foto produk</p>
                                    <small class="text-muted">Max: 2MB per gambar</small>
                                    <input type="file" id="productImages-${newIndex}" 
                                           name="warna[${newIndex}][product_images][]" 
                                           accept="image/*" multiple style="display: none;" 
                                           onchange="previewProductImages(${newIndex}, this)">
                                </div>
                                <div class="preview-container" id="productImagesPreview-${newIndex}"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-danger-sm" onclick="removeColor(${newIndex})">
                    &times;
                </button>
            `;
            
            container.appendChild(newColor);
            colorCount++;
            generateCombinations();
        }
        
        function removeColor(index) {
            if (colorCount <= 1) {
                alert('Minimal harus ada satu warna');
                return;
            }
            
            const colorElement = document.querySelector(`.color-option[data-color-index="${index}"]`);
            if (colorElement) {
                colorElement.remove();
                colorCount--;
                reindexColors();
                generateCombinations();
            }
        }
        
        function reindexColors() {
            const colorElements = document.querySelectorAll('.color-option');
            colorElements.forEach((element, newIndex) => {
                const oldIndex = element.dataset.colorIndex;
                element.dataset.colorIndex = newIndex;
                
                // Update semua input dalam element
                const inputs = element.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    if (input.name) {
                        input.name = input.name.replace(
                            /warna\[\d+\]/g,
                            `warna[${newIndex}]`
                        );
                    }
                });
                
                // Update ID dan event handlers
                const fileInputs = element.querySelectorAll('input[type="file"]');
                fileInputs.forEach(input => {
                    if (input.id.includes('thumbnail')) {
                        input.id = `thumbnail-${newIndex}`;
                        input.setAttribute('onchange', `previewThumbnail(${newIndex}, this)`);
                        input.parentElement.setAttribute('onclick', `document.getElementById('thumbnail-${newIndex}').click()`);
                    }
                    if (input.id.includes('productImages')) {
                        input.id = `productImages-${newIndex}`;
                        input.setAttribute('onchange', `previewProductImages(${newIndex}, this)`);
                        input.parentElement.setAttribute('onclick', `document.getElementById('productImages-${newIndex}').click()`);
                    }
                });
                
                // Update preview containers
                element.querySelectorAll('[id*="Preview"]').forEach(el => {
                    el.id = el.id.replace(/\d+/, newIndex);
                });
                
                // Update remove button
                const removeBtn = element.querySelector('.btn-danger-sm');
                if (removeBtn) {
                    removeBtn.setAttribute('onclick', `removeColor(${newIndex})`);
                }
            });
        }
        
        // Penyimpanan
        function addStorage() {
            const container = document.getElementById('storagesContainer');
            const newIndex = storageCount;
            
            const newStorage = document.createElement('div');
            newStorage.className = 'storage-option';
            newStorage.dataset.storageIndex = newIndex;
            newStorage.innerHTML = `
                <div class="row g-3 w-100">
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="penyimpanan[${newIndex}][size]" 
                               placeholder="Ukuran (Contoh: 256GB)" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Harga Normal <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="penyimpanan[${newIndex}][harga]" 
                               placeholder="Harga" min="0" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Harga Diskon</label>
                        <input type="number" class="form-control" name="penyimpanan[${newIndex}][harga_diskon]" 
                               placeholder="Diskon (opsional)" min="0">
                    </div>
                </div>
                <button type="button" class="btn-danger-sm" onclick="removeStorage(${newIndex})">
                    &times;
                </button>
            `;
            
            container.appendChild(newStorage);
            storageCount++;
            generateCombinations();
        }
        
        function removeStorage(index) {
            if (storageCount <= 1) {
                alert('Minimal harus ada satu penyimpanan');
                return;
            }
            
            const storageElement = document.querySelector(`.storage-option[data-storage-index="${index}"]`);
            if (storageElement) {
                storageElement.remove();
                storageCount--;
                reindexStorages();
                generateCombinations();
            }
        }
        
        function reindexStorages() {
            const storageElements = document.querySelectorAll('.storage-option');
            storageElements.forEach((element, newIndex) => {
                const oldIndex = element.dataset.storageIndex;
                element.dataset.storageIndex = newIndex;
                
                // Update semua input dalam element
                const inputs = element.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    if (input.name) {
                        input.name = input.name.replace(
                            /penyimpanan\[\d+\]/g,
                            `penyimpanan[${newIndex}]`
                        );
                    }
                });
                
                // Update remove button
                const removeBtn = element.querySelector('.btn-danger-sm');
                if (removeBtn) {
                    removeBtn.setAttribute('onclick', `removeStorage(${newIndex})`);
                }
            });
        }
        
        // Konektivitas
        function addConnectivity() {
            const container = document.getElementById('connectivitiesContainer');
            const newIndex = connectivityCount;
            
            const newConnectivity = document.createElement('div');
            newConnectivity.className = 'connectivity-option';
            newConnectivity.dataset.connectivityIndex = newIndex;
            newConnectivity.innerHTML = `
                <input type="text" class="form-control" name="konektivitas[${newIndex}]" 
                       placeholder="Tipe Konektivitas (Contoh: Wi-Fi + Cellular)" required>
                <button type="button" class="btn-danger-sm" onclick="removeConnectivity(${newIndex})">
                    &times;
                </button>
            `;
            
            container.appendChild(newConnectivity);
            connectivityCount++;
            generateCombinations();
        }
        
        function removeConnectivity(index) {
            if (connectivityCount <= 1) {
                alert('Minimal harus ada satu konektivitas');
                return;
            }
            
            const connectivityElement = document.querySelector(`.connectivity-option[data-connectivity-index="${index}"]`);
            if (connectivityElement) {
                connectivityElement.remove();
                connectivityCount--;
                reindexConnectivities();
                generateCombinations();
            }
        }
        
        function reindexConnectivities() {
            const connectivityElements = document.querySelectorAll('.connectivity-option');
            connectivityElements.forEach((element, newIndex) => {
                const oldIndex = element.dataset.connectivityIndex;
                element.dataset.connectivityIndex = newIndex;
                
                // Update input name
                const input = element.querySelector('input');
                if (input) {
                    input.name = `konektivitas[${newIndex}]`;
                }
                
                // Update remove button
                const removeBtn = element.querySelector('.btn-danger-sm');
                if (removeBtn) {
                    removeBtn.setAttribute('onclick', `removeConnectivity(${newIndex})`);
                }
            });
        }
        
        // Generate Combinations
        function generateCombinations() {
    const container = document.getElementById('combinationsContainer');
    
    // Collect data
    const colors = [];
    document.querySelectorAll('.color-option input[name$="[nama]"]').forEach(input => {
        if (input.value.trim()) {
            colors.push(input.value.trim());
        }
    });
    
    const storages = [];
    document.querySelectorAll('.storage-option').forEach(option => {
        const sizeInput = option.querySelector('input[name$="[size]"]');
        const hargaInput = option.querySelector('input[name$="[harga]"]');
        const diskonInput = option.querySelector('input[name$="[harga_diskon]"]');
        
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
        if (input.value.trim()) {
            connectivities.push(input.value.trim());
        }
    });
    
    console.log("Colors:", colors);
    console.log("Storages:", storages);
    console.log("Connectivities:", connectivities);
    
    // Calculate total combinations
    const totalCombinations = colors.length * storages.length * connectivities.length;
    document.getElementById('totalCombinations').innerHTML = 
        `Total Kombinasi: <strong>${totalCombinations}</strong> (${colors.length} warna × ${storages.length} penyimpanan × ${connectivities.length} konektivitas)`;
    
    // Generate table
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
                        <th>Harga Diskon</th>
                        <th>Jumlah Stok</th>
                    </tr>
                </thead>
                <tbody>
        `;
        
        let counter = 1;
        colors.forEach((color, colorIndex) => {
            storages.forEach((storage, storageIndex) => {
                connectivities.forEach((connectivity, connectivityIndex) => {
                    const combinationId = `${colorIndex}-${storageIndex}-${connectivityIndex}`;
                    
                    tableHTML += `
                        <tr>
                            <td>${counter}</td>
                            <td><strong>${color}</strong></td>
                            <td><strong>${storage.size}</strong></td>
                            <td>${connectivity}</td>
                            <td>
                                <input type="hidden" name="combinations[${combinationId}][warna]" value="${color}">
                                <input type="hidden" name="combinations[${combinationId}][penyimpanan]" value="${storage.size}">
                                <input type="hidden" name="combinations[${combinationId}][konektivitas]" value="${connectivity}">
                                <input type="hidden" name="combinations[${combinationId}][harga]" value="${storage.harga}">
                                <input type="hidden" name="combinations[${combinationId}][harga_diskon]" value="${storage.harga_diskon || ''}">
                                <div class="text-success fw-bold">Rp ${parseInt(storage.harga).toLocaleString('id-ID')}</div>
                            </td>
                            <td>
                                ${storage.harga_diskon ? 
                                    `<div class="text-danger fw-bold">Rp ${parseInt(storage.harga_diskon).toLocaleString('id-ID')}</div>` : 
                                    '<span class="text-muted">-</span>'}
                            </td>
                            <td>
                                <input type="number" class="form-control" 
                                       name="combinations[${combinationId}][jumlah_stok]" 
                                       value="0" min="0" required 
                                       style="width: 100px;">
                            </td>
                        </tr>
                    `;
                    counter++;
                });
            });
        });
        
        tableHTML += `
                </tbody>
            </table>
        `;
        
        container.innerHTML = tableHTML;
    } else {
        container.innerHTML = '<p class="text-muted">Tambahkan minimal satu warna, penyimpanan, dan konektivitas untuk melihat kombinasi.</p>';
    }
}
        
        // Preview functions
        function previewThumbnail(index, input) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById(`thumbnailImg-${index}`);
                    const preview = document.getElementById(`thumbnailPreview-${index}`);
                    if (img) img.src = e.target.result;
                    if (preview) preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        }
        
        function removeThumbnail(index) {
            const input = document.getElementById(`thumbnail-${index}`);
            const preview = document.getElementById(`thumbnailPreview-${index}`);
            if (input) input.value = '';
            if (preview) preview.style.display = 'none';
        }
        
        function previewProductImages(index, input) {
            const files = input.files;
            const previewContainer = document.getElementById(`productImagesPreview-${index}`);
            
            // Clear existing previews
            previewContainer.innerHTML = '';
            
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const previewItem = document.createElement('div');
                    previewItem.className = 'preview-item';
                    previewItem.innerHTML = `
                        <img src="${e.target.result}" alt="Product Image">
                        <button type="button" class="remove-btn" onclick="this.parentElement.remove()">&times;</button>
                    `;
                    previewContainer.appendChild(previewItem);
                };
                
                reader.readAsDataURL(file);
            }
        }
        
        // Form validation and submission
        document.getElementById('addIpadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate product name
            const productName = document.querySelector('input[name="nama_produk"]').value;
            if (!productName.trim()) {
                alert('Nama produk harus diisi');
                return;
            }
            
            // Validate colors
            const colorInputs = document.querySelectorAll('.color-option input[name$="[nama]"]');
            const validColors = Array.from(colorInputs).filter(input => input.value.trim()).length;
            if (validColors === 0) {
                alert('Minimal satu warna harus diisi');
                return;
            }
            
            // Validate color thumbnails
            let missingThumbnail = false;
            document.querySelectorAll('.color-option').forEach((option, index) => {
                const thumbnailInput = document.getElementById(`thumbnail-${index}`);
                if (!thumbnailInput || !thumbnailInput.files[0]) {
                    missingThumbnail = true;
                }
            });
            
            if (missingThumbnail) {
                alert('Semua warna harus memiliki thumbnail');
                return;
            }
            
            // Validate storages
            const storageInputs = document.querySelectorAll('.storage-option input[name$="[size]"]');
            const validStorages = Array.from(storageInputs).filter(input => input.value.trim()).length;
            if (validStorages === 0) {
                alert('Minimal satu penyimpanan harus diisi');
                return;
            }
            
            // Validate storage prices
            let invalidPrice = false;
            document.querySelectorAll('.storage-option').forEach(option => {
                const hargaInput = option.querySelector('input[name$="[harga]"]');
                const diskonInput = option.querySelector('input[name$="[harga_diskon]"]');
                
                if (hargaInput && parseFloat(hargaInput.value) <= 0) {
                    invalidPrice = true;
                }
                
                if (diskonInput && diskonInput.value) {
                    const harga = parseFloat(hargaInput.value);
                    const diskon = parseFloat(diskonInput.value);
                    if (diskon >= harga) {
                        alert('Harga diskon harus lebih rendah dari harga normal');
                        invalidPrice = true;
                    }
                }
            });
            
            if (invalidPrice) {
                alert('Periksa harga dan diskon pada penyimpanan');
                return;
            }
            
            // Validate connectivities
            const connectivityInputs = document.querySelectorAll('.connectivity-option input');
            const validConnectivities = Array.from(connectivityInputs).filter(input => input.value.trim()).length;
            if (validConnectivities === 0) {
                alert('Minimal satu konektivitas harus diisi');
                return;
            }
            
            // Validate combinations
            const stockInputs = document.querySelectorAll('input[name$="[jumlah_stok]"]');
            let invalidStock = false;
            stockInputs.forEach(input => {
                if (parseInt(input.value) < 0) {
                    invalidStock = true;
                }
            });
            
            if (invalidStock) {
                alert('Stok tidak boleh kurang dari 0');
                return;
            }
            
            // Show loading
            const submitBtn = document.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Menyimpan...';
            submitBtn.disabled = true;
            
            // Submit form
            const formData = new FormData(this);
            
            fetch('api/api-add-ipad.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Produk berhasil ditambahkan!');
                    window.location.href = 'ipad.php';
                } else {
                    alert('Error: ' + data.message);
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan data');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
        
        // Initialize combinations on page load
        document.addEventListener('DOMContentLoaded', function() {
            generateCombinations();
            
            // Add event listeners for real-time validation
            document.addEventListener('input', function(e) {
                if (e.target.name && e.target.name.includes('[harga]')) {
                    generateCombinations();
                }
            });
        });
        // Debug function
function debugFormData() {
    const formData = new FormData(document.getElementById('addIpadForm'));
    console.log("=== DEBUG FORM DATA ===");
    
    // Log warna
    const warnaInputs = document.querySelectorAll('input[name^="warna["]');
    console.log("Warna inputs found:", warnaInputs.length);
    
    // Log penyimpanan
    const storageInputs = document.querySelectorAll('input[name^="penyimpanan["]');
    console.log("Penyimpanan inputs found:", storageInputs.length);
    
    // Log konektivitas
    const connectivityInputs = document.querySelectorAll('input[name^="konektivitas["]');
    console.log("Konektivitas inputs found:", connectivityInputs.length);
    
    // Log kombinasi
    const combinationInputs = document.querySelectorAll('input[name^="combinations["]');
    console.log("Combination inputs found:", combinationInputs.length);
    
    // Show form data
    for (let pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }
    
    return false;
}
    </script>
</body>
</html>