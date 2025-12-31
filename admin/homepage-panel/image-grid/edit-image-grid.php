<?php
session_start();
require_once '../../db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../../auth/login.php?error=not_logged_in');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: image-grid.php?error=no_id');
    exit();
}

$id = $_GET['id'];

// Ambil data grid berdasarkan ID
$query_grid = "SELECT * FROM home_grid WHERE id = '$id'";
$result_grid = mysqli_query($db, $query_grid);
$grid_item = mysqli_fetch_assoc($result_grid);

if (!$grid_item) {
    header('Location: image-grid.php?error=not_found');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tipe_produk = $_POST['tipe_produk'];
    $produk_id = $_POST['produk_id'];
    $label = mysqli_real_escape_string($db, $_POST['label']);
    $urutan = $_POST['urutan'] ?? 0;
    
    // Cek apakah produk lain sudah ada di grid (kecuali item ini sendiri)
    $check_query = "SELECT * FROM home_grid 
                    WHERE produk_id = '$produk_id' AND tipe_produk = '$tipe_produk' AND id != '$id'";
    $check_result = mysqli_query($db, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        header('Location: image-grid.php?error=already_exists');
        exit();
    }

    $update_query = "UPDATE home_grid 
                     SET tipe_produk = '$tipe_produk', produk_id = '$produk_id', label = '$label', urutan = '$urutan' 
                     WHERE id = '$id'";
    
    if (mysqli_query($db, $update_query)) {
        header('Location: image-grid.php?success=updated');
    } else {
        header('Location: image-grid.php?error=update_failed');
    }
    exit();
}

// Ambil daftar produk dari semua kategori untuk dropdown
$all_products = [];
$categories = [
    'iphone' => 'admin_produk_iphone',
    'ipad' => 'admin_produk_ipad',
    'mac' => 'admin_produk_mac',
    'music' => 'admin_produk_music',
    'watch' => 'admin_produk_watch',
    'aksesoris' => 'admin_produk_aksesoris',
    'airtag' => 'admin_produk_airtag'
];

foreach ($categories as $key => $table_name) {
    $q = "SELECT id, nama_produk FROM $table_name ORDER BY nama_produk";
    $res = mysqli_query($db, $q);
    while ($row = mysqli_fetch_assoc($res)) {
        $all_products[] = [
            'id' => $row['id'],
            'nama' => $row['nama_produk'],
            'tipe' => $key
        ];
    }
}

// Ambil detail produk saat ini untuk preview awal
$tipe_sekarang = $grid_item['tipe_produk'];
$produk_id_sekarang = $grid_item['produk_id'];

$table_curr = $categories[$tipe_sekarang] ?? '';
$detail_query = "SELECT * FROM $table_curr WHERE id = '$produk_id_sekarang'";
$detail = mysqli_fetch_assoc(mysqli_query($db, $detail_query));

