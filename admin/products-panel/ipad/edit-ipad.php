<?php
session_start();
require_once '../../db.php';

// Cek login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../../auth/login.php?error=not_logged_in');
    exit();
}

// Cek ID produk
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ipad.php?error=invalid_id');
    exit();
}

$product_id = escape($_GET['id']);

// Ambil data produk
$query = "SELECT * FROM admin_produk_ipad WHERE id = '$product_id'";
$result = mysqli_query($db, $query);

if (mysqli_num_rows($result) == 0) {
    header('Location: ipad.php?error=product_not_found');
    exit();
}

$product = mysqli_fetch_assoc($result);

// Ambil gambar produk
$query_images = "SELECT * FROM admin_gambar_produk WHERE produk_id = '$product_id' AND tipe_produk = 'ipad'";
$result_images = mysqli_query($db, $query_images);
$images = mysqli_fetch_all($result_images, MYSQLI_ASSOC);

$thumbnail = '';
$product_images = [];

foreach($images as $img) {
    if(!empty($img['foto_thumbnail'])) {
        $thumbnail = $img;
    }
    if(!empty($img['foto_produk'])) {
        $product_images[] = $img;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk iPad</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* CSS sama dengan add-ipad.php, tambahkan sedikit modifikasi */
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
            background: linear-gradient(135deg, #ff9800 0%, #ff5722 100%);
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
        
        .existing-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #eaeaea;
        }
        
        .image-container {
            position: relative;
            display: inline-block;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        
        .remove-existing {
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
                <h2><i class="fas fa-edit me-2"></i> Edit Produk iPad</h2>
            </div>
            <div class="card-body">
                <form id="editIpadForm" action="api/api-edit-ipad.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <input type="hidden" name="existing_thumbnail" value="<?php echo $thumbnail ? $thumbnail['foto_thumbnail'] : ''; ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-section">
                                <h4>Informasi Produk</h4>
                                
                                <div class="mb-3">
                                    <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nama_produk" value="<?php echo htmlspecialchars($product['nama_produk']); ?>" required>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Warna</label>
                                        <input type="text" class="form-control" name="warna" value="<?php echo htmlspecialchars($product['warna']); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Penyimpanan</label>
                                        <input type="text" class="form-control" name="penyimpanan" value="<?php echo htmlspecialchars($product['penyimpanan']); ?>">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Konektivitas</label>
                                    <input type="text" class="form-control" name="konektivitas" value="<?php echo htmlspecialchars($product['konektivitas']); ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Deskripsi Produk</label>
                                    <textarea class="form-control" name="deskripsi_produk" rows="4"><?php echo htmlspecialchars($product['deskripsi_produk']); ?></textarea>
                                </div>
                            </div>
                            
                            <div class="form-section">
                                <h4>Harga & Stok</h4>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Harga Normal <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="harga" value="<?php echo $product['harga']; ?>" min="0" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Harga Diskon</label>
                                        <input type="number" class="form-control" name="harga_diskon" value="<?php echo $product['harga_diskon']; ?>" min="0">
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Jumlah Stok</label>
                                        <input type="number" class="form-control" name="jumlah_stok" value="<?php echo $product['jumlah_stok']; ?>" min="0">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Status Stok</label>
                                        <select class="form-select" name="status_stok">
                                            <option value="tersedia" <?php echo $product['status_stok'] == 'tersedia' ? 'selected' : ''; ?>>Tersedia</option>
                                            <option value="habis" <?php echo $product['status_stok'] == 'habis' ? 'selected' : ''; ?>>Habis</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-section">
                                <h4>Gambar Produk</h4>
                                
                                <!-- Thumbnail Existing -->
                                <div class="mb-4">
                                    <label class="form-label">Thumbnail Saat Ini</label>
                                    <?php if($thumbnail): ?>
                                        <div class="image-container">
                                            <img src="../../uploads/<?php echo $thumbnail['foto_thumbnail']; ?>" alt="Thumbnail" class="existing-image">
                                            <button type="button" class="remove-existing" onclick="removeExistingImage(<?php echo $thumbnail['id']; ?>, 'thumbnail')">&times;</button>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <label class="form-label mt-3">Upload Thumbnail Baru (Opsional)</label>
                                    <div class="file-upload" onclick="document.getElementById('new_thumbnail').click()">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <p class="mb-1">Klik untuk upload thumbnail baru</p>
                                        <small class="text-muted">Kosongkan jika tidak ingin mengganti</small>
                                        <input type="file" id="new_thumbnail" name="new_thumbnail" accept="image/*" style="display: none;">
                                    </div>
                                    <div class="preview-container">
                                        <div id="thumbnailPreview" class="preview-item" style="display: none;">
                                            <img id="thumbnailImg" src="" alt="Thumbnail Preview">
                                            <button type="button" class="remove-btn" onclick="removeThumbnail()">&times;</button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Product Images Existing -->
                                <div class="mb-4">
                                    <label class="form-label">Foto Produk Saat Ini</label>
                                    <div class="mb-3">
                                        <?php if(count($product_images) > 0): ?>
                                            <?php foreach($product_images as $img): ?>
                                                <div class="image-container">
                                                    <img src="../../uploads/<?php echo $img['foto_produk']; ?>" alt="Product Image" class="existing-image">
                                                    <button type="button" class="remove-existing" onclick="removeExistingImage(<?php echo $img['id']; ?>, 'product')">&times;</button>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p class="text-muted">Belum ada foto produk</p>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <label class="form-label">Tambah Foto Produk Baru (Opsional)</label>
                                    <div class="file-upload" onclick="document.getElementById('new_product_images').click()">
                                        <i class="fas fa-images"></i>
                                        <p class="mb-1">Klik untuk tambah foto produk</p>
                                        <small class="text-muted">Bisa pilih lebih dari satu file</small>
                                        <input type="file" id="new_product_images" name="new_product_images[]" accept="image/*" multiple style="display: none;">
                                    </div>
                                    <div class="preview-container" id="productImagesPreview"></div>
                                </div>
                            </div>
                            
                            <div class="form-section">
                                <h4>Konfirmasi</h4>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Perubahan akan langsung diterapkan di website.
                                </div>
                                
                                <div class="d-flex justify-content-between mt-4">
                                    <a href="ipad.php" class="btn-back">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                    <button type="submit" class="btn-submit">
                                        <i class="fas fa-save me-2"></i> Update Produk
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
        // Array untuk menyimpan ID gambar yang akan dihapus
        let imagesToDelete = [];
        
        function removeExistingImage(imageId, type) {
            if (confirm('Yakin ingin menghapus gambar ini?')) {
                imagesToDelete.push({id: imageId, type: type});
                document.querySelector(`.remove-existing[onclick="removeExistingImage(${imageId}, '${type}')"]`).parentElement.remove();
            }
        }

        // Thumbnail preview untuk upload baru
        document.getElementById('new_thumbnail').addEventListener('change', function(e) {
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
            document.getElementById('new_thumbnail').value = '';
            document.getElementById('thumbnailPreview').style.display = 'none';
        }

        // Product images preview untuk upload baru
        document.getElementById('new_product_images').addEventListener('change', function(e) {
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
                        <button type="button" class="remove-btn" onclick="removeNewProductImage(this)">&times;</button>
                    `;
                    previewContainer.appendChild(previewItem);
                }
                
                reader.readAsDataURL(file);
            }
        });

        function removeNewProductImage(button) {
            button.parentElement.remove();
        }

        // Form submission
        document.getElementById('editIpadForm').addEventListener('submit', function(e) {
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
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memperbarui...';
            submitBtn.disabled = true;
            
            // Tambahkan data gambar yang akan dihapus ke form
            const formData = new FormData(this);
            formData.append('images_to_delete', JSON.stringify(imagesToDelete));
            
            fetch('api/api-edit-ipad.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Produk berhasil diperbarui!');
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