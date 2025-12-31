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
    $label = $_POST['label'];
    $urutan = $_POST['urutan'] ?? 0;

    // Cek apakah sudah ada
    $check_query = "SELECT * FROM home_produk_populer 
                    WHERE produk_id = '$produk_id' AND tipe_produk = '$tipe_produk'";
    $check_result = mysqli_query($db, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        header('Location: produk-populer.php?error=already_exists');
        exit();
    }

    $insert_query = "INSERT INTO home_produk_populer 
                    (produk_id, tipe_produk, label, urutan) 
                    VALUES ('$produk_id', '$tipe_produk', '$label', '$urutan')";
    
    if (mysqli_query($db, $insert_query)) {
        header('Location: produk-populer.php?success=added');
    } else {
        header('Location: produk-populer.php?error=db_error');
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
    $query = "SELECT id, nama_produk FROM $table ORDER BY nama_produk";
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
    <title>Tambah Produk Populer</title>
    <style>
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        select, input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .btn-submit {
            background: #4a6cf7;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Tambah Produk Populer</h1>
        
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
                <label>Label (contoh: Populer, Terlaris, Rekomendasi):</label>
                <input type="text" name="label" placeholder="Masukkan label..." required>
            </div>

            <div class="form-group">
                <label>Urutan Tampil (angka kecil = tampil pertama):</label>
                <input type="number" name="urutan" value="0" min="0">
            </div>

            <button type="submit" class="btn-submit">Simpan Produk Populer</button>
            <a href="produk-populer.php" style="margin-left: 10px;">Kembali</a>
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