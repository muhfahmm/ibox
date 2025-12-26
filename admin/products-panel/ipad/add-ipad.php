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
        
        .btn-add-field {
            background-color: #17a2b8;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 6px;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-top: 10px;
        }
        
        .btn-add-field:hover {
            background-color: #138496;
        }
        
        .form-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            position: relative;
        }
        
        .form-section h4 {
            color: #4a6cf7;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eaeaea;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .variant-group {
            margin-bottom: 15px;
            padding: 15px;
            background-color: white;
            border-radius: 8px;
            border: 1px solid #eaeaea;
        }
        
        .remove-variant {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 14px;
        }
        
        .alert-info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .input-group-dynamic {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .input-group-dynamic .form-control {
            flex: 1;
        }
        
        .input-group-dynamic .btn-remove-item {
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 6px;
            width: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .variant-counter {
            background-color: #4a6cf7;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            margin-left: 10px;
        }
        
        .variant-list {
            background-color: white;
            border: 1px solid #eaeaea;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            max-height: 200px;
            overflow-y: auto;
        }
        
        .variant-list-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .variant-list-item:last-child {
            border-bottom: none;
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
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-section">
                                <h4>Informasi Produk Dasar</h4>
                                
                                <div class="mb-3">
                                    <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nama_produk" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Deskripsi Produk</label>
                                    <textarea class="form-control" name="deskripsi_produk" rows="4" placeholder="Masukkan deskripsi lengkap produk..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Container untuk varian produk -->
                    <div id="variantContainer">
                        <div class="variant-item">
                            <div class="form-section">
                                <h4>Varian Produk #1</h4>
                                
                                <!-- Warna -->
                                <div class="mb-4">
                                    <label class="form-label">Warna <span class="text-danger">*</span></label>
                                    <div id="warna-container-1">
                                        <div class="input-group-dynamic">
                                            <input type="text" class="form-control" name="variant[1][warna][]" placeholder="Masukkan warna" required>
                                            <button type="button" class="btn-remove-item" onclick="removeVariantItem(this, 'warna')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-add-field" onclick="addVariantItem(1, 'warna')">
                                        <i class="fas fa-plus"></i> Tambah Warna
                                    </button>
                                </div>
                                
                                <!-- Penyimpanan -->
                                <div class="mb-4">
                                    <label class="form-label">Penyimpanan <span class="text-danger">*</span></label>
                                    <div id="penyimpanan-container-1">
                                        <div class="input-group-dynamic">
                                            <input type="text" class="form-control" name="variant[1][penyimpanan][]" placeholder="Masukkan penyimpanan" required>
                                            <button type="button" class="btn-remove-item" onclick="removeVariantItem(this, 'penyimpanan')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-add-field" onclick="addVariantItem(1, 'penyimpanan')">
                                        <i class="fas fa-plus"></i> Tambah Penyimpanan
                                    </button>
                                </div>
                                
                                <!-- Konektivitas -->
                                <div class="mb-4">
                                    <label class="form-label">Konektivitas <span class="text-danger">*</span></label>
                                    <div id="konektivitas-container-1">
                                        <div class="input-group-dynamic">
                                            <input type="text" class="form-control" name="variant[1][konektivitas][]" placeholder="Masukkan konektivitas" required>
                                            <button type="button" class="btn-remove-item" onclick="removeVariantItem(this, 'konektivitas')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-add-field" onclick="addVariantItem(1, 'konektivitas')">
                                        <i class="fas fa-plus"></i> Tambah Konektivitas
                                    </button>
                                </div>
                                
                                <!-- Harga & Stok -->
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Harga Normal <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="variant[1][harga]" min="0" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Harga Diskon</label>
                                        <input type="number" class="form-control" name="variant[1][harga_diskon]" min="0" placeholder="Kosongkan jika tidak ada diskon">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Status Stok</label>
                                        <select class="form-select" name="variant[1][status_stok]">
                                            <option value="tersedia">Tersedia</option>
                                            <option value="habis">Habis</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Gambar Produk -->
                                <div class="mb-4">
                                    <label class="form-label">Thumbnail (Satu Gambar) <span class="text-danger">*</span></label>
                                    <div class="file-upload" onclick="document.getElementById('thumbnail-1').click()">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <p class="mb-1">Klik untuk upload thumbnail</p>
                                        <small class="text-muted">Format: JPG, PNG, GIF. Max: 2MB</small>
                                        <input type="file" id="thumbnail-1" name="variant[1][thumbnail]" accept="image/*" style="display: none;" required>
                                    </div>
                                    <div class="preview-container">
                                        <div id="thumbnailPreview-1" class="preview-item" style="display: none;">
                                            <img id="thumbnailImg-1" src="" alt="Thumbnail Preview">
                                            <button type="button" class="remove-btn" onclick="removeThumbnail(1)">&times;</button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label">Foto Produk (Bisa Lebih dari Satu)</label>
                                    <div class="file-upload" onclick="document.getElementById('productImages-1').click()">
                                        <i class="fas fa-images"></i>
                                        <p class="mb-1">Klik untuk upload foto produk</p>
                                        <small class="text-muted">Format: JPG, PNG, GIF. Max: 2MB per gambar</small>
                                        <input type="file" id="productImages-1" name="variant[1][product_images][]" accept="image/*" multiple style="display: none;">
                                    </div>
                                    <div class="preview-container" id="productImagesPreview-1"></div>
                                </div>
                                
                                <button type="button" class="remove-variant" onclick="removeVariant(1)" style="display: none;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tombol untuk menambah varian baru -->
                    <div class="mb-4">
                        <button type="button" class="btn btn-success" onclick="addNewVariant()">
                            <i class="fas fa-plus-circle me-2"></i> Tambah Varian Baru
                        </button>
                        <span class="text-muted ms-3">Klik untuk menambah varian produk dengan spesifikasi berbeda</span>
                    </div>
                    
                    <!-- Preview Kombinasi Varian -->
                    <div class="form-section">
                        <h4>Preview Kombinasi Varian <span id="totalCombinations" class="variant-counter">0</span></h4>
                        <div id="combinationPreview" class="variant-list">
                            <p class="text-muted">Belum ada kombinasi varian</p>
                        </div>
                    </div>
                    
                    <!-- Konfirmasi -->
                    <div class="form-section">
                        <h4>Konfirmasi</h4>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Sistem akan membuat semua kombinasi dari warna, penyimpanan, dan konektivitas yang dimasukkan.
                            Pastikan semua data sudah benar sebelum menyimpan.
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="ipad.php" class="btn-back">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn-submit">
                                <i class="fas fa-save me-2"></i> Simpan Semua Produk
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let variantCounter = 1;
        
        // Fungsi untuk menambah varian baru
        function addNewVariant() {
            variantCounter++;
            const variantContainer = document.getElementById('variantContainer');
            const newVariant = document.createElement('div');
            newVariant.className = 'variant-item';
            newVariant.innerHTML = `
                <div class="form-section">
                    <h4>Varian Produk #${variantCounter}</h4>
                    
                    <!-- Warna -->
                    <div class="mb-4">
                        <label class="form-label">Warna <span class="text-danger">*</span></label>
                        <div id="warna-container-${variantCounter}">
                            <div class="input-group-dynamic">
                                <input type="text" class="form-control" name="variant[${variantCounter}][warna][]" placeholder="Masukkan warna" required>
                                <button type="button" class="btn-remove-item" onclick="removeVariantItem(this, 'warna')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <button type="button" class="btn-add-field" onclick="addVariantItem(${variantCounter}, 'warna')">
                            <i class="fas fa-plus"></i> Tambah Warna
                        </button>
                    </div>
                    
                    <!-- Penyimpanan -->
                    <div class="mb-4">
                        <label class="form-label">Penyimpanan <span class="text-danger">*</span></label>
                        <div id="penyimpanan-container-${variantCounter}">
                            <div class="input-group-dynamic">
                                <input type="text" class="form-control" name="variant[${variantCounter}][penyimpanan][]" placeholder="Masukkan penyimpanan" required>
                                <button type="button" class="btn-remove-item" onclick="removeVariantItem(this, 'penyimpanan')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <button type="button" class="btn-add-field" onclick="addVariantItem(${variantCounter}, 'penyimpanan')">
                            <i class="fas fa-plus"></i> Tambah Penyimpanan
                        </button>
                    </div>
                    
                    <!-- Konektivitas -->
                    <div class="mb-4">
                        <label class="form-label">Konektivitas <span class="text-danger">*</span></label>
                        <div id="konektivitas-container-${variantCounter}">
                            <div class="input-group-dynamic">
                                <input type="text" class="form-control" name="variant[${variantCounter}][konektivitas][]" placeholder="Masukkan konektivitas" required>
                                <button type="button" class="btn-remove-item" onclick="removeVariantItem(this, 'konektivitas')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <button type="button" class="btn-add-field" onclick="addVariantItem(${variantCounter}, 'konektivitas')">
                            <i class="fas fa-plus"></i> Tambah Konektivitas
                        </button>
                    </div>
                    
                    <!-- Harga & Stok -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Harga Normal <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="variant[${variantCounter}][harga]" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Harga Diskon</label>
                            <input type="number" class="form-control" name="variant[${variantCounter}][harga_diskon]" min="0" placeholder="Kosongkan jika tidak ada diskon">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status Stok</label>
                            <select class="form-select" name="variant[${variantCounter}][status_stok]">
                                <option value="tersedia">Tersedia</option>
                                <option value="habis">Habis</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Gambar Produk -->
                    <div class="mb-4">
                        <label class="form-label">Thumbnail (Satu Gambar) <span class="text-danger">*</span></label>
                        <div class="file-upload" onclick="document.getElementById('thumbnail-${variantCounter}').click()">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p class="mb-1">Klik untuk upload thumbnail</p>
                            <small class="text-muted">Format: JPG, PNG, GIF. Max: 2MB</small>
                            <input type="file" id="thumbnail-${variantCounter}" name="variant[${variantCounter}][thumbnail]" accept="image/*" style="display: none;" required>
                        </div>
                        <div class="preview-container">
                            <div id="thumbnailPreview-${variantCounter}" class="preview-item" style="display: none;">
                                <img id="thumbnailImg-${variantCounter}" src="" alt="Thumbnail Preview">
                                <button type="button" class="remove-btn" onclick="removeThumbnail(${variantCounter})">&times;</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Foto Produk (Bisa Lebih dari Satu)</label>
                        <div class="file-upload" onclick="document.getElementById('productImages-${variantCounter}').click()">
                            <i class="fas fa-images"></i>
                            <p class="mb-1">Klik untuk upload foto produk</p>
                            <small class="text-muted">Format: JPG, PNG, GIF. Max: 2MB per gambar</small>
                            <input type="file" id="productImages-${variantCounter}" name="variant[${variantCounter}][product_images][]" accept="image/*" multiple style="display: none;">
                        </div>
                        <div class="preview-container" id="productImagesPreview-${variantCounter}"></div>
                    </div>
                    
                    <button type="button" class="remove-variant" onclick="removeVariant(${variantCounter})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            
            variantContainer.appendChild(newVariant);
            
            // Tambah event listener untuk thumbnail
            document.getElementById(`thumbnail-${variantCounter}`).addEventListener('change', function(e) {
                handleThumbnailChange(variantCounter, e);
            });
            
            // Tambah event listener untuk product images
            document.getElementById(`productImages-${variantCounter}`).addEventListener('change', function(e) {
                handleProductImagesChange(variantCounter, e);
            });
            
            // Update kombinasi
            updateCombinationPreview();
            
            // Tampilkan tombol hapus untuk varian pertama
            if (variantCounter > 1) {
                document.querySelectorAll('.remove-variant')[0].style.display = 'block';
            }
        }
        
        // Fungsi untuk menambah item ke dalam varian (warna, penyimpanan, konektivitas)
        function addVariantItem(variantId, type) {
            const container = document.getElementById(`${type}-container-${variantId}`);
            const newItem = document.createElement('div');
            newItem.className = 'input-group-dynamic';
            newItem.innerHTML = `
                <input type="text" class="form-control" name="variant[${variantId}][${type}][]" placeholder="Masukkan ${type}" required>
                <button type="button" class="btn-remove-item" onclick="removeVariantItem(this, '${type}')">
                    <i class="fas fa-times"></i>
                </button>
            `;
            container.appendChild(newItem);
            updateCombinationPreview();
        }
        
        // Fungsi untuk menghapus item dari varian
        function removeVariantItem(button, type) {
            const container = button.closest('.input-group-dynamic');
            if (container) {
                container.remove();
                updateCombinationPreview();
            }
        }
        
        // Fungsi untuk menghapus varian
        function removeVariant(variantId) {
            const variantItem = document.querySelector(`.variant-item:nth-child(${variantId})`);
            if (variantItem) {
                variantItem.remove();
                updateCombinationPreview();
            }
        }
        
        // Fungsi untuk menghandle thumbnail change
        function handleThumbnailChange(variantId, e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(`thumbnailImg-${variantId}`).src = e.target.result;
                    document.getElementById(`thumbnailPreview-${variantId}`).style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        }
        
        // Fungsi untuk remove thumbnail
        function removeThumbnail(variantId) {
            document.getElementById(`thumbnail-${variantId}`).value = '';
            document.getElementById(`thumbnailPreview-${variantId}`).style.display = 'none';
        }
        
        // Fungsi untuk menghandle product images change
        function handleProductImagesChange(variantId, e) {
            const files = e.target.files;
            const previewContainer = document.getElementById(`productImagesPreview-${variantId}`);
            
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const previewItem = document.createElement('div');
                    previewItem.className = 'preview-item';
                    previewItem.innerHTML = `
                        <img src="${e.target.result}" alt="Product Image">
                        <button type="button" class="remove-btn" onclick="removeProductImage(this)">&times;</button>
                    `;
                    previewContainer.appendChild(previewItem);
                }
                
                reader.readAsDataURL(file);
            }
        }
        
        // Fungsi untuk remove product image
        function removeProductImage(button) {
            button.parentElement.remove();
        }
        
        // Fungsi untuk menghitung dan menampilkan kombinasi
        function updateCombinationPreview() {
            const combinations = [];
            const variantItems = document.querySelectorAll('.variant-item');
            
            variantItems.forEach((variantItem, variantIndex) => {
                const variantId = variantIndex + 1;
                
                // Ambil semua warna
                const warnaInputs = variantItem.querySelectorAll(`input[name="variant[${variantId}][warna][]"]`);
                const warna = Array.from(warnaInputs).map(input => input.value.trim()).filter(val => val);
                
                // Ambil semua penyimpanan
                const penyimpananInputs = variantItem.querySelectorAll(`input[name="variant[${variantId}][penyimpanan][]"]`);
                const penyimpanan = Array.from(penyimpananInputs).map(input => input.value.trim()).filter(val => val);
                
                // Ambil semua konektivitas
                const konektivitasInputs = variantItem.querySelectorAll(`input[name="variant[${variantId}][konektivitas][]"]`);
                const konektivitas = Array.from(konektivitasInputs).map(input => input.value.trim()).filter(val => val);
                
                // Ambil harga
                const hargaInput = variantItem.querySelector(`input[name="variant[${variantId}][harga]"]`);
                const harga = hargaInput ? hargaInput.value : '0';
                
                // Buat kombinasi
                warna.forEach(w => {
                    penyimpanan.forEach(p => {
                        konektivitas.forEach(k => {
                            combinations.push({
                                warna: w,
                                penyimpanan: p,
                                konektivitas: k,
                                harga: harga,
                                variant: variantId
                            });
                        });
                    });
                });
            });
            
            // Update total kombinasi
            document.getElementById('totalCombinations').textContent = combinations.length;
            
            // Update preview
            const previewContainer = document.getElementById('combinationPreview');
            if (combinations.length > 0) {
                let html = '';
                combinations.forEach((combo, index) => {
                    html += `
                        <div class="variant-list-item">
                            <div>
                                <strong>Kombinasi #${index + 1}</strong><br>
                                <small class="text-muted">
                                    Warna: ${combo.warna} | 
                                    Penyimpanan: ${combo.penyimpanan} | 
                                    Konektivitas: ${combo.konektivitas} |
                                    Harga: Rp ${parseInt(combo.harga).toLocaleString('id-ID')}
                                </small>
                            </div>
                            <small class="text-muted">Varian #${combo.variant}</small>
                        </div>
                    `;
                });
                previewContainer.innerHTML = html;
            } else {
                previewContainer.innerHTML = '<p class="text-muted">Belum ada kombinasi varian</p>';
            }
        }
        
        // Event listener untuk real-time update kombinasi
        document.addEventListener('input', function(e) {
            if (e.target.name.includes('variant') && 
                (e.target.name.includes('[warna]') || 
                 e.target.name.includes('[penyimpanan]') || 
                 e.target.name.includes('[konektivitas]') ||
                 e.target.name.includes('[harga]'))) {
                updateCombinationPreview();
            }
        });
        
        // Inisialisasi event listener untuk varian pertama
        document.getElementById('thumbnail-1').addEventListener('change', function(e) {
            handleThumbnailChange(1, e);
        });
        
        document.getElementById('productImages-1').addEventListener('change', function(e) {
            handleProductImagesChange(1, e);
        });
        
        // Form submission
        document.getElementById('addIpadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validasi minimal satu varian
            const variantItems = document.querySelectorAll('.variant-item');
            if (variantItems.length === 0) {
                alert('Minimal harus ada satu varian produk!');
                return;
            }
            
            // Validasi setiap varian
            let isValid = true;
            variantItems.forEach((variantItem, variantIndex) => {
                const variantId = variantIndex + 1;
                
                // Validasi warna
                const warnaInputs = variantItem.querySelectorAll(`input[name="variant[${variantId}][warna][]"]`);
                const hasWarna = Array.from(warnaInputs).some(input => input.value.trim() !== '');
                
                // Validasi penyimpanan
                const penyimpananInputs = variantItem.querySelectorAll(`input[name="variant[${variantId}][penyimpanan][]"]`);
                const hasPenyimpanan = Array.from(penyimpananInputs).some(input => input.value.trim() !== '');
                
                // Validasi konektivitas
                const konektivitasInputs = variantItem.querySelectorAll(`input[name="variant[${variantId}][konektivitas][]"]`);
                const hasKonektivitas = Array.from(konektivitasInputs).some(input => input.value.trim() !== '');
                
                if (!hasWarna || !hasPenyimpanan || !hasKonektivitas) {
                    isValid = false;
                    alert(`Varian #${variantId}: Harap isi minimal satu warna, penyimpanan, dan konektivitas!`);
                    return;
                }
                
                // Validasi harga
                const hargaInput = variantItem.querySelector(`input[name="variant[${variantId}][harga]"]`);
                if (!hargaInput || hargaInput.value.trim() === '' || parseFloat(hargaInput.value) <= 0) {
                    isValid = false;
                    alert(`Varian #${variantId}: Harga harus diisi dengan nilai yang valid!`);
                    return;
                }
                
                // Validasi harga diskon
                const hargaDiskonInput = variantItem.querySelector(`input[name="variant[${variantId}][harga_diskon]"]`);
                if (hargaDiskonInput && hargaDiskonInput.value.trim() !== '') {
                    if (parseFloat(hargaDiskonInput.value) >= parseFloat(hargaInput.value)) {
                        isValid = false;
                        alert(`Varian #${variantId}: Harga diskon harus lebih rendah dari harga normal!`);
                        return;
                    }
                }
                
                // Validasi thumbnail
                const thumbnailInput = variantItem.querySelector(`input[name="variant[${variantId}][thumbnail]"]`);
                if (!thumbnailInput || !thumbnailInput.files || thumbnailInput.files.length === 0) {
                    isValid = false;
                    alert(`Varian #${variantId}: Thumbnail harus diupload!`);
                    return;
                }
            });
            
            if (!isValid) return;
            
            // Tampilkan loading
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
                    alert('Semua produk berhasil ditambahkan!');
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
        
        // Inisialisasi preview kombinasi
        updateCombinationPreview();
    </script>
</body>
</html>