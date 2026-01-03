<?php
session_start();
require_once '../../db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../../auth/login.php?error=not_logged_in');
    exit();
}

$admin_username = $_SESSION['admin_username'] ?? 'Admin';

// Ambil daftar produk dari semua kategori
$products = [];
$categories = [
    'iphone' => 'admin_produk_iphone',
    'ipad' => 'admin_produk_ipad',
    'mac' => 'admin_produk_mac',
    'music' => 'admin_produk_music',
    'watch' => 'admin_produk_watch',
    'aksesoris' => 'admin_produk_aksesoris',
    'airtag' => 'admin_produk_airtag'
];

foreach ($categories as $key => $table) {
    $query = "SELECT id, nama_produk, deskripsi_produk FROM $table ORDER BY nama_produk";
    $result = mysqli_query($db, $query);
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = [
                'id' => $row['id'],
                'nama' => $row['nama_produk'],
                'deskripsi' => $row['deskripsi_produk'] ?? '',
                'tipe' => $key
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Image Slider</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            background-color: #f5f7fb;
            padding: 40px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }
        h1 {
            font-size: 24px;
            margin-bottom: 30px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .form-group {
            margin-bottom: 25px;
        }
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: 500;
            color: #444;
            font-size: 14px;
        }
        select, input, textarea {
            width: 100%;
            padding: 14px;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s ease;
            background-color: #fcfcfc;
        }
        select:focus, input:focus, textarea:focus {
            outline: none;
            border-color: #4a6cf7;
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(74, 108, 247, 0.1);
        }
        textarea {
            min-height: 120px;
            resize: vertical;
        }
        .file-upload-wrapper {
            position: relative;
            width: 100%;
        }
        .file-upload-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            border: 2px dashed #e0e0e0;
            border-radius: 12px;
            background-color: #fcfcfc;
            cursor: pointer;
            transition: all 0.3s;
        }
        .file-upload-label:hover {
            border-color: #4a6cf7;
            background-color: #f0f4ff;
        }
        .file-upload-label i {
            font-size: 48px;
            color: #4a6cf7;
            margin-bottom: 15px;
        }
        .file-upload-label span {
            color: #666;
            font-size: 14px;
        }
        .file-upload-label small {
            color: #999;
            font-size: 12px;
            margin-top: 5px;
        }
        #gambar_produk {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        .image-preview {
            margin-top: 20px;
            display: none;
            text-align: center;
        }
        .image-preview img {
            max-width: 100%;
            max-height: 400px;
            border-radius: 12px;
            border: 2px solid #e0e0e0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .image-preview-label {
            display: block;
            margin-top: 10px;
            font-size: 13px;
            color: #666;
            font-weight: 500;
        }
        .btn-submit {
            background: linear-gradient(135deg, #4a6cf7 0%, #6a11cb 100%);
            color: white;
            padding: 14px 28px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(74, 108, 247, 0.2);
            width: 100%;
            margin-top: 10px;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(74, 108, 247, 0.3);
        }
        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
        .btn-back {
            display: block;
            text-align: center;
            text-decoration: none;
            color: #888;
            margin-top: 20px;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.3s;
        }
        .btn-back:hover {
            color: #333;
        }
        .help-text {
            display: block;
            margin-top: 6px;
            font-size: 12px;
            color: #999;
        }
        .required {
            color: #e74c3c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-images" style="color: #4a6cf7;"></i> Tambah Image Slider</h1>
        
        <form id="addSliderForm" enctype="multipart/form-data">
            <div class="form-group">
                <label><i class="fas fa-layer-group me-2"></i>Tipe Produk <span class="required">*</span></label>
                <select name="tipe_produk" id="tipe_produk" required>
                    <option value="">-- Pilih Tipe --</option>
                    <option value="iphone">iPhone</option>
                    <option value="ipad">iPad</option>
                    <option value="mac">Mac</option>
                    <option value="music">Music</option>
                    <option value="watch">Watch</option>
                    <option value="aksesoris">Aksesoris</option>
                    <option value="airtag">AirTag</option>
                </select>
            </div>

            <div class="form-group">
                <label><i class="fas fa-box me-2"></i>Produk <span class="required">*</span></label>
                <select name="produk_id" id="produk_id" required disabled>
                    <option value="">Pilih Tipe Produk Terlebih Dahulu</option>
                </select>
            </div>

            <div class="form-group">
                <label><i class="fas fa-heading me-2"></i>Nama Produk <span class="required">*</span></label>
                <input type="text" name="nama_produk" id="nama_produk" placeholder="Nama produk untuk slider" required>
                <small class="help-text">Nama ini akan ditampilkan di slider homepage.</small>
            </div>

            <div class="form-group">
                <label><i class="fas fa-align-left me-2"></i>Deskripsi Produk</label>
                <textarea name="deskripsi_produk" id="deskripsi_produk" placeholder="Deskripsi singkat untuk slider"></textarea>
                <small class="help-text">Deskripsi akan muncul di bawah nama produk di slider.</small>
            </div>

            <div class="form-group">
                <label><i class="fas fa-image me-2"></i>Gambar Slider <span class="required">*</span></label>
                <div class="file-upload-wrapper">
                    <input type="file" name="gambar_produk" id="gambar_produk" accept="image/*" required>
                    <label for="gambar_produk" class="file-upload-label">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span>Klik untuk memilih gambar atau drag & drop</span>
                        <small>Format: JPG, JPEG, PNG, GIF, WebP (Maks. 5MB)</small>
                    </label>
                </div>
                <div class="image-preview" id="imagePreview">
                    <img id="previewImg" src="#" alt="Preview">
                    <span class="image-preview-label">Preview Gambar Slider</span>
                </div>
            </div>

            <button type="submit" class="btn-submit" id="btnSubmit">
                <i class="fas fa-save me-2"></i> Simpan Image Slider
            </button>
            <a href="image-slider.php" class="btn-back">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </form>
    </div>

    <script>
        const allProducts = <?php echo json_encode($products); ?>;
        
        // Handle tipe produk change
        document.getElementById('tipe_produk').addEventListener('change', function() {
            const selectedTipe = this.value;
            const produkSelect = document.getElementById('produk_id');
            
            if (selectedTipe === "") {
                produkSelect.disabled = true;
                produkSelect.innerHTML = '<option value="">Pilih Tipe Produk Terlebih Dahulu</option>';
                return;
            }

            produkSelect.disabled = false;
            produkSelect.innerHTML = '<option value="">-- Pilih Produk --</option>';
            
            const filteredProducts = allProducts.filter(p => p.tipe === selectedTipe);
            filteredProducts.forEach(p => {
                const option = document.createElement('option');
                option.value = p.id;
                option.text = p.nama;
                option.dataset.nama = p.nama;
                option.dataset.deskripsi = p.deskripsi;
                produkSelect.appendChild(option);
            });
        });

        // Auto-fill nama dan deskripsi saat produk dipilih
        document.getElementById('produk_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const namaInput = document.getElementById('nama_produk');
            const deskripsiInput = document.getElementById('deskripsi_produk');
            
            if (selectedOption.value) {
                namaInput.value = selectedOption.dataset.nama;
                
                if (!deskripsiInput.value.trim()) {
                    const temp = document.createElement('div');
                    temp.innerHTML = selectedOption.dataset.deskripsi;
                    deskripsiInput.value = temp.textContent || temp.innerText || "";
                }
            }
        });

        // Image preview
        document.getElementById('gambar_produk').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            
            if (file) {
                // Validasi ukuran file (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Terlalu Besar!',
                        text: 'Ukuran file maksimal 5MB'
                    });
                    this.value = '';
                    preview.style.display = 'none';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });

        // Form submission
        document.getElementById('addSliderForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btnSubmit = document.getElementById('btnSubmit');
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Mengupload...';

            const formData = new FormData(this);

            fetch('api/api-image-slider.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = 'image-slider.php?success=added';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message
                    });
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<i class="fas fa-save me-2"></i> Simpan Image Slider';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan sistem.'
                });
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = '<i class="fas fa-save me-2"></i> Simpan Image Slider';
            });
        });
    </script>
</body>
</html>