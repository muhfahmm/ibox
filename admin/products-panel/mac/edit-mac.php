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
    header('Location: mac.php?error=invalid_id');
    exit();
}

$product_id = mysqli_real_escape_string($db, $_GET['id']);

// Fetch main product data
$query_product = "SELECT * FROM admin_produk_mac WHERE id = '$product_id'";
$result_product = mysqli_query($db, $query_product);

if (mysqli_num_rows($result_product) === 0) {
    header('Location: mac.php?error=product_not_found');
    exit();
}

$product = mysqli_fetch_assoc($result_product);

// Fetch all color images
$query_images = "SELECT * FROM admin_produk_mac_gambar WHERE produk_id = '$product_id'";
$result_images = mysqli_query($db, $query_images);
$color_images = mysqli_fetch_all($result_images, MYSQLI_ASSOC);

// Fetch all combinations
$query_combinations = "SELECT * FROM admin_produk_mac_kombinasi WHERE produk_id = '$product_id'";
$result_combinations = mysqli_query($db, $query_combinations);
$combinations = mysqli_fetch_all($result_combinations, MYSQLI_ASSOC);

// Extract unique values from combinations
$unique_colors = [];
$unique_processors = [];
$unique_storages = [];
$unique_rams = [];

foreach ($combinations as $combination) {
    if (!in_array($combination['warna'], $unique_colors)) {
        $unique_colors[] = $combination['warna'];
    }
    if (!in_array($combination['processor'], $unique_processors)) {
        $unique_processors[] = $combination['processor'];
    }
    if (!in_array($combination['penyimpanan'], $unique_storages)) {
        $unique_storages[] = $combination['penyimpanan'];
    }
    if (!in_array($combination['ram'], $unique_rams)) {
        $unique_rams[] = $combination['ram'];
    }
}

