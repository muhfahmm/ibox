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
            max-width: 1200px;
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
                        <div class="col-md-6">
                            <div class="form-section">
                                <h4>Informasi Produk</h4>
                                
                                <div class="mb-3">
                                    <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nama_produk" required>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Warna</label>
                                        <input type="text" class="form-control" name="warna" placeholder="Contoh: Silver, Space Gray">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Penyimpanan</label>
                                        <input type="text" class="form-control" name="penyimpanan" placeholder="Contoh: 128GB, 256GB">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Konektivitas</label>
                                    <input type="text" class="form-control" name="konektivitas" placeholder="Contoh: Wi-Fi, Wi-Fi + Cellular">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Deskripsi Produk</label>
                                    <textarea class="form-control" name="deskripsi_produk" rows="4" placeholder="Masukkan deskripsi lengkap produk..."></textarea>
                                </div>
                            </div>
                            
                            <div class="form-section">
                                <h4>Harga & Stok</h4>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Harga Normal <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="harga" min="0" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Harga Diskon</label>
                                        <input type="number" class="form-control" name="harga_diskon" min="0" placeholder="Kosongkan jika tidak ada diskon">
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Jumlah Stok</label>
                                        <input type="number" class="form-control" name="jumlah_stok" value="0" min="0">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Status Stok</label>
                                        <select class="form-select" name="status_stok">
                                            <option value="tersedia">Tersedia</option>
                                            <option value="habis">Habis</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-section">
                                <h4>Gambar Produk</h4>
                                
                                <!-- Thumbnail Upload -->
                                <div class="mb-4">
                                    <label class="form-label">Thumbnail (Satu Gambar) <span class="text-danger">*</span></label>
                                    <div class="file-upload" onclick="document.getElementById('thumbnail').click()">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <p class="mb-1">Klik untuk upload thumbnail</p>
                                        <small class="text-muted">Format: JPG, PNG, GIF. Max: 2MB</small>
                                        <input type="file" id="thumbnail" name="thumbnail" accept="image/*" style="display: none;" required>
                                    </div>
                                    <div class="preview-container">
                                        <div id="thumbnailPreview" class="preview-item" style="display: none;">
                                            <img id="thumbnailImg" src="" alt="Thumbnail Preview">
                                            <button type="button" class="remove-btn" onclick="removeThumbnail()">&times;</button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Product Images Upload -->
                                <div class="mb-4">
                                    <label class="form-label">Foto Produk (Bisa Lebih dari Satu)</label>
                                    <div class="file-upload" onclick="document.getElementById('productImages').click()">
                                        <i class="fas fa-images"></i>
                                        <p class="mb-1">Klik untuk upload foto produk</p>
                                        <small class="text-muted">Format: JPG, PNG, GIF. Max: 2MB per gambar</small>
                                        <input type="file" id="productImages" name="product_images[]" accept="image/*" multiple style="display: none;">
                                    </div>
                                    <div class="preview-container" id="productImagesPreview"></div>
                                </div>
                            </div>
                            
                            <div class="form-section">
                                <h4>Konfirmasi</h4>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Pastikan semua data sudah benar sebelum menyimpan. Produk akan langsung muncul di website setelah disimpan.
                                </div>
                                
                                <div class="d-flex justify-content-between mt-4">
                                    <a href="ipad.php" class="btn-back">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                    <button type="submit" class="btn-submit">
                                        <i class="fas fa-save me-2"></i> Simpan Produk
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Thumbnail preview
        document.getElementById('thumbnail').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('thumbnailImg').src = e.target.result;
                    document.getElementById('thumbnailPreview').style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });

        function removeThumbnail() {
            document.getElementById('thumbnail').value = '';
            document.getElementById('thumbnailPreview').style.display = 'none';
        }

        // Product images preview
        document.getElementById('productImages').addEventListener('change', function(e) {
            const files = e.target.files;
            const previewContainer = document.getElementById('productImagesPreview');
            
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
        });

        function removeProductImage(button) {
            button.parentElement.remove();
        }

        // Form submission
        document.getElementById('addIpadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validasi harga
            const harga = parseFloat(document.querySelector('input[name="harga"]').value);
            const hargaDiskon = parseFloat(document.querySelector('input[name="harga_diskon"]').value);
            
            if (hargaDiskon && hargaDiskon >= harga) {
                alert('Harga diskon harus lebih rendah dari harga normal');
                return;
            }
            
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
    </script>
</body>
</html>