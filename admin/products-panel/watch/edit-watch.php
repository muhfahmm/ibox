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
    header('Location: watch.php');
    exit();
}

$product_id = mysqli_real_escape_string($db, $_GET['id']);
$product = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM admin_produk_watch WHERE id = '$product_id'"));

if (!$product) {
    header('Location: watch.php?error=not_found');
    exit();
}

// Fetch Existing Data
$images_query = mysqli_query($db, "SELECT * FROM admin_produk_watch_gambar WHERE produk_id='$product_id'");
$colors_data = [];
while($row = mysqli_fetch_assoc($images_query)) {
    $colors_data[] = [
        'nama' => $row['warna_case'],
        'thumbnail' => $row['foto_thumbnail'],
        'images' => json_decode($row['foto_produk'], true) ?? []
    ];
}

$combinations_query = mysqli_query($db, "SELECT * FROM admin_produk_watch_kombinasi WHERE produk_id='$product_id'");
$combinations = mysqli_fetch_all($combinations_query, MYSQLI_ASSOC);

$sizes_map = [];
$connections_map = [];
$materials_map = [];
$stocks_map = [];

foreach($combinations as $c) {
    if (!isset($sizes_map[$c['ukuran_case']])) {
        $sizes_map[$c['ukuran_case']] = true;
    }
    if (!isset($connections_map[$c['tipe_koneksi']])) {
        $connections_map[$c['tipe_koneksi']] = true;
    }
    if (!isset($materials_map[$c['material']])) {
        $materials_map[$c['material']] = true;
    }
    
    // Create a key for stock lookup
    $key = $c['warna_case'] . '|' . $c['ukuran_case'] . '|' . $c['tipe_koneksi'] . '|' . $c['material'];
    $stocks_map[$key] = $c['jumlah_stok'];
}

