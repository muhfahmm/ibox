<?php
session_start();
require_once '../../db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../../auth/login.php?error=not_logged_in');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tipe_produk = $_POST['tipe_produk'];
    $produk_id = $_POST['produk_id'];
    $urutan = $_POST['urutan'] ?? 0;

    // Cek apakah produk sudah ada di daftar terbaru
    $check_query = "SELECT * FROM home_produk_terbaru 
                    WHERE produk_id = '$produk_id' AND tipe_produk = '$tipe_produk'";
    $check_result = mysqli_query($db, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        header('Location: produk-terbaru.php?error=already_exists');
        exit();
    }

    $insert_query = "INSERT INTO home_produk_terbaru 
                    (produk_id, tipe_produk, urutan) 
                    VALUES ('$produk_id', '$tipe_produk', '$urutan')";
    
    if (mysqli_query($db, $insert_query)) {
        header('Location: produk-terbaru.php?success=added');
    } else {
        header('Location: produk-terbaru.php?error=db_error');
    }
    exit();
}

// Ambil daftar produk dari semua kategori
$products = [];

// Query untuk setiap kategori
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
    $query = "SELECT id, nama_produk FROM $table ORDER BY id DESC";
    $result = mysqli_query($db, $query);
    
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = [
            'id' => $row['id'],
            'nama' => $row['nama_produk'],
            'tipe' => $key
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk Terbaru</title>
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

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: 500;
            margin-bottom: 8px;
            color: #333;
        }

        select, input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: border 0.3s;
        }

        select:focus, input:focus {
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

        .product-option {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .product-option:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-plus"></i> Tambah Produk Terbaru</h1>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Pilih Tipe Produk:</label>
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
                <div class="form-hint">Pilih kategori produk</div>
            </div>

            <div class="form-group">
                <label>Pilih Produk:</label>
                <select name="produk_id" id="produk_id" required>
                    <option value="">-- Pilih Produk --</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?php echo $product['id']; ?>" data-tipe="<?php echo $product['tipe']; ?>">
                            [<?php echo strtoupper($product['tipe']); ?>] <?php echo htmlspecialchars($product['nama']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-hint">Produk akan ditampilkan sebagai "Produk Terbaru"</div>
            </div>

            <div class="form-group">
                <label>Urutan Tampil:</label>
                <input type="number" name="urutan" value="0" min="0" placeholder="0">
                <div class="form-hint">Angka kecil = tampil pertama (0, 1, 2, ...)</div>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i> Simpan Produk Terbaru
                </button>
                <a href="produk-terbaru.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>

    <script>
        // Filter produk berdasarkan tipe yang dipilih
        document.getElementById('tipe_produk').addEventListener('change', function() {
            const selectedTipe = this.value;
            const produkSelect = document.getElementById('produk_id');
            
            // Reset dan tampilkan semua opsi
            for (let option of produkSelect.options) {
                option.style.display = 'block';
            }
            
            // Jika tipe dipilih, filter opsi
            if (selectedTipe) {
                for (let option of produkSelect.options) {
                    if (option.value && option.dataset.tipe !== selectedTipe) {
                        option.style.display = 'none';
                    }
                }
            }
            
            // Reset ke pilihan pertama
            produkSelect.value = '';
        });
    </script>
</body>
</html>