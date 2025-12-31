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
    $label = mysqli_real_escape_string($db, $_POST['label']);
    $urutan = $_POST['urutan'] ?? 0;

    // Cek apakah sudah ada
    $check_query = "SELECT * FROM home_grid 
                    WHERE produk_id = '$produk_id' AND tipe_produk = '$tipe_produk'";
    $check_result = mysqli_query($db, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        header('Location: image-grid.php?error=already_exists');
        exit();
    }

    $insert_query = "INSERT INTO home_grid 
                    (produk_id, tipe_produk, label, urutan) 
                    VALUES ('$produk_id', '$tipe_produk', '$label', '$urutan')";
    
    if (mysqli_query($db, $insert_query)) {
        header('Location: image-grid.php?success=added');
    } else {
        header('Location: image-grid.php?error=db_error');
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
    <title>Tambah Produk ke Grid</title>
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

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: 500;
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
        <h1><i class="fas fa-plus-circle"></i> Tambah Produk Grid</h1>
        
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
                <select name="produk_id" id="produk_id" required disabled>
                    <option value="">-- Pilih Tipe Terlebih Dahulu --</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?php echo $product['id']; ?>" data-tipe="<?php echo $product['tipe']; ?>">
                            <?php echo $product['nama']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Label Grid (opsional):</label>
                <input type="text" name="label" placeholder="Contoh: Baru, Promo, Hot Item...">
                <div class="form-hint">Akan muncul sebagai badge pada produk.</div>
            </div>

            <div class="form-group">
                <label>Urutan Tampil:</label>
                <input type="number" name="urutan" value="0" min="0">
                <div class="form-hint">Angka lebih kecil akan muncul lebih awal.</div>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn-submit">Simpan Produk</button>
                <a href="image-grid.php" class="btn-back">Batal</a>
            </div>
        </form>
    </div>

    <script>
        const tipeSelect = document.getElementById('tipe_produk');
        const produkSelect = document.getElementById('produk_id');
        const allOptions = Array.from(produkSelect.options);

        tipeSelect.addEventListener('change', function() {
            const selectedTipe = this.value;
            
            if (selectedTipe === "") {
                produkSelect.disabled = true;
                produkSelect.innerHTML = '<option value="">-- Pilih Tipe Terlebih Dahulu --</option>';
                return;
            }

            produkSelect.disabled = false;
            produkSelect.innerHTML = '<option value="">-- Pilih Produk --</option>';
            
            allOptions.forEach(option => {
                if (option.getAttribute('data-tipe') === selectedTipe) {
                    produkSelect.appendChild(option.cloneNode(true));
                }
            });
        });
    </script>
</body>
</html>
