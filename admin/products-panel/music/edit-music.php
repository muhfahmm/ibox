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
    header('Location: music.php?error=invalid_id');
    exit();
}

$product_id = mysqli_real_escape_string($db, $_GET['id']);

// Fetch main product data
$query_product = "SELECT * FROM admin_produk_music WHERE id = '$product_id'";
$result_product = mysqli_query($db, $query_product);

if (mysqli_num_rows($result_product) === 0) {
    header('Location: music.php?error=product_not_found');
    exit();
}

$product = mysqli_fetch_assoc($result_product);

// Fetch all color images
$query_images = "SELECT * FROM admin_produk_music_gambar WHERE produk_id = '$product_id'";
$result_images = mysqli_query($db, $query_images);
$color_images = mysqli_fetch_all($result_images, MYSQLI_ASSOC);

// Fetch all combinations
$query_combinations = "SELECT * FROM admin_produk_music_kombinasi WHERE produk_id = '$product_id'";
$result_combinations = mysqli_query($db, $query_combinations);
$combinations = mysqli_fetch_all($result_combinations, MYSQLI_ASSOC);

// Extract unique values from combinations
$unique_colors = [];
$unique_tipes = [];
$unique_konektivitases = [];

foreach ($combinations as $combination) {
    if (!in_array($combination['warna'], $unique_colors)) {
        $unique_colors[] = $combination['warna'];
    }
    if (!in_array($combination['tipe'], $unique_tipes)) {
        $unique_tipes[] = $combination['tipe'];
    }
    if (!in_array($combination['konektivitas'], $unique_konektivitases)) {
        $unique_konektivitases[] = $combination['konektivitas'];
    }
}

