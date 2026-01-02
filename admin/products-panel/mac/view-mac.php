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

// Fetch all colors with images
$query_colors = "SELECT * FROM admin_produk_mac_gambar WHERE produk_id = '$product_id'";
$result_colors = mysqli_query($db, $query_colors);
$colors = mysqli_fetch_all($result_colors, MYSQLI_ASSOC);

// Fetch all combinations
$query_combinations = "SELECT * FROM admin_produk_mac_kombinasi WHERE produk_id = '$product_id' ORDER BY warna, processor, penyimpanan, ram";
$result_combinations = mysqli_query($db, $query_combinations);
$combinations = mysqli_fetch_all($result_combinations, MYSQLI_ASSOC);

// Calculate totals
$total_stock = 0;
$total_combinations = count($combinations);
$available_combinations = 0;
$min_price = PHP_INT_MAX;
$max_price = 0;
$unique_colors = [];

foreach ($combinations as $combo) {
    $total_stock += $combo['jumlah_stok'];
    if ($combo['jumlah_stok'] > 0) {
        $available_combinations++;
    }
    
    // Track unique colors
    if (!in_array($combo['warna'], $unique_colors)) {
        $unique_colors[] = $combo['warna'];
    }

    $price = floatval($combo['harga']);
    $min_price = min($min_price, $price);
    $max_price = max($max_price, $price);
}

