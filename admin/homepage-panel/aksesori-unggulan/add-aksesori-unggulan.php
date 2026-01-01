<?php
session_start();
require_once '../../db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../../auth/login.php?error=not_logged_in');
    exit();
}

$admin_username = $_SESSION['admin_username'] ?? 'Admin';

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
    <title>Tambah Aksesori Unggulan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }
        h1 {
            font-size: 24px;
            margin-bottom: 30px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .form-group {
            margin-bottom: 25px;
        }
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: 500;
            color: #444;
            font-size: 14px;
        }
        select, input {
            width: 100%;
            padding: 14px;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s ease;
            background-color: #fcfcfc;
        }
        select:focus, input:focus {
            outline: none;
            border-color: #4a6cf7;
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(74, 108, 247, 0.1);
        }
        .btn-submit {
            background: linear-gradient(135deg, #4a6cf7 0%, #6a11cb 100%);
            color: white;
            padding: 14px 28px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(74, 108, 247, 0.2);
            width: 100%;
            margin-top: 10px;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(74, 108, 247, 0.3);
        }
        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
        .btn-back {
            display: block;
            text-align: center;
            text-decoration: none;
            color: #888;
            margin-top: 20px;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.3s;
        }
        .btn-back:hover {
            color: #333;
        }
        .help-text {
            display: block;
            margin-top: 6px;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-gem" style="color: #4a6cf7;"></i> Tambah Aksesori Unggulan</h1>
        
        <form id="addAksesoriForm">
            <div class="form-group">
                <label><i class="fas fa-layer-group me-2"></i>Tipe Produk</label>
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
                <label><i class="fas fa-box me-2"></i>Produk</label>
                <select name="produk_id" id="produk_id" required disabled>
                    <option value="">Pilih Tipe Produk Terlebih Dahulu</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?php echo $product['id']; ?>" data-tipe="<?php echo $product['tipe']; ?>">
                            <?php echo $product['nama']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label><i class="fas fa-tag me-2"></i>Label Tampil</label>
                <input type="text" name="label" placeholder="Contoh: New Arrival, Bestseller, Limited" required>
                <small class="help-text">Label ini akan muncul sebagai badge kecil di atas nama produk.</small>
            </div>

            <div class="form-group">
                <label><i class="fas fa-sort me-2"></i>Urutan</label>
                <input type="number" name="urutan" value="0" min="0">
                <small class="help-text">Angka lebih kecil akan tampil lebih awal di slider.</small>
            </div>

            <button type="submit" class="btn-submit" id="btnSubmit">
                <i class="fas fa-save me-2"></i> Simpan Aksesori Unggulan
            </button>
            <a href="aksesori-unggulan.php" class="btn-back">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </form>
    </div>

    <script>
        document.getElementById('tipe_produk').addEventListener('change', function() {
            const selectedTipe = this.value;
            const produkSelect = document.getElementById('produk_id');
            
            if (selectedTipe === "") {
                produkSelect.disabled = true;
                produkSelect.innerHTML = '<option value="">Pilih Tipe Produk Terlebih Dahulu</option>';
                return;
            }

            produkSelect.disabled = false;
            produkSelect.innerHTML = '<option value="">-- Pilih Produk --</option>';
            
            <?php foreach ($products as $product): ?>
                if ("<?php echo $product['tipe']; ?>" === selectedTipe) {
                    const option = document.createElement('option');
                    option.value = "<?php echo $product['id']; ?>";
                    option.text = "<?php echo addslashes($product['nama']); ?>";
                    produkSelect.appendChild(option);
                }
            <?php endforeach; ?>
        });

        document.getElementById('addAksesoriForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btnSubmit = document.getElementById('btnSubmit');
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Menyimpan...';

            const formData = new FormData(this);

            fetch('api/add-aksesori-unggulan.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = 'aksesori-unggulan.php?success=added';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message
                    });
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<i class="fas fa-save me-2"></i> Simpan Aksesori Unggulan';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan sistem.'
                });
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = '<i class="fas fa-save me-2"></i> Simpan Aksesori Unggulan';
            });
        });
    </script>
</body>
</html>
