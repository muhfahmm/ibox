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
    header('Location: aksesoris.php');
    exit();
}

$product_id = mysqli_real_escape_string($db, $_GET['id']);
$product = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM admin_produk_aksesoris WHERE id = '$product_id'"));

if (!$product) {
    header('Location: aksesoris.php?error=not_found');
    exit();
}

// Decode kompatibel_dengan jika ada
$kompatibel_data = json_decode($product['kompatibel_dengan'], true) ?? [];

// Fetch Existing Data
$images_query = mysqli_query($db, "SELECT * FROM admin_produk_aksesoris_gambar WHERE produk_id='$product_id'");
$colors_data = [];
while ($row = mysqli_fetch_assoc($images_query)) {
    $colors_data[] = [
        'nama' => $row['warna'],
        'thumbnail' => $row['foto_thumbnail'],
        'images' => json_decode($row['foto_produk'], true) ?? []
    ];
}

// Fetch combinations dengan lebih detail
$combinations_query = mysqli_query($db, "SELECT * FROM admin_produk_aksesoris_kombinasi WHERE produk_id='$product_id'");
$combinations_data = mysqli_fetch_all($combinations_query, MYSQLI_ASSOC);

// Ekstrak tipe dan ukuran unik dari data kombinasi
$types_map = [];
$sizes_map = [];

foreach ($combinations_data as $c) {
    if (!empty($c['tipe']) && !isset($types_map[$c['tipe']])) {
        $types_map[$c['tipe']] = true;
    }
    if (!empty($c['ukuran']) && !isset($sizes_map[$c['ukuran']])) {
        $sizes_map[$c['ukuran']] = true;
    }
}