if ($min_price === PHP_INT_MAX) $min_price = 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk Mac - <?php echo htmlspecialchars($product['nama_produk']); ?></title>
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
            margin-bottom: 30px;
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
        
        .product-header {
            border-bottom: 2px solid #eaeaea;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .product-title {
            color: #333;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .product-meta {
            color: #666;
            font-size: 16px;
            margin-bottom: 20px;
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            border: 1px solid #eaeaea;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: 700;
            color: #4a6cf7;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #666;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .color-section {
            background: #fff;
            border: 1px solid #eaeaea;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .variant-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        
        .variant-table th {
            text-align: left;
            padding: 12px;
            background-color: #f8f9fa;
            border-bottom: 2px solid #eaeaea;
            font-weight: 600;
            color: #555;
        }
        
        .variant-table td {
            padding: 12px;
            border-bottom: 1px solid #eaeaea;
            vertical-align: middle;
        }
        
        .image-gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 15px;
        }
        
        .image-item {
            position: relative;
            width: 100px;
            height: 100px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #eaeaea;
        }
        
        .image-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .thumbnail-container {
            width: 120px;
            height: 120px;
            border-radius: 10px;
            overflow: hidden;
            border: 3px solid #4a6cf7;
            margin-bottom: 10px;
        }
        
        .thumbnail-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
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
        
        .btn-edit {
            background: linear-gradient(135deg, #4a6cf7 0%, #6a11cb 100%);
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
        
        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(74, 108, 247, 0.3);
            color: white;
        }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }
        
        .status-tersedia {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-habis {
            background-color: #f8d7da;
            color: #721c24;
        }

        .original-price {
            text-decoration: line-through;
            color: #999;
            font-size: 13px;
            display: block;
        }
        
        .final-price {
            font-weight: 600;
            color: #333;
        }
        
        .discount-price {
            color: #dc3545;
            font-weight: 600;
        }

        .section-title {
            border-left: 4px solid #4a6cf7;
            padding-left: 15px;
            margin-bottom: 20px;
            color: #333;
        }
        
        .processor-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
            background-color: #e8f4f8;
            color: #0d6efd;
            border: 1px solid #b6d4fe;
        }
        
        .storage-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
            background-color: #e7f6e7;
            color: #198754;
            border: 1px solid #c1e1c1;
        }
        
        .ram-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
            background-color: #f0e6ff;
            color: #6f42c1;
            border: 1px solid #d9c4f1;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h2>
                        <i class="fas fa-eye me-2"></i> 
                        Detail Produk Mac
                    </h2>
                    <div>
                        <a href="mac.php" class="btn-back me-2">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <a href="edit-mac.php?id=<?php echo $product_id; ?>" class="btn-edit">
                            <i class="fas fa-edit"></i> Edit Produk
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <!-- Product Header -->
                <div class="product-header">
                    <h1 class="product-title"><?php echo htmlspecialchars($product['nama_produk']); ?></h1>
                    <?php if(!empty($product['kategori'])): 
                        $kategori_labels = [
                            'macbook-pro' => 'MacBook Pro',
                            'macbook-air' => 'MacBook Air',
                            'imac' => 'iMac',
                            'mac-mini' => 'Mac Mini',
                            'mac-studio' => 'Mac Studio'
                        ];
                        $kategori_display = isset($kategori_labels[$product['kategori']]) ? $kategori_labels[$product['kategori']] : $product['kategori'];
                    ?>
                        <div class="mb-3">
                            <span class="badge bg-primary" style="padding: 8px 15px; font-size: 14px;">
                                <i class="fas fa-tag me-1"></i> Kategori: <?php echo htmlspecialchars($kategori_display); ?>
                            </span>
                        </div>
                    <?php endif; ?>
                    <div class="stats-container">
                        <div class="stat-card">
                            <div class="stat-number"><?php echo count($unique_colors); ?></div>
                            <div class="stat-label">Varian Warna</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number"><?php echo $total_combinations; ?></div>
                            <div class="stat-label">Total Kombinasi</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number"><?php echo $total_stock; ?></div>
                            <div class="stat-label">Total Stok</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number" style="font-size: 24px;">
                                Rp <?php echo number_format($min_price, 0, ',', '.'); ?>
                                <?php if($max_price > $min_price): ?>
                                    - <?php echo number_format($max_price, 0, ',', '.'); ?>
                                <?php endif; ?>
                            </div>
                            <div class="stat-label">Range Harga</div>
                        </div>
                    </div>
                </div>
                
                <!-- Product Description -->
                <div class="mb-5">
                    <h4 class="section-title">Deskripsi Produk</h4>
                    <div class="bg-light p-4 rounded">
                        <?php if(!empty($product['deskripsi_produk'])): ?>
                            <?php echo nl2br(htmlspecialchars($product['deskripsi_produk'])); ?>
                        <?php else: ?>
                            <p class="text-muted mb-0"><i>Tidak ada deskripsi produk</i></p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Images per Color -->
                <div class="mb-5">
                    <h4 class="section-title">Galeri Warna</h4>
                    <?php if(count($colors) > 0): ?>
                        <div class="row">
                            <?php foreach($colors as $color): 
                                $product_images = json_decode($color['foto_produk'], true) ?? [];
                            ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="color-section h-100">
                                        <h5 class="mb-3"><i class="fas fa-palette me-2"></i><?php echo htmlspecialchars($color['warna']); ?></h5>
                                        
                                        <div class="d-flex gap-3 align-items-start">
                                            <div>
                                                <div class="text-muted small mb-1">Thumbnail</div>
                                                <?php if(!empty($color['foto_thumbnail'])): ?>
                                                    <div class="thumbnail-container">
                                                        <img src="../../uploads/<?php echo htmlspecialchars($color['foto_thumbnail']); ?>" 
                                                             alt="Thumbnail <?php echo htmlspecialchars($color['warna']); ?>">
                                                    </div>
                                                <?php else: ?>
                                                    <div class="thumbnail-container d-flex align-items-center justify-content-center bg-light">
                                                        <span class="text-muted"><i class="fas fa-image"></i></span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div class="flex-grow-1">
                                                <div class="text-muted small mb-1">Galeri (<?php echo count($product_images); ?>)</div>
                                                <div class="image-gallery">
                                                    <?php foreach($product_images as $img): ?>
                                                        <div class="image-item">
                                                            <img src="../../uploads/<?php echo htmlspecialchars($img); ?>" 
                                                                 alt="Foto <?php echo htmlspecialchars($color['warna']); ?>">
                                                        </div>
                                                    <?php endforeach; ?>
                                                    <?php if(empty($product_images)): ?>
                                                        <div class="text-muted small"><i>Tidak ada foto tambahan</i></div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i> Belum ada data gambar untuk produk ini.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Combinations Table -->
                <!-- KOLOM MAC: Processor, Penyimpanan, RAM (menggantikan Konektivitas) -->
                <div class="mb-4">
                    <h4 class="section-title">Daftar Kombinasi & Stok</h4>
                    
                    <div class="table-responsive">
                        <table class="variant-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Warna</th>
                                    <th>Processor</th>
                                    <th>Penyimpanan</th>
                                    <th>RAM</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($combinations) > 0): ?>
                                    <?php $no = 1; foreach($combinations as $combo): 
                                        $has_discount = !empty($combo['harga_diskon']) && $combo['harga_diskon'] > 0;
                                        $final_price = $has_discount ? $combo['harga_diskon'] : $combo['harga'];
                                    ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td>
                                            <span class="badge bg-light text-dark border">
                                                <?php echo htmlspecialchars($combo['warna']); ?>
                                            </span>
                                        </td>
                                        <!-- Kolom khusus Mac -->
                                        <td>
                                            <span class="processor-badge">
                                                <?php echo htmlspecialchars($combo['processor']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="storage-badge">
                                                <?php echo htmlspecialchars($combo['penyimpanan']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="ram-badge">
                                                <?php echo htmlspecialchars($combo['ram']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if($has_discount): ?>
                                                <span class="original-price">Rp <?php echo number_format($combo['harga'], 0, ',', '.'); ?></span>
                                                <span class="discount-price">Rp <?php echo number_format($final_price, 0, ',', '.'); ?></span>
                                            <?php else: ?>
                                                <span class="final-price">Rp <?php echo number_format($combo['harga'], 0, ',', '.'); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo $combo['jumlah_stok']; ?></strong> unit
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo ($combo['jumlah_stok'] > 0) ? 'tersedia' : 'habis'; ?>">
                                                <?php echo ($combo['jumlah_stok'] > 0) ? 'Tersedia' : 'Habis'; ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            <i>Tidak ada data kombinasi untuk produk ini</i>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Action Buttons Bottom -->
                <div class="d-flex justify-content-between mt-5 pt-4 border-top">
                    <a href="mac.php" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                    </a>
                    <div>
                        <a href="edit-mac.php?id=<?php echo $product_id; ?>" class="btn-edit me-2">
                            <i class="fas fa-edit"></i> Edit Produk
                        </a>
                        <a href="delete-mac.php?id=<?php echo $product_id; ?>" 
                           class="btn btn-danger" 
                           onclick="return confirm('Yakin ingin menghapus produk ini? Semua data terkait akan terhapus.')">
                            <i class="fas fa-trash"></i> Hapus Produk
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>