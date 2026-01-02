<?php
session_start();
require_once '../../db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../../auth/login.php?error=not_logged_in');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: chekout-sekarang.php');
    exit();
}

$id = $_GET['id'];

// Ambil data untuk diedit
$stmt = $db->prepare("SELECT * FROM home_checkout WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$current_data = $stmt->get_result()->fetch_assoc();

if (!$current_data) {
    header('Location: chekout-sekarang.php');
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
    <title>Edit Checkout Sekarang</title>
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
        <h1><i class="fas fa-edit" style="color: #4a6cf7;"></i> Edit Checkout Sekarang</h1>
        
        <form id="editCheckoutForm" autocomplete="off">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            
            <div class="form-group">
                <label><i class="fas fa-layer-group me-2"></i>Tipe Produk</label>
                <select name="tipe_produk" id="tipe_produk" required>
                    <option value="">-- Pilih Tipe --</option>
                    <option value="iphone" <?php echo ($current_data['tipe_produk'] == 'iphone') ? 'selected' : ''; ?>>iPhone</option>
                    <option value="ipad" <?php echo ($current_data['tipe_produk'] == 'ipad') ? 'selected' : ''; ?>>iPad</option>
                    <option value="mac" <?php echo ($current_data['tipe_produk'] == 'mac') ? 'selected' : ''; ?>>Mac</option>
                    <option value="music" <?php echo ($current_data['tipe_produk'] == 'music') ? 'selected' : ''; ?>>Music</option>
                    <option value="watch" <?php echo ($current_data['tipe_produk'] == 'watch') ? 'selected' : ''; ?>>Watch</option>
                    <option value="aksesoris" <?php echo ($current_data['tipe_produk'] == 'aksesoris') ? 'selected' : ''; ?>>Aksesoris</option>
                    <option value="airtag" <?php echo ($current_data['tipe_produk'] == 'airtag') ? 'selected' : ''; ?>>AirTag</option>
                </select>
            </div>

            <div class="form-group">
                <label><i class="fas fa-box me-2"></i>Produk</label>
                <select name="produk_id" id="produk_id" required>
                    <option value="">-- Pilih Produk --</option>
                    <!-- Will be populated by JS -->
                </select>
            </div>

            <div class="form-group">
                <label><i class="fas fa-tag me-2"></i>Label Tampil</label>
                <input type="text" name="label" value="<?php echo htmlspecialchars($current_data['label']); ?>" placeholder="Contoh: New Arrival, Bestseller, Limited" required>
                <small class="help-text">Label ini akan muncul sebagai badge kecil di atas nama produk.</small>
            </div>

            <div class="form-group">
                <label><i class="fas fa-align-left me-2"></i>Deskripsi Produk</label>
                <textarea name="deskripsi_produk" rows="4" placeholder="Masukkan deskripsi produk yang akan ditampilkan di halaman depan..." style="width: 100%; padding: 14px; border: 1px solid #e0e0e0; border-radius: 12px; font-size: 15px; transition: all 0.3s ease; background-color: #fcfcfc; font-family: 'Poppins', sans-serif; resize: vertical;"><?php echo htmlspecialchars($current_data['deskripsi_produk'] ?? ''); ?></textarea>
                <small class="help-text">Deskripsi ini akan ditampilkan di card checkout di halaman depan. Jika kosong, akan menggunakan deskripsi dari produk.</small>
            </div>

            <div class="form-group">
                <label><i class="fas fa-sort me-2"></i>Urutan</label>
                <input type="number" name="urutan" value="<?php echo $current_data['urutan']; ?>" min="0">
                <small class="help-text">Angka lebih kecil akan tampil lebih awal di slider.</small>
            </div>

            <button type="submit" class="btn-submit" id="btnSubmit">
                <i class="fas fa-save me-2"></i> Update Checkout
            </button>
            <a href="chekout-sekarang.php" class="btn-back">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </form>
    </div>

    <script>
        const products = <?php echo json_encode($products); ?>;
        const currentData = <?php echo json_encode($current_data); ?>;

        function populateProducts(selectedTipe, currentSelectedId = null) {
            const produkSelect = document.getElementById('produk_id');
            produkSelect.innerHTML = '<option value="">-- Pilih Produk --</option>';
            
            if (selectedTipe === "") {
                produkSelect.disabled = true;
                return;
            }

            produkSelect.disabled = false;
            products.forEach(product => {
                if (product.tipe === selectedTipe) {
                    const option = document.createElement('option');
                    option.value = product.id;
                    option.text = product.nama;
                    if (currentSelectedId && product.id == currentSelectedId) {
                        option.selected = true;
                    }
                    produkSelect.appendChild(option);
                }
            });
        }

        // Initialize products on load
        populateProducts(currentData.tipe_produk, currentData.produk_id);

        document.getElementById('tipe_produk').addEventListener('change', function() {
            populateProducts(this.value);
        });

        document.getElementById('editCheckoutForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btnSubmit = document.getElementById('btnSubmit');
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memperbarui...';

            const formData = new FormData(this);

            fetch('api/api-edit-checkout-sekarang.php', {
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
                        window.location.href = 'chekout-sekarang.php?success=updated';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message
                    });
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<i class="fas fa-save me-2"></i> Update Checkout';
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
                btnSubmit.innerHTML = '<i class="fas fa-save me-2"></i> Update Checkout';
            });
        });
    </script>
</body>
</html>

