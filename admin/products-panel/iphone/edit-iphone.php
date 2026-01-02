<?php
session_start();
require_once '../../db.php';

// Cek login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../../auth/login.php?error=not_logged_in');
    exit();
}

// Cek ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: iphone.php?error=invalid_id');
    exit();
}

$product_id = mysqli_real_escape_string($db, $_GET['id']);

// Ambil data produk utama
$query_product = "SELECT * FROM admin_produk_iphone WHERE id = '$product_id'";
$result_product = mysqli_query($db, $query_product);

if (mysqli_num_rows($result_product) === 0) {
    header('Location: iphone.php?error=product_not_found');
    exit();
}

$product = mysqli_fetch_assoc($result_product);

// Ambil data gambar (grouped by color)
$query_images = "SELECT * FROM admin_produk_iphone_gambar WHERE produk_id = '$product_id'";
$result_images = mysqli_query($db, $query_images);
$images_by_color = [];
while ($row = mysqli_fetch_assoc($result_images)) {
    $images_by_color[$row['warna']] = $row;
}

// Ambil data kombinasi (untuk pre-fill penyimpanan, konektivitas, dan stok)
$query_combos = "SELECT * FROM admin_produk_iphone_kombinasi WHERE produk_id = '$product_id'";
$result_combos = mysqli_query($db, $query_combos);
$combinations = [];
while ($row = mysqli_fetch_assoc($result_combos)) {
    $combinations[] = $row;
}

// Extract unique attributes
$unique_colors = [];
$unique_storages = []; // Associative: size => {price, discount}
$unique_connectivities = [];

foreach ($combinations as $combo) {
    if (!in_array($combo['warna'], $unique_colors)) {
        $unique_colors[] = $combo['warna'];
    }

    // Store logic for storage prices might vary per color in complex systems, 
    // but here we simplify or take the first occurrence if inconsistent.
    if (!isset($unique_storages[$combo['penyimpanan']])) {
        $unique_storages[$combo['penyimpanan']] = [
            'size' => $combo['penyimpanan'], // Tambahkan ini
            'harga' => $combo['harga'],
            'harga_diskon' => $combo['harga_diskon']
        ];
    }

    if (!in_array($combo['konektivitas'], $unique_connectivities)) {
        $unique_connectivities[] = $combo['konektivitas'];
    }
}

// Prepare initial data for JavaScript
$initialData = [
    'colors' => [],
    'storages' => array_values($unique_storages),
    'connectivities' => $unique_connectivities,
    'stocks' => []
];

foreach ($images_by_color as $warna => $img_data) {
    $photos = json_decode($img_data['foto_produk'], true) ?? [];
    $initialData['colors'][] = [
        'nama' => $warna,
        'thumbnail' => $img_data['foto_thumbnail'],
        'images' => $photos
    ];
}