$initialData = [
    'colors' => $colors_data,
    'sizes' => array_keys($sizes_map),
    'connections' => array_keys($connections_map),
    'materials' => array_keys($materials_map),
    'stocks' => $stocks_map
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk Watch - <?php echo htmlspecialchars($product['nama_produk']); ?></title>
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
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.25);
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
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
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
        .color-option, .size-option, .connection-option, .material-option {
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
                <h2><i class="fas fa-edit"></i> Edit Produk Apple Watch</h2>
            </div>
            <div class="card-body">
                <form id="editWatchForm" action="api/api-edit-watch.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                    
                    <!-- Informasi Produk -->
                    <div class="form-section">
                        <h4><i class="fas fa-info-circle"></i> Informasi Produk</h4>
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_produk" value="<?php echo htmlspecialchars($product['nama_produk']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Seri</label>
                            <input type="text" class="form-control" name="seri" value="<?php echo htmlspecialchars($product['seri']); ?>" placeholder="Contoh: Series 9, Ultra 2">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Deskripsi Produk</label>
                            <textarea class="form-control" name="deskripsi_produk" rows="4" placeholder="Masukkan deskripsi lengkap produk..."><?php echo htmlspecialchars($product['deskripsi_produk']); ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Warna dengan Gambar -->
                    <div class="form-section">
                        <h4><i class="fas fa-palette"></i> Warna Case</h4>
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
                    
                    <!-- Ukuran Case -->
                    <div class="form-section">
                        <h4><i class="fas fa-ruler"></i> Ukuran Case</h4>
                        
                        <div id="sizesContainer">
                            <!-- Ukuran akan di-generate oleh Javascript -->
                        </div>
                        
                        <button type="button" class="add-option-btn" onclick="addSize()">
                            <i class="fas fa-plus"></i> Tambah Ukuran Lain
                        </button>
                    </div>
                    
                    <!-- Tipe Koneksi -->
                    <div class="form-section">
                        <h4><i class="fas fa-wifi"></i> Tipe Koneksi</h4>
                        
                        <div id="connectionsContainer">
                            <!-- Tipe koneksi akan di-generate oleh Javascript -->
                        </div>
                        
                        <button type="button" class="add-option-btn" onclick="addConnection()">
                            <i class="fas fa-plus"></i> Tambah Tipe Koneksi Lain
                        </button>
                    </div>
                    
                    <!-- Material -->
                    <div class="form-section">
                        <h4><i class="fas fa-gem"></i> Material</h4>
                        
                        <div id="materialsContainer">
                            <!-- Material akan di-generate oleh Javascript -->
                        </div>
                        
                        <button type="button" class="add-option-btn" onclick="addMaterial()">
                            <i class="fas fa-plus"></i> Tambah Material Lain
                        </button>
                    </div>
                    
                    <!-- Tabel Kombinasi & Stok -->
                    <div class="form-section">
                        <h4><i class="fas fa-table"></i> Kombinasi, Harga & Stok</h4>
                        <div class="alert-info">
                            <i class="fas fa-info-circle"></i>
                            Stok dan harga akan di-update otomatis dari data yang ada. Ubah jika ingin melakukan update.
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
                        <a href="watch.php" class="btn-back">
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
        let sizeCount = 0;
        let connectionCount = 0;
        let materialCount = 0;
        
        // Initialize Form
        window.addEventListener('DOMContentLoaded', () => {
            // Colors
            if (initialData.colors && initialData.colors.length > 0) {
                initialData.colors.forEach(color => addColor(color));
            } else {
                addColor(); // Default empty
            }
            
            // Sizes
            if (initialData.sizes && initialData.sizes.length > 0) {
                initialData.sizes.forEach(size => addSize(size));
            } else {
                addSize();
            }
            
            // Connections
            if (initialData.connections && initialData.connections.length > 0) {
                initialData.connections.forEach(conn => addConnection(conn));
            } else {
                addConnection();
            }
            
            // Materials
            if (initialData.materials && initialData.materials.length > 0) {
                initialData.materials.forEach(mat => addMaterial(mat));
            } else {
                addMaterial();
            }
            
            // Generate Combinations with slight delay to ensure DOM is ready
            setTimeout(() => {
                generateCombinations();
            }, 100);
        });

        // Form Submission
        document.getElementById('editWatchForm').addEventListener('submit', function(e) {
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

            fetch('api/api-edit-watch.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Produk berhasil diperbarui!');
                    window.location.href = 'view-watch.php?id=<?php echo $product_id; ?>';
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
                        <input type="hidden" name="warna_case[${newIndex}][existing_thumbnail]" value="${data.thumbnail}">
                    `;
                }
                
                if (data.images && data.images.length > 0) {
                    productImagesPreview = `<div class="preview-container">`;
                    data.images.forEach(img => {
                        productImagesPreview += `
                            <div class="preview-item">
                                <img src="${uploadPath}${img}" alt="Product Image">
                                <input type="hidden" name="warna_case[${newIndex}][existing_images][]" value="${img}">
                            </div>
                        `;
                    });
                    productImagesPreview += `</div>`;
                }
            }
            
            newColor.innerHTML = `
                <div class="form-row">
                    <div class="form-col col-3">
                        <label class="form-label">Nama Warna Case</label>
                        <input type="text" class="form-control" name="warna_case[${newIndex}][nama]" 
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
                                           name="warna_case[${newIndex}][thumbnail]" 
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
                                           name="warna_case[${newIndex}][product_images][]" 
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
        
        // Ukuran
        function addSize(data = null) {
            const container = document.getElementById('sizesContainer');
            const newIndex = sizeCount;
            
            const newSize = document.createElement('div');
            newSize.className = 'size-option';
            newSize.dataset.sizeIndex = newIndex;
            newSize.innerHTML = `
                <input type="text" class="form-control" name="ukuran_case[${newIndex}]" 
                       placeholder="Ukuran Case (Contoh: 41mm, 45mm)" required value="${data ? data : ''}" onchange="generateCombinations()">
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
                alert('Minimal harus ada satu ukuran case');
                return;
            }
            
            const sizeElement = document.querySelector(`.size-option[data-size-index="${index}"]`);
            if (sizeElement) {
                sizeElement.remove();
                generateCombinations();
            }
        }
        
        // Tipe Koneksi
        function addConnection(data = null) {
            const container = document.getElementById('connectionsContainer');
            const newIndex = connectionCount;
            
            const newConnection = document.createElement('div');
            newConnection.className = 'connection-option';
            newConnection.dataset.connectionIndex = newIndex;
            newConnection.innerHTML = `
                <input type="text" class="form-control" name="tipe_koneksi[${newIndex}]" 
                       placeholder="Tipe Koneksi (Contoh: GPS, GPS + Cellular)" required value="${data ? data : ''}" onchange="generateCombinations()">
                <button type="button" class="btn-danger-sm" onclick="removeConnection(${newIndex})">
                    ×
                </button>
            `;
            
            container.appendChild(newConnection);
            connectionCount++;
            generateCombinations();
        }
        
        function removeConnection(index) {
            const connElements = document.querySelectorAll('.connection-option');
            if (connElements.length <= 1) {
                alert('Minimal harus ada satu tipe koneksi');
                return;
            }
            
            const element = document.querySelector(`.connection-option[data-connection-index="${index}"]`);
            if (element) {
                element.remove();
                generateCombinations();
            }
        }
        
        // Material
        function addMaterial(data = null) {
            const container = document.getElementById('materialsContainer');
            const newIndex = materialCount;
            
            const newMaterial = document.createElement('div');
            newMaterial.className = 'material-option';
            newMaterial.dataset.materialIndex = newIndex;
            newMaterial.innerHTML = `
                <input type="text" class="form-control" name="material[${newIndex}]" 
                       placeholder="Material (Contoh: Aluminum, Stainless Steel)" required value="${data ? data : ''}" onchange="generateCombinations()">
                <button type="button" class="btn-danger-sm" onclick="removeMaterial(${newIndex})">
                    ×
                </button>
            `;
            
            container.appendChild(newMaterial);
            materialCount++;
            generateCombinations();
        }
        
        function removeMaterial(index) {
            const materialElements = document.querySelectorAll('.material-option');
            if (materialElements.length <= 1) {
                alert('Minimal harus ada satu material');
                return;
            }
            
            const element = document.querySelector(`.material-option[data-material-index="${index}"]`);
            if (element) {
                element.remove();
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

        // Generate Combinations
        function generateCombinations() {
            const container = document.getElementById('combinationsContainer');
            const totalContainer = document.getElementById('totalCombinations');
            
            // Collect data from DOM inputs
            const colors = [];
            document.querySelectorAll('.color-option input[name*="[nama]"]').forEach(input => {
                if (input.value.trim()) colors.push(input.value.trim());
            });
            
            const sizes = [];
            document.querySelectorAll('.size-option input').forEach(input => {
                const val = input.value.trim();
                if (val) sizes.push(val);
            });
            
            const connections = [];
            document.querySelectorAll('.connection-option input').forEach(input => {
                const val = input.value.trim();
                if (val) connections.push(val);
            });
            
            const materials = [];
            document.querySelectorAll('.material-option input').forEach(input => {
                const val = input.value.trim();
                if (val) materials.push(val);
            });
            
            // Generate table
            const totalCombinations = colors.length * sizes.length * connections.length * materials.length;
            totalContainer.innerHTML = 
                `Total Kombinasi: <strong>${totalCombinations}</strong> (${colors.length} warna × ${sizes.length} ukuran × ${connections.length} koneksi × ${materials.length} material)`;
            
            if (totalCombinations > 0) {
                let tableHTML = `
                    <table class="combination-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Warna Case</th>
                                <th>Ukuran Case</th>
                                <th>Tipe Koneksi</th>
                                <th>Material</th>
                                <th>Harga</th>
                                <th>Diskon</th>
                                <th>Jumlah Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                
                let counter = 1;
                colors.forEach((color, cIdx) => {
                    sizes.forEach((size, sIdx) => {
                        connections.forEach((connection, kIdx) => {
                            materials.forEach((material, mIdx) => {
                                const uniqueId = `${cIdx}_${sIdx}_${kIdx}_${mIdx}`;
                                
                                // Try to find existing stock
                                const stockKey = `${color}|${size}|${connection}|${material}`;
                                const existingStock = initialData.stocks[stockKey] !== undefined ? initialData.stocks[stockKey] : 0;
                                
                                tableHTML += `
                                    <tr>
                                        <td>${counter}</td>
                                        <td>${color}</td>
                                        <td>${size}</td>
                                        <td>${connection}</td>
                                        <td>${material}</td>
                                        <td>
                                            <input type="number" class="form-control" style="width: 120px;" 
                                                   name="combinations[${uniqueId}][harga]" 
                                                   placeholder="Harga" min="0" required>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" style="width: 120px;" 
                                                   name="combinations[${uniqueId}][harga_diskon]" 
                                                   placeholder="Diskon (opsional)" min="0">
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
                });
                
                tableHTML += `</tbody></table>`;
                container.innerHTML = tableHTML;
            } else {
                container.innerHTML = '<div class="text-center p-4 text-muted">Lengkapi data warna, ukuran, koneksi, dan material untuk melihat tabel kombinasi</div>';
            }
        }
        
        // Listen for input changes to update combinations
        document.addEventListener('input', function(e) {
            if (e.target.matches('input[name*="[nama]"], input[name*="ukuran_case"], input[name*="tipe_koneksi"], input[name*="material"]')) {
               if (!e.target.name.includes('jumlah_stok') && !e.target.name.includes('harga')) {
                   generateCombinations();
               }
            }
        });
    </script>
</body>
</html>