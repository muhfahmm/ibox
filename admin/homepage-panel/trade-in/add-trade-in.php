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
    $label_promo = $_POST['label_promo'];
    $urutan = $_POST['urutan'] ?? 0;

    // Cek apakah sudah ada
    $check_query = "SELECT * FROM home_trade_in 
                    WHERE produk_id = '$produk_id' AND tipe_produk = '$tipe_produk'";
    $check_result = mysqli_query($db, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        header('Location: trade-in.php?error=already_exists');
        exit();
    }

    $insert_query = "INSERT INTO home_trade_in 
                    (produk_id, tipe_produk, label_promo, urutan) 
                    VALUES ('$produk_id', '$tipe_produk', '$label_promo', '$urutan')";
    
    if (mysqli_query($db, $insert_query)) {
        header('Location: trade-in.php?success=added');
    } else {
        header('Location: trade-in.php?error=db_error');
    }
    exit();
}

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
    $query = "SELECT id, nama_produk FROM $table ORDER BY nama_produk";
    $result = mysqli_query($db, $query);
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = [
                'id' => $row['id'],
                'nama' => $row['nama_produk'],
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
    <title>Tambah Produk Trade In</title>
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
            padding: 40px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }
        h1 {
            font-size: 24px;
            margin-bottom: 30px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }
        select, input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
        }
        select:focus, input:focus {
            outline: none;
            border-color: #4a6cf7;
            box-shadow: 0 0 0 3px rgba(74, 108, 247, 0.1);
        }
        .btn-submit {
            background: linear-gradient(135deg, #4a6cf7 0%, #6a11cb 100%);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(74, 108, 247, 0.3);
        }
        .btn-back {
            text-decoration: none;
            color: #666;
            margin-left: 15px;
            font-size: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-plus-circle"></i> Tambah Produk Trade In</h1>
        
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
            </div>

            <div class="form-group">
                <label>Pilih Produk:</label>
                <select name="produk_id" id="produk_id" required>
                    <option value="">-- Pilih Produk --</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?php echo $product['id']; ?>" data-tipe="<?php echo $product['tipe']; ?>">
                            [<?php echo strtoupper($product['tipe']); ?>] <?php echo $product['nama']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Potongan Harga (%):</label>
                <input type="number" name="label_promo" placeholder="Contoh: 10" min="0" max="100" required>
                <small style="color: #666; margin-top: 5px; display: block; line-height: 1.4;">
                    Masukkan angka saja (persentase). Sistem akan otomatis menghitung potongan harga dan menampilkan tanda % di halaman depan.
                </small>
            </div>

            <div class="form-group">
                <label>Urutan Tampil (angka kecil = tampil pertama):</label>
                <input type="number" name="urutan" value="0" min="0">
            </div>

            <button type="submit" class="btn-submit">Simpan Produk Trade In</button>
            <a href="trade-in.php" class="btn-back">Kembali</a>
        </form>
    </div>

    <script>
        document.getElementById('tipe_produk').addEventListener('change', function() {
            const selectedTipe = this.value;
            const produkSelect = document.getElementById('produk_id');
            
            for (let option of produkSelect.options) {
                if (option.value === "") {
                    option.style.display = 'block';
                    continue;
                }
                
                if (selectedTipe === "" || option.dataset.tipe === selectedTipe) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            }
            
            produkSelect.value = '';
        });
    </script>
</body>
</html>