// Group combinations by storage for easier editing
$storage_data = [];
foreach ($combinations as $combination) {
    $storage = $combination['penyimpanan'];
    if (!isset($storage_data[$storage])) {
        $storage_data[$storage] = [
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
    <title>Edit Produk Mac - <?php echo htmlspecialchars($product['nama_produk']); ?></title>
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
                <h2><i class="fas fa-edit me-2"></i> Edit Produk Mac</h2>
            </div>
            <div class="card-body">
                <form id="editMacForm" action="api/api-edit-mac.php" method="POST" enctype="multipart/form-data">
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
                    
                    <!-- Processor -->
                    <div class="form-section">
                        <h4><i class="fas fa-microchip me-2"></i> Processor</h4>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Processor yang sudah ada: 
                            <?php foreach($unique_processors as $processor): ?>
                                <span class="badge bg-secondary"><?php echo htmlspecialchars($processor); ?></span>
                            <?php endforeach; ?>
                        </div>
                        
                        <div id="processorsContainer">
                            <!-- Existing processors will be loaded here -->
                        </div>
                        
                        <button type="button" class="add-option-btn" onclick="addProcessor()">
                            <i class="fas fa-plus"></i> Tambah Processor Baru
                        </button>
                    </div>
                    
                    <!-- Penyimpanan dengan Harga -->
                    <div class="form-section">
                        <h4><i class="fas fa-hdd me-2"></i> Penyimpanan & Harga</h4>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Penyimpanan yang sudah ada: 
                            <?php foreach($unique_storages as $storage): ?>
                                <span class="badge bg-secondary"><?php echo htmlspecialchars($storage); ?></span>
                            <?php endforeach; ?>
                        </div>
                        
                        <div id="storagesContainer">
                            <!-- Existing storages will be loaded here -->
                        </div>
                        
                        <button type="button" class="add-option-btn" onclick="addStorage()">
                            <i class="fas fa-plus"></i> Tambah Penyimpanan Baru
                        </button>
                    </div>
                    
                    <!-- RAM -->
                    <div class="form-section">
                        <h4><i class="fas fa-memory me-2"></i> RAM</h4>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            RAM yang sudah ada: 
                            <?php foreach($unique_rams as $ram): ?>
                                <span class="badge bg-secondary"><?php echo htmlspecialchars($ram); ?></span>
                            <?php endforeach; ?>
                        </div>
                        
                        <div id="ramsContainer">
                            <!-- Existing RAMs will be loaded here -->
                        </div>
                        
                        <button type="button" class="add-option-btn" onclick="addRam()">
                            <i class="fas fa-plus"></i> Tambah RAM Baru
                        </button>
                    </div>
                    
                    <!-- Tabel Kombinasi & Stok -->
                    <div class="form-section">
                        <h4><i class="fas fa-table me-2"></i> Kombinasi & Stok</h4>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Sistem akan membuat semua kombinasi dari Warna, Processor, Penyimpanan, dan RAM. 
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
                        <a href="view-mac.php?id=<?php echo $product_id; ?>" class="btn-back">
                            <i class="fas fa-arrow-left"></i> Kembali ke Detail
                        </a>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save me-2"></i> Update Produk Mac
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let colorCount = 0;
        let processorCount = 0;
        let storageCount = 0;
        let ramCount = 0;
        
        // Initialize with existing data
        document.addEventListener('DOMContentLoaded', function() {
            // Load existing colors
            <?php foreach($color_images as $color): ?>
                addExistingColor(
                    '<?php echo htmlspecialchars($color['warna'], ENT_QUOTES); ?>',
                    '<?php echo htmlspecialchars($color['foto_thumbnail']); ?>'
                );
            <?php endforeach; ?>
            
            // Load existing processors
            <?php foreach($unique_processors as $processor): ?>
                addExistingProcessor('<?php echo htmlspecialchars($processor, ENT_QUOTES); ?>');
            <?php endforeach; ?>
            
            // Load existing storages
            <?php foreach($storage_data as $storage => $data): ?>
                addExistingStorage(
                    '<?php echo htmlspecialchars($storage, ENT_QUOTES); ?>',
                    '<?php echo $data['harga']; ?>',
                    '<?php echo $data['harga_diskon'] ?? ''; ?>'
                );
            <?php endforeach; ?>
            
            // Load existing RAMs
            <?php foreach($unique_rams as $ram): ?>
                addExistingRam('<?php echo htmlspecialchars($ram, ENT_QUOTES); ?>');
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
        
        // Similar functions for processor, storage, and RAM...
        // Processor functions
        function addExistingProcessor(processor) {
            const container = document.getElementById('processorsContainer');
            const newIndex = processorCount;
            
            const newProcessor = document.createElement('div');
            newProcessor.className = 'processor-option';
            newProcessor.dataset.processorIndex = newIndex;
            newProcessor.innerHTML = `
                <input type="text" class="form-control" name="processor[${newIndex}]" 
                       value="${processor}" required>
                <input type="hidden" name="processor[${newIndex}][existing]" value="1">
                <button type="button" class="btn-danger-sm" onclick="removeProcessor(${newIndex})">
                    &times;
                </button>
            `;
            
            container.appendChild(newProcessor);
            processorCount++;
        }
        
        function addProcessor() {
            const container = document.getElementById('processorsContainer');
            const newIndex = processorCount;
            
            const newProcessor = document.createElement('div');
            newProcessor.className = 'processor-option';
            newProcessor.dataset.processorIndex = newIndex;
            newProcessor.innerHTML = `
                <input type="text" class="form-control" name="processor[${newIndex}]" 
                       placeholder="Processor Baru" required>
                <button type="button" class="btn-danger-sm" onclick="removeProcessor(${newIndex})">
                    &times;
                </button>
            `;
            
            container.appendChild(newProcessor);
            processorCount++;
            generateCombinations();
        }
        
        // Storage functions
        function addExistingStorage(storage, harga, harga_diskon) {
            const container = document.getElementById('storagesContainer');
            const newIndex = storageCount;
            
            const newStorage = document.createElement('div');
            newStorage.className = 'storage-option';
            newStorage.dataset.storageIndex = newIndex;
            newStorage.innerHTML = `
                <div class="row g-3 w-100">
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="penyimpanan[${newIndex}][size]" 
                               value="${storage}" required>
                        <input type="hidden" name="penyimpanan[${newIndex}][existing]" value="1">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Harga Normal <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="penyimpanan[${newIndex}][harga]" 
                               value="${harga}" min="0" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Harga Diskon</label>
                        <input type="number" class="form-control" name="penyimpanan[${newIndex}][harga_diskon]" 
                               value="${harga_diskon}" min="0">
                    </div>
                </div>
                <button type="button" class="btn-danger-sm" onclick="removeStorage(${newIndex})">
                    &times;
                </button>
            `;
            
            container.appendChild(newStorage);
            storageCount++;
        }
        
        // RAM functions
        function addExistingRam(ram) {
            const container = document.getElementById('ramsContainer');
            const newIndex = ramCount;
            
            const newRam = document.createElement('div');
            newRam.className = 'ram-option';
            newRam.dataset.ramIndex = newIndex;
            newRam.innerHTML = `
                <input type="text" class="form-control" name="ram[${newIndex}]" 
                       value="${ram}" required>
                <input type="hidden" name="ram[${newIndex}][existing]" value="1">
                <button type="button" class="btn-danger-sm" onclick="removeRam(${newIndex})">
                    &times;
                </button>
            `;
            
            container.appendChild(newRam);
            ramCount++;
        }
        
        // Generate Combinations for edit
        function generateCombinations() {
            const container = document.getElementById('combinationsContainer');
            
            // Collect data
            const colors = [];
            document.querySelectorAll('.color-option input[name$="[nama]"]').forEach(input => {
                colors.push(input.value);
            });
            
            const processors = [];
            document.querySelectorAll('.processor-option input').forEach(input => {
                processors.push(input.value);
            });
            
            const storages = [];
            document.querySelectorAll('.storage-option').forEach(option => {
                const sizeInput = option.querySelector('input[name$="[size]"]');
                const hargaInput = option.querySelector('input[name$="[harga]"]');
                const diskonInput = option.querySelector('input[name$="[harga_diskon]"]');
                
                if (sizeInput && hargaInput) {
                    storages.push({
                        size: sizeInput.value,
                        harga: hargaInput.value,
                        harga_diskon: diskonInput.value
                    });
                }
            });
            
            const rams = [];
            document.querySelectorAll('.ram-option input').forEach(input => {
                rams.push(input.value);
            });
            
            // Calculate total combinations
            const totalCombinations = colors.length * processors.length * storages.length * rams.length;
            document.getElementById('totalCombinations').innerHTML = 
                `Total Kombinasi: <strong>${totalCombinations}</strong> (${colors.length} warna × ${processors.length} processor × ${storages.length} penyimpanan × ${rams.length} RAM)`;
            
            // Generate table
            if (totalCombinations > 0) {
                let tableHTML = `
                    <table class="combination-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Warna</th>
                                <th>Processor</th>
                                <th>Penyimpanan</th>
                                <th>RAM</th>
                                <th>Harga</th>
                                <th>Harga Diskon</th>
                                <th>Jumlah Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                
                let counter = 1;
                colors.forEach((color, colorIndex) => {
                    processors.forEach((processor, processorIndex) => {
                        storages.forEach((storage, storageIndex) => {
                            rams.forEach((ram, ramIndex) => {
                                const combinationId = `${colorIndex}-${processorIndex}-${storageIndex}-${ramIndex}`;
                                
                                // Find existing stock for this combination
                                let existingStock = 0;
                                <?php foreach($combinations as $combination): ?>
                                    if ('<?php echo $combination['warna']; ?>' === color &&
                                        '<?php echo $combination['processor']; ?>' === processor &&
                                        '<?php echo $combination['penyimpanan']; ?>' === storage.size &&
                                        '<?php echo $combination['ram']; ?>' === ram) {
                                        existingStock = <?php echo $combination['jumlah_stok']; ?>;
                                    }
                                <?php endforeach; ?>
                                
                                tableHTML += `
                                    <tr>
                                        <td>${counter}</td>
                                        <td><strong>${color}</strong></td>
                                        <td>${processor}</td>
                                        <td><strong>${storage.size}</strong></td>
                                        <td>${ram}</td>
                                        <td>
                                            <input type="hidden" name="combinations[${combinationId}][warna]" value="${color}">
                                            <input type="hidden" name="combinations[${combinationId}][processor]" value="${processor}">
                                            <input type="hidden" name="combinations[${combinationId}][penyimpanan]" value="${storage.size}">
                                            <input type="hidden" name="combinations[${combinationId}][ram]" value="${ram}">
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
                                                   value="${existingStock}" min="0" required 
                                                   style="width: 100px;">
                                        </td>
                                    </tr>
                                `;
                                counter++;
                            });
                        });
                    });
                });
                
                tableHTML += `
                        </tbody>
                    </table>
                `;
                
                container.innerHTML = tableHTML;
            } else {
                container.innerHTML = '<p class="text-muted">Tambahkan minimal satu warna, processor, penyimpanan, dan RAM untuk melihat kombinasi.</p>';
            }
        }
        
        // Preview functions (same as add-mac.php)
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
        document.getElementById('editMacForm').addEventListener('submit', function(e) {
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
            
            // Validate processors
            const processorInputs = document.querySelectorAll('.processor-option input');
            const validProcessors = Array.from(processorInputs).filter(input => input.value.trim()).length;
            if (validProcessors === 0) {
                alert('Minimal satu processor harus diisi');
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
            
            // Validate RAMs
            const ramInputs = document.querySelectorAll('.ram-option input');
            const validRams = Array.from(ramInputs).filter(input => input.value.trim()).length;
            if (validRams === 0) {
                alert('Minimal satu RAM harus diisi');
                return;
            }
            
            // Show loading
            const submitBtn = document.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Menyimpan...';
            submitBtn.disabled = true;
            
            // Submit form
            const formData = new FormData(this);
            
            fetch('api/api-edit-mac.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Produk berhasil diperbarui!');
                    window.location.href = 'view-mac.php?id=<?php echo $product_id; ?>';
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