$gambar_table = $table_curr . '_gambar';
$gambar = mysqli_fetch_assoc(mysqli_query($db, "SELECT foto_thumbnail FROM $gambar_table WHERE produk_id = '$produk_id_sekarang' LIMIT 1"));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk Grid</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            padding: 40px;
            width: 100%;
            max-width: 600px;
        }

        h1 {
            font-size: 24px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        h1 i {
            color: #4a6cf7;
        }

        .product-preview {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            display: flex;
            gap: 20px;
            align-items: center;
            border: 1px solid #eee;
        }

        .preview-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .preview-info h3 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .tipe-badge-preview {
            font-size: 11px;
            background: #6a11cb;
            color: white;
            padding: 3px 10px;
            border-radius: 6px;
            display: inline-block;
            text-transform: uppercase;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        select, input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s;
        }

        select:focus, input:focus {
            outline: none;
            border-color: #4a6cf7;
            box-shadow: 0 0 0 3px rgba(74, 108, 247, 0.1);
        }

        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 35px;
        }

        .btn-submit {
            background: linear-gradient(135deg, #4a6cf7 0%, #6a11cb 100%);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            flex: 1;
            transition: all 0.3s;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(74, 108, 247, 0.3);
        }

        .btn-back {
            background: #f1f3f5;
            color: #495057;
            padding: 12px 25px;
            border: none;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            text-align: center;
            flex: 1;
            transition: all 0.3s;
        }

        .btn-back:hover {
            background: #e9ecef;
        }

        .form-hint {
            font-size: 12px;
            color: #777;
            margin-top: 6px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-edit"></i> Edit Produk Grid</h1>
        
        <div class="product-preview" id="preview-panel">
            <?php if(!empty($gambar['foto_thumbnail'])): ?>
                <img src="../../uploads/<?php echo htmlspecialchars($gambar['foto_thumbnail']); ?>" 
                     alt="Thumbnail" class="preview-img" id="preview-img">
            <?php else: ?>
                <div class="preview-img" id="preview-placeholder" style="background: #e0e0e0; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-image" style="color: #999;"></i>
                </div>
            <?php endif; ?>
            <div class="preview-info">
                <h3 id="preview-name"><?php echo htmlspecialchars($detail['nama_produk'] ?? 'Produk tidak ditemukan'); ?></h3>
                <span class="tipe-badge-preview" id="preview-tipe"><?php echo $tipe_sekarang; ?></span>
            </div>
        </div>

        <form method="POST" action="">
            <div class="form-group">
                <label>Ganti Tipe Produk:</label>
                <select name="tipe_produk" id="tipe_produk" required>
                    <option value="iphone" <?php if($tipe_sekarang == 'iphone') echo 'selected'; ?>>iPhone</option>
                    <option value="ipad" <?php if($tipe_sekarang == 'ipad') echo 'selected'; ?>>iPad</option>
                    <option value="mac" <?php if($tipe_sekarang == 'mac') echo 'selected'; ?>>Mac</option>
                    <option value="music" <?php if($tipe_sekarang == 'music') echo 'selected'; ?>>Music</option>
                    <option value="watch" <?php if($tipe_sekarang == 'watch') echo 'selected'; ?>>Watch</option>
                    <option value="aksesoris" <?php if($tipe_sekarang == 'aksesoris') echo 'selected'; ?>>Aksesoris</option>
                    <option value="airtag" <?php if($tipe_sekarang == 'airtag') echo 'selected'; ?>>AirTag</option>
                </select>
            </div>

            <div class="form-group">
                <label>Pilih Produk Baru:</label>
                <select name="produk_id" id="produk_id" required>
                    <?php foreach ($all_products as $p): ?>
                        <option value="<?php echo $p['id']; ?>" 
                                data-tipe="<?php echo $p['tipe']; ?>"
                                <?php if($p['id'] == $produk_id_sekarang && $p['tipe'] == $tipe_sekarang) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($p['nama']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Label Grid:</label>
                <input type="text" name="label" value="<?php echo htmlspecialchars($grid_item['label']); ?>" placeholder="Baru, Promo, Hot Item...">
                <div class="form-hint">Muncul sebagai badge pada gambar produk.</div>
            </div>

            <div class="form-group">
                <label>Urutan Tampil:</label>
                <input type="number" name="urutan" value="<?php echo $grid_item['urutan']; ?>" min="0">
                <div class="form-hint">Urutan tampilan di grid halaman utama.</div>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn-submit">Simpan Perubahan</button>
                <a href="image-grid.php" class="btn-back">Batal</a>
            </div>
        </form>
    </div>

    <script>
        const tipeSelect = document.getElementById('tipe_produk');
        const produkSelect = document.getElementById('produk_id');
        const allOptions = Array.from(produkSelect.options);

        function filterProducts() {
            const selectedTipe = tipeSelect.value;
            produkSelect.innerHTML = '';
            
            allOptions.forEach(opt => {
                if(opt.getAttribute('data-tipe') === selectedTipe) {
                    produkSelect.appendChild(opt.cloneNode(true));
                }
            });

            // Update preview basic info
            document.getElementById('preview-tipe').textContent = selectedTipe.toUpperCase();
        }

        tipeSelect.addEventListener('change', filterProducts);
        
        // Initial filter on load (to show current selection)
        window.addEventListener('load', () => {
            const currentTipe = "<?php echo $tipe_sekarang; ?>";
            const currentId = "<?php echo $produk_id_sekarang; ?>";
            
            tipeSelect.value = currentTipe;
            filterProducts();
            produkSelect.value = currentId;
        });
    </script>
</body>
</html>