foreach ($combinations as $c) {
    $key = $c['warna'] . '|' . $c['penyimpanan'] . '|' . $c['konektivitas'];
    $initialData['stocks'][$key] = $c['jumlah_stok'];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk iPhone - <?php echo htmlspecialchars($product['nama_produk']); ?></title>
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
        textarea.form-control {
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
        textarea.form-control:focus {
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
        .color-option,
        .storage-option,
        .connectivity-option {
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
                <h2><i class="fas fa-edit"></i> Edit Produk iPhone</h2>
            </div>
            <div class="card-body">
                <form id="editIphoneForm" action="api/api-edit-iphone.php" method="POST" enctype="multipart/form-data" autocomplete="off">
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
                            <input type="text" class="form-control" name="kategori" value="<?php echo htmlspecialchars($product['kategori'] ?? ''); ?>" placeholder="Contoh: iPhone 15, iPhone 15 Pro, iPhone 14" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi Produk</label>
                            <textarea class="form-control" name="deskripsi_produk" rows="4" placeholder="Masukkan deskripsi lengkap produk..."><?php echo htmlspecialchars($product['deskripsi_produk']); ?></textarea>
                        </div>
                    </div>

                    <!-- Warna dengan Gambar -->
                    <div class="form-section">
                        <h4><i class="fas fa-palette"></i> Varian Warna & Gambar</h4>
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

                    <!-- Penyimpanan dengan Harga -->
                    <div class="form-section">
                        <h4><i class="fas fa-hdd"></i> Penyimpanan & Harga Dasar</h4>

                        <div id="storagesContainer">
                            <!-- Penyimpanan akan di-generate oleh Javascript -->
                        </div>

                        <button type="button" class="add-option-btn" onclick="addStorage()">
                            <i class="fas fa-plus"></i> Tambah Penyimpanan Lain
                        </button>
                    </div>

                    <!-- Konektivitas -->
                    <div class="form-section">
                        <h4><i class="fas fa-wifi"></i> Konektivitas</h4>

                        <div id="connectivitiesContainer">
                            <!-- Konektivitas akan di-generate oleh Javascript -->
                        </div>

                        <button type="button" class="add-option-btn" onclick="addConnectivity()">
                            <i class="fas fa-plus"></i> Tambah Konektivitas Lain
                        </button>
                    </div>

                    <!-- Tabel Kombinasi & Stok -->
                    <div class="form-section">
                        <h4><i class="fas fa-table"></i> Stok Kombinasi Variabel</h4>
                        <div class="alert-info">
                            <i class="fas fa-info-circle"></i>
                            Stok akan di-update otomatis dari data yang ada. Ubah jumlah stok jika ingin melakukan update.
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
                        <a href="iphone.php" class="btn-back">
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

    <!-- Pass Existing Combinations to JS -->
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

        // Form Submission
        document.getElementById('editIphoneForm').addEventListener('submit', function(e) {
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
                        window.location.href = 'iphone.php';
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
                               placeholder="Nama Warna (Contoh: Midnight)" required value="${data ? data.nama : ''}" onchange="generateCombinations()">
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

        // Penyimpanan
        function addStorage(data = null) {
            const container = document.getElementById('storagesContainer');
            const newIndex = storageCount;

            const newStorage = document.createElement('div');
            newStorage.className = 'storage-option';
            newStorage.dataset.storageIndex = newIndex;

            // Debug: console.log data untuk memastikan struktur benar
            console.log('Storage data:', data);

            newStorage.innerHTML = `
        <div class="form-row">
            <div class="form-col col-3">
                <label class="form-label">Ukuran</label>
                <input type="text" class="form-control" name="penyimpanan[${newIndex}][size]" 
                       placeholder="Ukuran (Contoh: 128GB)" required value="${data && data.size ? data.size : ''}" onchange="generateCombinations()">
            </div>
            <div class="form-col col-4">
                <label class="form-label">Harga Normal <span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="penyimpanan[${newIndex}][harga]" 
                       placeholder="Harga" min="0" required value="${data && data.harga ? data.harga : ''}" onchange="generateCombinations()">
            </div>
            <div class="form-col col-4">
                <label class="form-label">Harga Diskon</label>
                <input type="number" class="form-control" name="penyimpanan[${newIndex}][harga_diskon]" 
                       placeholder="Diskon (opsional)" min="0" value="${data && data.harga_diskon ? data.harga_diskon : ''}" onchange="generateCombinations()">
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

        // Konektivitas
        function addConnectivity(data = null) {
            const container = document.getElementById('connectivitiesContainer');
            const newIndex = connectivityCount;

            const newConnectivity = document.createElement('div');
            newConnectivity.className = 'connectivity-option';
            newConnectivity.dataset.connectivityIndex = newIndex;
            newConnectivity.innerHTML = `
                <input type="text" class="form-control" name="konektivitas[${newIndex}]" 
                       placeholder="Tipe Konektivitas (Contoh: Wi-Fi)" required value="${data ? data : ''}" onchange="generateCombinations()">
                <button type="button" class="btn-danger-sm" onclick="removeConnectivity(${newIndex})">
                    ×
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
        // Generate Combinations
        function generateCombinations() {
            const container = document.getElementById('combinationsContainer');
            const totalContainer = document.getElementById('totalCombinations');

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

                // Debug: console.log untuk melihat nilai
                console.log('Storage inputs:', {
                    size: sizeInput ? sizeInput.value : 'null',
                    harga: hargaInput ? hargaInput.value : 'null',
                    diskon: diskonInput ? diskonInput.value : 'null'
                });

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
                if (val) connectivities.push(val);
            });

            // Debug: Tampilkan data yang dikumpulkan
            console.log('Collected data:', {
                colors,
                storages,
                connectivities
            });

            // Generate table
            const totalCombinations = colors.length * storages.length * connectivities.length;
            totalContainer.innerHTML =
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
                                <div style="font-weight: bold; color: #28a745;">Rp ${parseInt(storage.harga).toLocaleString('id-ID')}</div>
                                ${storage.harga_diskon ? `<small style="color: #dc3545; text-decoration: line-through;">Diskon: Rp ${parseInt(storage.harga_diskon).toLocaleString('id-ID')}</small>` : ''}
                                
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

        // Listen for input changes to update combinations
        document.addEventListener('input', function(e) {
            if (e.target.matches('input[name*="[nama]"], input[name*="[size]"], input[name*="[harga]"], input[name*="konektivitas"]')) {
                if (!e.target.name.includes('jumlah_stok')) {
                    generateCombinations();
                }
            }
        });
    </script>
</body>

</html>