<?php
session_start();
require_once '../../db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../../auth/login.php?error=not_logged_in');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: produk-terbaru.php?error=no_id');
    exit();
}

$id = $_GET['id'];

// Ambil data produk terbaru berdasarkan ID
$query = "SELECT * FROM home_produk_terbaru WHERE id = '$id'";
$result = mysqli_query($db, $query);
$produk_terbaru = mysqli_fetch_assoc($result);

if (!$produk_terbaru) {
    header('Location: produk-terbaru.php?error=not_found');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $urutan = $_POST['urutan'] ?? 0;
    
    $update_query = "UPDATE home_produk_terbaru 
                     SET urutan = '$urutan' 
                     WHERE id = '$id'";
    
    if (mysqli_query($db, $update_query)) {
        header('Location: produk-terbaru.php?success=updated');
    } else {
        header('Location: produk-terbaru.php?error=update_failed');
    }
    exit();
}

// Ambil detail produk asli untuk ditampilkan
$tipe = $produk_terbaru['tipe_produk'];
$produk_id = $produk_terbaru['produk_id'];

$tables = [
    'iphone' => 'admin_produk_iphone',
    'ipad' => 'admin_produk_ipad',
    'mac' => 'admin_produk_mac',
    'music' => 'admin_produk_music',
    'watch' => 'admin_produk_watch',
    'aksesoris' => 'admin_produk_aksesoris',
    'airtag' => 'admin_produk_airtag'
];

$table_name = $tables[$tipe] ?? '';
$detail_query = "SELECT * FROM $table_name WHERE id = '$produk_id'";
$detail_result = mysqli_query($db, $detail_query);
$detail = mysqli_fetch_assoc($detail_result);

// Ambil thumbnail
$gambar_table = $table_name . '_gambar';
$gambar_query = "SELECT foto_thumbnail FROM $gambar_table WHERE produk_id = '$produk_id' LIMIT 1";
$gambar_result = mysqli_query($db, $gambar_query);
$gambar = mysqli_fetch_assoc($gambar_result);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk Terbaru</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f5f7fb;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            padding: 30px;
            width: 100%;
            max-width: 600px;
        }

        h1 {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        h1 i {
            color: #4a6cf7;
        }

        .product-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .product-thumbnail {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #4a6cf7;
        }

        .product-details h3 {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .product-details p {
            font-size: 14px;
            color: #666;
        }

        .tipe-badge {
            background: #6a11cb;
            color: white;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
            display: inline-block;
            margin-top: 5px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: 500;
            margin-bottom: 8px;
            color: #333;
        }

        input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: border 0.3s;
        }

        input:focus {
            outline: none;
            border-color: #4a6cf7;
        }

        .form-hint {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        .btn-submit, .btn-back {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-submit {
            background: linear-gradient(135deg, #4a6cf7 0%, #6a11cb 100%);
            color: white;
            flex: 1;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(74, 108, 247, 0.3);
        }

        .btn-back {
            background-color: #f8f9fa;
            color: #333;
            border: 1px solid #ddd;
        }

        .btn-back:hover {
            background-color: #e9ecef;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-edit"></i> Edit Produk Terbaru</h1>
        
        <div class="product-info">
            <?php if(!empty($gambar['foto_thumbnail'])): ?>
                <img src="../../uploads/<?php echo htmlspecialchars($gambar['foto_thumbnail']); ?>" 
                     alt="Thumbnail" class="product-thumbnail">
            <?php else: ?>
                <div class="product-thumbnail" style="background: #e0e0e0; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-image" style="color: #999;"></i>
                </div>
            <?php endif; ?>
            <div class="product-details">
                <h3><?php echo htmlspecialchars($detail['nama_produk'] ?? 'Produk tidak ditemukan'); ?></h3>
                <p>ID: <?php echo $produk_id; ?></p>
                <span class="tipe-badge"><?php echo strtoupper($tipe); ?></span>
            </div>
        </div>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Urutan Tampil:</label>
                <input type="number" name="urutan" value="<?php echo $produk_terbaru['urutan']; ?>" min="0" placeholder="0" required>
                <div class="form-hint">Angka kecil = tampil pertama (0, 1, 2, ...)</div>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i> Update Produk Terbaru
                </button>
                <a href="produk-terbaru.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</body>
</html>