// Format data untuk JavaScript
$initialData = [
    'colors' => $colors_data,
    'types' => array_keys($types_map),
    'sizes' => array_keys($sizes_map),
    'stocks' => $combinations_data, // Kirim semua data kombinasi
    'kompatibel' => $kompatibel_data
];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk Aksesoris - <?php echo htmlspecialchars($product['nama_produk']); ?></title>
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
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
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

        .form-control,
        textarea.form-control,
        select.form-control {
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

        .form-control:focus,
        textarea.form-control:focus,
        select.form-control:focus {
            border-color: #4a6cf7;
            outline: none;
            box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.25);
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
            box-shadow: 0 5px 15px rgba(39, 174, 96, 0.3);
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
        .color-option,
        .type-option,
        .size-option,
        .kompatibel-option {
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
            to {
                transform: rotate(360deg);
            }
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

        .col-3 {
            width: 25%;
        }

        .col-4 {
            width: 33.333%;
        }

        .col-6 {
            width: 50%;
        }

        .col-9 {
            width: 75%;
        }

        .col-12 {
            width: 100%;
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eaeaea;
        }

        .mb-3 {
            margin-bottom: 15px;
        }

        .mt-2 {
            margin-top: 10px;
        }

        .mt-5 {
            margin-top: 30px;
        }

        .pt-3 {
            padding-top: 15px;
        }

        .pt-4 {
            padding-top: 20px;
        }

        .text-danger {
            color: #dc3545;
        }

        .text-muted {
            color: #6c757d;
        }

        .text-center {
            text-align: center;
        }

        .p-4 {
            padding: 20px;
        }

        /* Tag input untuk kompatibel */
        .tags-container {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            min-height: 50px;
        }

        .tag {
            background-color: #e3f2fd;
            color: #1976d2;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .tag-remove {
            cursor: pointer;
            font-size: 16px;
            line-height: 1;
        }

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

            .col-3,
            .col-4,
            .col-6,
            .col-9,
            .col-12 {
                width: 100%;
            }

            .form-actions {
                flex-direction: column;
                gap: 15px;
            }

            .btn-submit,
            .btn-back {
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
                <h2><i class="fas fa-edit"></i> Edit Produk Aksesoris</h2>
            </div>
            <div class="card-body">
                <form id="editAksesorisForm" action="api/api-edit-aksesoris.php" method="POST" enctype="multipart/form-data" autocomplete="off">
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">

                    <!-- Informasi Produk -->
                    <div class="form-section">
                        <h4><i class="fas fa-info-circle"></i> Informasi Produk</h4>

                        <div class="form-row">
                            <div class="form-col col-6">
                                <div class="mb-3">
                                    <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nama_produk" value="<?php echo htmlspecialchars($product['nama_produk']); ?>" required>
                                </div>
                            </div>
                            <div class="form-col col-6">
                                <div class="mb-3">
                                    <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="kategori" value="<?php echo htmlspecialchars($product['kategori'] ?? ''); ?>" placeholder="Contoh: Case, Charger, Headphone, Keyboard" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kompatibel Dengan</label>
                            <input type="text" id="kompatibelInput" class="form-control" placeholder="Masukkan perangkat (tekan Enter untuk menambahkan)">
                            <div class="tags-container" id="kompatibelTags">
                                <!-- Tags akan muncul di sini -->
                            </div>
                            <input type="hidden" name="kompatibel_dengan" id="kompatibelHidden">
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

                    <!-- Tipe -->
                    <div class="form-section">
                        <h4><i class="fas fa-tag"></i> Tipe Aksesoris</h4>

                        <div id="typesContainer">
                            <!-- Tipe akan di-generate oleh Javascript -->
                        </div>

                        <button type="button" class="add-option-btn" onclick="addType()">
                            <i class="fas fa-plus"></i> Tambah Tipe Lain
                        </button>
                    </div>

                    <!-- Ukuran (Opsional) -->
                    <div class="form-section">
                        <h4><i class="fas fa-ruler"></i> Ukuran (Opsional)</h4>

                        <div id="sizesContainer">
                            <!-- Ukuran akan di-generate oleh Javascript -->
                        </div>

                        <button type="button" class="add-option-btn" onclick="addSize()">
                            <i class="fas fa-plus"></i> Tambah Ukuran Lain
                        </button>
                    </div>

                    <!-- Tabel Kombinasi & Stok -->
                    <div class="form-section">
                        <h4><i class="fas fa-table"></i> Kombinasi, Harga & Stok</h4>
                        <div class="alert-info">
                            <i class="fas fa-info-circle"></i>
                            Harga dan stok akan di-update otomatis dari data yang ada. Ubah jika ingin melakukan update.
                        </div>

                        <div id="combinationsContainer">
                            <!-- Tabel kombinasi akan di-generate di sini -->
                        </div>

                        <div class="total-combinations" id="totalCombinations">
                            Loading data...
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="form-actions">
                        <a href="aksesoris.php" class="btn-back">
                            <i class="fas fa-arrow-left"></i> Kembali
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
        let typeCount = 0;
        let sizeCount = 0;
        let kompatibelTags = [...(initialData.kompatibel || [])];

        // Fungsi untuk mencari data existing berdasarkan kombinasi
        function findExistingStock(color, type, size) {
            if (!initialData.stocks || !Array.isArray(initialData.stocks)) {
                return null;
            }

            // Normalisasi nilai untuk perbandingan
            const normalizedColor = color ? color.trim() : '';
            const normalizedType = type ? type.trim() : '';
            const normalizedSize = size ? size.trim() : '';

            // Cari data yang cocok
            return initialData.stocks.find(item => {
                const itemColor = item.warna ? item.warna.trim() : '';
                const itemType = item.tipe ? item.tipe.trim() : '';
                const itemSize = item.ukuran ? item.ukuran.trim() : '';

                return itemColor === normalizedColor &&
                    itemType === normalizedType &&
                    itemSize === normalizedSize;
            }) || null;
        }

        // Initialize Form
        window.addEventListener('DOMContentLoaded', () => {
            // Initialize kompatibel tags
            updateKompatibelTags();

            // Colors
            if (initialData.colors && initialData.colors.length > 0) {
                initialData.colors.forEach(color => addColor(color));
            } else {
                addColor(); // Default empty
            }

            // Types
            if (initialData.types && initialData.types.length > 0) {
                initialData.types.forEach(type => addType(type));
            } else {
                addType();
            }

            // Sizes
            if (initialData.sizes && initialData.sizes.length > 0) {
                initialData.sizes.forEach(size => addSize(size));
            } else {
                addSize(); // Optional, but add one empty
            }

            // Generate Combinations
            setTimeout(() => {
                generateCombinations();
            }, 300);
        });

        // Kompatibel Tags System
        const kompatibelInput = document.getElementById('kompatibelInput');
        const kompatibelTagsContainer = document.getElementById('kompatibelTags');
        const kompatibelHidden = document.getElementById('kompatibelHidden');

        function updateKompatibelTags() {
            kompatibelTagsContainer.innerHTML = '';
            kompatibelTags.forEach((tag, index) => {
                const tagElement = document.createElement('div');
                tagElement.className = 'tag';
                tagElement.innerHTML = `
                    ${tag}
                    <span class="tag-remove" onclick="removeKompatibelTag(${index})">&times;</span>
                `;
                kompatibelTagsContainer.appendChild(tagElement);
            });
            kompatibelHidden.value = JSON.stringify(kompatibelTags);
        }

        function addKompatibelTag() {
            const value = kompatibelInput.value.trim();
            if (value && !kompatibelTags.includes(value)) {
                kompatibelTags.push(value);
                updateKompatibelTags();
                kompatibelInput.value = '';
            }
        }

        function removeKompatibelTag(index) {
            kompatibelTags.splice(index, 1);
            updateKompatibelTags();
        }

        kompatibelInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addKompatibelTag();
            }
        });

        // Form Submission
        document.getElementById('editAksesorisForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Basic Validation
            const productName = document.querySelector('input[name="nama_produk"]').value;
            if (!productName.trim()) {
                alert('Nama produk harus diisi');
                return;
            }

            // Show Loading
            const overlay = document.getElementById('loadingOverlay');
            overlay.style.display = 'flex';

            const formData = new FormData(this);

            fetch('api/api-edit-aksesoris.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Produk berhasil diperbarui!');
                        window.location.href = 'view-aksesoris.php?id=<?php echo $product_id; ?>';
                    } else {
                        alert('Error: ' + data.message);
                        overlay.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan network/server');
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
                        <input type="text" class="form-control color-name-input" 
                               name="warna[${newIndex}][nama]" 
                               placeholder="Nama Warna (Contoh: Hitam)" 
                               required value="${data ? data.nama : ''}" 
                               data-index="${newIndex}"
                               onchange="handleColorChange(${newIndex})">
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

            // Jika ini warna baru, generate kombinasi setelah ditambahkan
            setTimeout(() => {
                generateCombinations();
            }, 100);
        }

        function handleColorChange(index) {
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

        // Tipe
        function addType(data = null) {
            const container = document.getElementById('typesContainer');
            const newIndex = typeCount;

            const newType = document.createElement('div');
            newType.className = 'type-option';
            newType.dataset.typeIndex = newIndex;
            newType.innerHTML = `
                <input type="text" class="form-control type-input" 
                       name="tipe[${newIndex}]" 
                       placeholder="Tipe (Contoh: Silicone, Leather)" 
                       required value="${data ? data : ''}" 
                       data-index="${newIndex}"
                       onchange="generateCombinations()">
                <button type="button" class="btn-danger-sm" onclick="removeType(${newIndex})">
                    ×
                </button>
            `;

            container.appendChild(newType);
            typeCount++;
            generateCombinations();
        }

        function removeType(index) {
            const typeElements = document.querySelectorAll('.type-option');
            if (typeElements.length <= 1) {
                alert('Minimal harus ada satu tipe');
                return;
            }

            const typeElement = document.querySelector(`.type-option[data-type-index="${index}"]`);
            if (typeElement) {
                typeElement.remove();
                generateCombinations();
            }
        }

        // Ukuran (Opsional)
        function addSize(data = null) {
            const container = document.getElementById('sizesContainer');
            const newIndex = sizeCount;

            const newSize = document.createElement('div');
            newSize.className = 'size-option';
            newSize.dataset.sizeIndex = newIndex;
            newSize.innerHTML = `
                <input type="text" class="form-control size-input" 
                       name="ukuran[${newIndex}]" 
                       placeholder="Ukuran (Contoh: Standard, Large) - Opsional" 
                       value="${data ? data : ''}" 
                       data-index="${newIndex}"
                       onchange="generateCombinations()">
                <button type="button" class="btn-danger-sm" onclick="removeSize(${newIndex})">
                    ×
                </button>
            `;

            container.appendChild(newSize);
            sizeCount++;
            generateCombinations();
        }

        function removeSize(index) {
            const sizeElements = document.querySelectorAll('.size-option');
            if (sizeElements.length <= 1) {
                // For sizes, we can have 0 or more, but keep at least one empty
                const element = document.querySelector(`.size-option[data-size-index="${index}"]`);
                if (element) {
                    element.querySelector('input').value = '';
                }
                return;
            }

            const sizeElement = document.querySelector(`.size-option[data-size-index="${index}"]`);
            if (sizeElement) {
                sizeElement.remove();
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

        // Generate Combinations - DIPERBAIKI
        function generateCombinations() {
            const container = document.getElementById('combinationsContainer');
            const totalContainer = document.getElementById('totalCombinations');

            // Collect data dari input dengan benar
            const colors = [];
            const colorInputs = document.querySelectorAll('.color-option input[name*="[nama]"]');
            colorInputs.forEach(input => {
                const value = input.value.trim();
                if (value) colors.push(value);
            });

            const types = [];
            const typeInputs = document.querySelectorAll('.type-option input');
            typeInputs.forEach(input => {
                const value = input.value.trim();
                if (value) types.push(value);
            });

            const sizes = [];
            const sizeInputs = document.querySelectorAll('.size-option input');
            sizeInputs.forEach(input => {
                const value = input.value.trim();
                sizes.push(value);
            });

            // Jika tidak ada ukuran yang diisi, gunakan array dengan satu string kosong
            const validSizes = sizes.filter(s => s);
            const sizeList = validSizes.length > 0 ? validSizes : [''];

            // Generate table
            const totalCombinations = colors.length * types.length * sizeList.length;
            const sizesCount = validSizes.length || 1;

            totalContainer.innerHTML =
                `Total Kombinasi: <strong>${totalCombinations}</strong> (${colors.length} warna × ${types.length} tipe × ${sizesCount} ukuran)`;

            if (totalCombinations > 0) {
                let tableHTML = `
                    <table class="combination-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Warna</th>
                                <th>Tipe</th>
                                <th>Ukuran</th>
                                <th>Harga (Rp)</th>
                                <th>Diskon (Rp)</th>
                                <th>Jumlah Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                let counter = 1;
                colors.forEach((color, cIdx) => {
                    types.forEach((type, tIdx) => {
                        sizeList.forEach((size, sIdx) => {
                            const uniqueId = `${cIdx}_${tIdx}_${sIdx}`;

                            // Cari data existing dengan fungsi yang sudah diperbaiki
                            const existingData = findExistingStock(color, type, size);

                            tableHTML += `
                                <tr>
                                    <td>${counter}</td>
                                    <td>${color}</td>
                                    <td>${type}</td>
                                    <td>${size || '-'}</td>
                                    <td>
                                        <input type="number" class="form-control combination-harga" 
                                               name="combinations[${uniqueId}][harga]" 
                                               placeholder="Harga" min="0" required 
                                               value="${existingData ? existingData.harga : ''}">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control combination-diskon" 
                                               name="combinations[${uniqueId}][harga_diskon]" 
                                               placeholder="Diskon (opsional)" min="0"
                                               value="${existingData && existingData.harga_diskon ? existingData.harga_diskon : ''}">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control combination-stok" 
                                               name="combinations[${uniqueId}][jumlah_stok]" 
                                               value="${existingData ? existingData.jumlah_stok : ''}" 
                                               placeholder="0" min="0" required>
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
                container.innerHTML = '<div class="text-center p-4 text-muted">Lengkapi data warna dan tipe untuk melihat tabel kombinasi</div>';
            }
        }

        // Event listener untuk update kombinasi saat input berubah
        document.addEventListener('input', function(e) {
            if (e.target.matches('.color-name-input, .type-input, .size-input')) {
                // Gunakan debounce untuk menghindari terlalu banyak pemanggilan
                clearTimeout(window.combinationTimeout);
                window.combinationTimeout = setTimeout(() => {
                    generateCombinations();
                }, 500);
            }
        });
    </script>
</body>

</html>