// Group combinations by type for easier editing (since price is per combination)
$type_data = [];
foreach ($combinations as $combination) {
    $type = $combination['tipe'];
    if (!isset($type_data[$type])) {
        $type_data[$type] = [
            'harga' => $combination['harga'],
            'harga_diskon' => $combination['harga_diskon']
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk Music - <?php echo htmlspecialchars($product['nama_produk']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Same CSS as add-mac.php */
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
        
        .color-option, .processor-option, .storage-option, .ram-option {
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
        
        .existing-data {
            background: #f0f8ff;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 10px;
            border-left: 4px solid #4a6cf7;
        }
        
        .existing-data strong {
            color: #4a6cf7;
        }
    </style> 
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-edit me-2"></i> Edit Produk Music</h2>
            </div>
            <div class="card-body">
                <form id="editMusicForm" action="api/api-edit-music.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                    
                    <!-- Informasi Produk -->
                    <div class="form-section">
                        <h4><i class="fas fa-info-circle me-2"></i> Informasi Produk</h4>
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_produk" required 
                                   value="<?php echo htmlspecialchars($product['nama_produk']); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Kategori Music</label>
                            <select class="form-select" name="kategori">
                                <option value="airpods" <?php echo $product['kategori'] == 'airpods' ? 'selected' : ''; ?>>AirPods</option>
                                <option value="homepod" <?php echo $product['kategori'] == 'homepod' ? 'selected' : ''; ?>>HomePod</option>
                                <option value="beats" <?php echo $product['kategori'] == 'beats' ? 'selected' : ''; ?>>Beats</option>
                                <option value="aksesoris-audio" <?php echo $product['kategori'] == 'aksesoris-audio' ? 'selected' : ''; ?>>Aksesoris Audio</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Deskripsi Produk</label>
                            <textarea class="form-control" name="deskripsi_produk" rows="4"><?php echo htmlspecialchars($product['deskripsi_produk']); ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Warna dengan Gambar -->
                    <div class="form-section">
                        <h4><i class="fas fa-palette me-2"></i> Warna Produk</h4>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Warna yang sudah ada: 
                            <?php foreach($unique_colors as $color): ?>
                                <span class="badge bg-secondary"><?php echo htmlspecialchars($color); ?></span>
                            <?php endforeach; ?>
                        </div>
                        
                        <div id="colorsContainer">
                            <!-- Existing colors will be loaded here -->
                        </div>
                        
                        <button type="button" class="add-option-btn" onclick="addColor()">
                            <i class="fas fa-plus"></i> Tambah Warna Baru
                        </button>
                    </div>
                    
                    <!-- Tipe -->
                    <div class="form-section">
                        <h4><i class="fas fa-headphones me-2"></i> Tipe</h4>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Tipe yang sudah ada: 
                            <?php foreach($unique_tipes as $tipe): ?>
                                <span class="badge bg-secondary"><?php echo htmlspecialchars($tipe); ?></span>
                            <?php endforeach; ?>
                        </div>
                        
                        <div id="tipesContainer">
                            <!-- Existing types will be loaded here -->
                        </div>
                        
                        <button type="button" class="add-option-btn" onclick="addTipe()">
                            <i class="fas fa-plus"></i> Tambah Tipe Baru
                        </button>
                    </div>
                    
                    <!-- Konektivitas -->
                    <div class="form-section">
                        <h4><i class="fas fa-bluetooth me-2"></i> Konektivitas</h4>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Konektivitas yang sudah ada: 
                            <?php foreach($unique_konektivitases as $konektivitas): ?>
                                <span class="badge bg-secondary"><?php echo htmlspecialchars($konektivitas); ?></span>
                            <?php endforeach; ?>
                        </div>
                        
                        <div id="konektivitasesContainer">
                            <!-- Existing konektivitases will be loaded here -->
                        </div>
                        
                        <button type="button" class="add-option-btn" onclick="addKonektivitas()">
                            <i class="fas fa-plus"></i> Tambah Konektivitas Baru
                        </button>
                    </div>
                    
                    <!-- Tabel Kombinasi & Stok -->
                    <div class="form-section">
                        <h4><i class="fas fa-table me-2"></i> Kombinasi & Stok</h4>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Sistem akan membuat semua kombinasi dari Warna, Tipe, dan Konektivitas. 
                            Untuk kombinasi yang sudah ada, stok akan ditampilkan.
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
                        <a href="view-music.php?id=<?php echo $product_id; ?>" class="btn-back">
                            <i class="fas fa-arrow-left"></i> Kembali ke Detail
                        </a>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save me-2"></i> Update Produk Music
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let colorCount = 0;
        let tipeCount = 0;
        let konektivitasCount = 0;
        
        // Initialize with existing data
        document.addEventListener('DOMContentLoaded', function() {
            // Load existing colors
            <?php foreach($color_images as $color): ?>
                addExistingColor(
                    '<?php echo htmlspecialchars($color['warna'], ENT_QUOTES); ?>',
                    '<?php echo htmlspecialchars($color['foto_thumbnail']); ?>'
                );
            <?php endforeach; ?>
            
            // Load existing tipe
            <?php foreach($unique_tipes as $tipe): ?>
                addExistingTipe('<?php echo htmlspecialchars($tipe, ENT_QUOTES); ?>');
            <?php endforeach; ?>
            
            // Load existing konektivitases
            <?php foreach($unique_konektivitases as $konektivitas): ?>
                addExistingKonektivitas('<?php echo htmlspecialchars($konektivitas, ENT_QUOTES); ?>');
            <?php endforeach; ?>
            
            // Generate combinations
            generateCombinations();
        });
        
        // Color functions
        function addExistingColor(colorName, thumbnail) {
            const container = document.getElementById('colorsContainer');
            const newIndex = colorCount;
            
            const newColor = document.createElement('div');
            newColor.className = 'color-option';
            newColor.dataset.colorIndex = newIndex;
            newColor.innerHTML = `
                <div class="row g-3 w-100">
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="warna[${newIndex}][nama]" 
                               value="${colorName}" required>
                        <input type="hidden" name="warna[${newIndex}][existing]" value="1">
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Thumbnail Warna</label>
                                <div class="file-upload" onclick="document.getElementById('thumbnail-${newIndex}').click()">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p class="mb-1">Upload thumbnail baru (opsional)</p>
                                    <small class="text-muted">Max: 2MB</small>
                                    <input type="file" id="thumbnail-${newIndex}" 
                                           name="warna[${newIndex}][thumbnail]" 
                                           accept="image/*" style="display: none;" 
                                           onchange="previewThumbnail(${newIndex}, this)">
                                </div>
                                <div class="preview-container">
                                    ${thumbnail ? `
                                        <div class="existing-data">
                                            <strong>Thumbnail saat ini:</strong> ${thumbnail}<br>
                                            <input type="hidden" name="warna[${newIndex}][old_thumbnail]" value="${thumbnail}">
                                        </div>
                                    ` : ''}
                                    <div id="thumbnailPreview-${newIndex}" class="preview-item" style="display: none;">
                                        <img id="thumbnailImg-${newIndex}" src="" alt="Thumbnail Preview">
                                        <button type="button" class="remove-btn" onclick="removeThumbnail(${newIndex})">&times;</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Foto Produk Baru (opsional)</label>
                                <div class="file-upload" onclick="document.getElementById('productImages-${newIndex}').click()">
                                    <i class="fas fa-images"></i>
                                    <p class="mb-1">Upload foto produk baru</p>
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
        }
        
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
                               placeholder="Nama Warna Baru" required>
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
        
        // Tipe functions
        function addExistingTipe(tipe) {
            const container = document.getElementById('tipesContainer');
            const newIndex = tipeCount;
            
            const newTipe = document.createElement('div');
            newTipe.className = 'tipe-option';
            newTipe.dataset.tipeIndex = newIndex;
            newTipe.innerHTML = `
                <input type="text" class="form-control" name="tipe[${newIndex}]" 
                       value="${tipe}" required>
                <input type="hidden" name="tipe[${newIndex}][existing]" value="1">
                <button type="button" class="btn-danger-sm" onclick="removeTipe(${newIndex})">
                    &times;
                </button>
            `;
            
            container.appendChild(newTipe);
            tipeCount++;
        }
        
        function addTipe() {
            const container = document.getElementById('tipesContainer');
            const newIndex = tipeCount;
            
            const newTipe = document.createElement('div');
            newTipe.className = 'tipe-option';
            newTipe.dataset.tipeIndex = newIndex;
            newTipe.innerHTML = `
                <input type="text" class="form-control" name="tipe[${newIndex}]" 
                       placeholder="Tipe Baru (Contoh: Pro, Max, Generation)" required>
                <button type="button" class="btn-danger-sm" onclick="removeTipe(${newIndex})">
                    &times;
                </button>
            `;
            
            container.appendChild(newTipe);
            tipeCount++;
            generateCombinations();
        }
        
        function removeTipe(index) {
            if (tipeCount <= 1) {
                alert('Minimal harus ada satu tipe');
                return;
            }
            
            const tipeElement = document.querySelector(`.tipe-option[data-tipe-index="${index}"]`);
            if (tipeElement) {
                tipeElement.remove();
                tipeCount--;
                generateCombinations();
            }
        }
        
        // Konektivitas functions
        function addExistingKonektivitas(konektivitas) {
            const container = document.getElementById('konektivitasesContainer');
            const newIndex = konektivitasCount;
            
            const newKonektivitas = document.createElement('div');
            newKonektivitas.className = 'konektivitas-option';
            newKonektivitas.dataset.konektivitasIndex = newIndex;
            newKonektivitas.innerHTML = `
                <input type="text" class="form-control" name="konektivitas[${newIndex}]" 
                       value="${konektivitas}" required>
                <input type="hidden" name="konektivitas[${newIndex}][existing]" value="1">
                <button type="button" class="btn-danger-sm" onclick="removeKonektivitas(${newIndex})">
                    &times;
                </button>
            `;
            
            container.appendChild(newKonektivitas);
            konektivitasCount++;
        }
        
        function addKonektivitas() {
            const container = document.getElementById('konektivitasesContainer');
            const newIndex = konektivitasCount;
            
            const newKonektivitas = document.createElement('div');
            newKonektivitas.className = 'konektivitas-option';
            newKonektivitas.dataset.konektivitasIndex = newIndex;
            newKonektivitas.innerHTML = `
                <input type="text" class="form-control" name="konektivitas[${newIndex}]" 
                       placeholder="Konektivitas Baru (Contoh: Bluetooth, Lightning, USB-C)" required>
                <button type="button" class="btn-danger-sm" onclick="removeKonektivitas(${newIndex})">
                    &times;
                </button>
            `;
            
            container.appendChild(newKonektivitas);
            konektivitasCount++;
            generateCombinations();
        }
        
        function removeKonektivitas(index) {
            if (konektivitasCount <= 1) {
                alert('Minimal harus ada satu konektivitas');
                return;
            }
            
            const konektivitasElement = document.querySelector(`.konektivitas-option[data-konektivitas-index="${index}"]`);
            if (konektivitasElement) {
                konektivitasElement.remove();
                konektivitasCount--;
                generateCombinations();
            }
        }
        
        // Generate Combinations for edit
        function generateCombinations() {
            const container = document.getElementById('combinationsContainer');
            
            // Collect data
            const colors = [];
            document.querySelectorAll('.color-option input[name$="[nama]"]').forEach(input => {
                colors.push(input.value);
            });
            
            const tipes = [];
            document.querySelectorAll('.tipe-option input').forEach(input => {
                tipes.push(input.value);
            });
            
            const konektivitases = [];
            document.querySelectorAll('.konektivitas-option input').forEach(input => {
                konektivitases.push(input.value);
            });
            
            // Calculate total combinations
            const totalCombinations = colors.length * tipes.length * konektivitases.length;
            document.getElementById('totalCombinations').innerHTML = 
                `Total Kombinasi: <strong>${totalCombinations}</strong> (${colors.length} warna × ${tipes.length} tipe × ${konektivitases.length} konektivitas)`;
            
            // Generate table
            if (totalCombinations > 0) {
                let tableHTML = `
                    <table class="combination-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Warna</th>
                                <th>Tipe</th>
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
                    tipes.forEach((tipe, tipeIndex) => {
                        konektivitases.forEach((konektivitas, konektivitasIndex) => {
                            const combinationId = `${colorIndex}-${tipeIndex}-${konektivitasIndex}`;
                            
                            // Find existing data for this combination
                            let existingHarga = 0;
                            let existingHargaDiskon = null;
                            let existingStock = 0;
                            
                            <?php foreach($combinations as $combination): ?>
                                if ('<?php echo $combination['warna']; ?>' === color &&
                                    '<?php echo $combination['tipe']; ?>' === tipe &&
                                    '<?php echo $combination['konektivitas']; ?>' === konektivitas) {
                                    existingHarga = <?php echo $combination['harga']; ?>;
                                    existingHargaDiskon = <?php echo $combination['harga_diskon'] ?: 'null'; ?>;
                                    existingStock = <?php echo $combination['jumlah_stok']; ?>;
                                }
                            <?php endforeach; ?>
                            
                            tableHTML += `
                                <tr>
                                    <td>${counter}</td>
                                    <td><strong>${color}</strong></td>
                                    <td>${tipe}</td>
                                    <td>${konektivitas}</td>
                                    <td>
                                        <input type="hidden" name="combinations[${combinationId}][warna]" value="${color}">
                                        <input type="hidden" name="combinations[${combinationId}][tipe]" value="${tipe}">
                                        <input type="hidden" name="combinations[${combinationId}][konektivitas]" value="${konektivitas}">
                                        <input type="number" class="form-control" 
                                               name="combinations[${combinationId}][harga]" 
                                               value="${existingHarga}" min="0" required 
                                               style="width: 150px;">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" 
                                               name="combinations[${combinationId}][harga_diskon]" 
                                               value="${existingHargaDiskon || ''}" min="0" 
                                               style="width: 150px;">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" 
                                               name="combinations[${combinationId}][jumlah_stok]" 
                                               value="${existingStock}" min="0" required 
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
                container.innerHTML = '<p class="text-muted">Tambahkan minimal satu warna, tipe, dan konektivitas untuk melihat kombinasi.</p>';
            }
        }
        
        // Preview functions (same as edit-mac.php)
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
        document.getElementById('editMusicForm').addEventListener('submit', function(e) {
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
            
            // Validate tipe
            const tipeInputs = document.querySelectorAll('.tipe-option input');
            const validTipes = Array.from(tipeInputs).filter(input => input.value.trim()).length;
            if (validTipes === 0) {
                alert('Minimal satu tipe harus diisi');
                return;
            }
            
            // Validate konektivitas
            const konektivitasInputs = document.querySelectorAll('.konektivitas-option input');
            const validKonektivitases = Array.from(konektivitasInputs).filter(input => input.value.trim()).length;
            if (validKonektivitases === 0) {
                alert('Minimal satu konektivitas harus diisi');
                return;
            }
            
            // Validate combination prices
            const priceInputs = document.querySelectorAll('input[name$="[harga]"]');
            let invalidPrice = false;
            priceInputs.forEach(input => {
                if (parseFloat(input.value) <= 0) {
                    invalidPrice = true;
                    input.style.borderColor = 'red';
                } else {
                    input.style.borderColor = '';
                }
            });
            
            if (invalidPrice) {
                alert('Semua harga harus lebih dari 0');
                return;
            }
            
            // Validate discount prices
            const discountInputs = document.querySelectorAll('input[name$="[harga_diskon]"]');
            discountInputs.forEach(input => {
                if (input.value) {
                    const row = input.closest('tr');
                    const hargaInput = row.querySelector('input[name$="[harga]"]');
                    const harga = parseFloat(hargaInput.value);
                    const diskon = parseFloat(input.value);
                    
                    if (diskon >= harga) {
                        alert('Harga diskon harus lebih rendah dari harga normal');
                        invalidPrice = true;
                        input.style.borderColor = 'red';
                    } else {
                        input.style.borderColor = '';
                    }
                }
            });
            
            if (invalidPrice) {
                return;
            }
            
            // Show loading
            const submitBtn = document.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Menyimpan...';
            submitBtn.disabled = true;
            
            // Submit form
            const formData = new FormData(this);
            
            fetch('api/api-edit-music.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Produk Music berhasil diperbarui!');
                    window.location.href = 'view-music.php?id=<?php echo $product_id; ?>';
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
        
        function reindexColors() {
            const colorOptions = document.querySelectorAll('.color-option');
            colorOptions.forEach((option, index) => {
                option.dataset.colorIndex = index;
                
                // Update input names
                const colorNameInput = option.querySelector('input[name$="[nama]"]');
                const thumbnailInput = option.querySelector('input[name$="[thumbnail]"]');
                const productImagesInput = option.querySelector('input[name$="[product_images][]"]');
                
                if (colorNameInput) {
                    colorNameInput.name = `warna[${index}][nama]`;
                }
                if (thumbnailInput) {
                    thumbnailInput.name = `warna[${index}][thumbnail]`;
                    thumbnailInput.id = `thumbnail-${index}`;
                }
                if (productImagesInput) {
                    productImagesInput.name = `warna[${index}][product_images][]`;
                    productImagesInput.id = `productImages-${index}`;
                }
            });
        }
    </script>
</body>
</html>