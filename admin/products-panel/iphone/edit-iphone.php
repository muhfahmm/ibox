<?php
session_start();
require_once '../../db.php';

// Cek login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../../auth/login.php?error=not_logged_in');
    exit();
}

// Cek ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: iphone.php?error=invalid_id');
    exit();
}

$product_id = mysqli_real_escape_string($db, $_GET['id']);

// Ambil data produk utama
$query_product = "SELECT * FROM admin_produk_iphone WHERE id = '$product_id'";
$result_product = mysqli_query($db, $query_product);

if (mysqli_num_rows($result_product) === 0) {
    header('Location: iphone.php?error=product_not_found');
    exit();
}

$product = mysqli_fetch_assoc($result_product);

// Ambil data gambar (grouped by color)
$query_images = "SELECT * FROM admin_produk_iphone_gambar WHERE produk_id = '$product_id'";
$result_images = mysqli_query($db, $query_images);
$images_by_color = [];
while ($row = mysqli_fetch_assoc($result_images)) {
    $images_by_color[$row['warna']] = $row;
}

// Ambil data kombinasi (untuk pre-fill penyimpanan, konektivitas, dan stok)
$query_combos = "SELECT * FROM admin_produk_iphone_kombinasi WHERE produk_id = '$product_id'";
$result_combos = mysqli_query($db, $query_combos);
$combinations = [];
while ($row = mysqli_fetch_assoc($result_combos)) {
    $combinations[] = $row;
}

// Extract unique attributes
$unique_colors = [];
$unique_storages = []; // Associative: size => {price, discount}
$unique_connectivities = [];

foreach ($combinations as $combo) {
    if (!in_array($combo['warna'], $unique_colors)) {
        $unique_colors[] = $combo['warna'];
    }
    
    // Store logic for storage prices might vary per color in complex systems, 
    // but here we simplify or take the first occurrence if inconsistent.
    if (!isset($unique_storages[$combo['penyimpanan']])) {
        $unique_storages[$combo['penyimpanan']] = [
            'harga' => $combo['harga'],
            'harga_diskon' => $combo['harga_diskon']
        ];
    }
    
    if (!in_array($combo['konektivitas'], $unique_connectivities)) {
        $unique_connectivities[] = $combo['konektivitas'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk iPhone - <?php echo htmlspecialchars($product['nama_produk']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f5f7fb; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .container { max-width: 1400px; margin: 30px auto; padding: 20px; }
        .card { border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border: none; }
        .card-header { background: linear-gradient(135deg, #4a6cf7 0%, #6a11cb 100%); color: white; border-radius: 15px 15px 0 0 !important; padding: 25px; }
        .card-body { padding: 30px; }
        .form-section { background-color: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 25px; }
        .form-section h4 { color: #4a6cf7; border-bottom: 2px solid #eaeaea; padding-bottom: 10px; margin-bottom: 20px; }
        .btn-submit { background: linear-gradient(135deg, #4a6cf7 0%, #6a11cb 100%); color: white; border: none; padding: 12px 30px; border-radius: 8px; font-weight: 500; }
        .btn-submit:hover { opacity: 0.9; color: white; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(74, 108, 247, 0.3); }
        .btn-back { background-color: #6c757d; color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; }
        .btn-back:hover { background-color: #5a6268; color: white; }
        
        /* Custom styles for dynamic sections */
        .color-option, .storage-option, .connectivity-option { background: white; padding: 15px; border-radius: 8px; border: 1px solid #eaeaea; margin-bottom: 10px; position: relative; }
        .btn-danger-sm { position: absolute; top: 10px; right: 10px; background-color: #dc3545; color: white; border: none; width: 25px; height: 25px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; }
        .preview-item { width: 80px; height: 80px; margin-right: 10px; margin-bottom: 10px; display: inline-block; position: relative; }
        .preview-item img { width: 100%; height: 100%; object-fit: cover; border-radius: 5px; border: 1px solid #ddd; }
        
        .combination-table { width: 100%; border-collapse: collapse; margin-top: 15px; background: white; }
        .combination-table th { background: #4a6cf7; color: white; padding: 10px; }
        .combination-table td { padding: 10px; border-bottom: 1px solid #eee; }
        
        #loadingOverlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.8); z-index: 9999; display: none; align-items: center; justify-content: center; flex-direction: column; }
    </style>
</head>
<body>
    <div id="loadingOverlay">
        <div class="spinner-border text-primary" role="status"></div>
        <div class="mt-2 fw-bold">Menyimpan perubahan...</div>
    </div>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-edit me-2"></i> Edit Produk iPhone</h2>
            </div>
            <div class="card-body">
                <form id="editIphoneForm" action="api/api-edit-iphone.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                    
                    <!-- Info Produk -->
                    <div class="form-section">
                        <h4>Informasi Dasar</h4>
                        <div class="mb-3">
                            <label class="form-label">Nama Produk</label>
                            <input type="text" class="form-control" name="nama_produk" value="<?php echo htmlspecialchars($product['nama_produk']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" name="deskripsi_produk" rows="4"><?php echo htmlspecialchars($product['deskripsi_produk']); ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Warna -->
                    <div class="form-section">
                        <h4>Varian Warna & Gambar</h4>
                        <div id="colorsContainer">
                            <?php 
                            $idx = 0;
                            foreach($images_by_color as $warna => $img_data): 
                                $photos = json_decode($img_data['foto_produk'], true) ?? [];
                            ?>
                            <div class="color-option" data-color-index="<?php echo $idx; ?>">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Nama Warna</label>
                                        <input type="text" class="form-control" name="warna[<?php echo $idx; ?>][nama]" value="<?php echo htmlspecialchars($warna); ?>" required onchange="generateCombinations()">
                                    </div>
                                    <div class="col-md-9">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label">Thumbnail (Ganti untuk update)</label>
                                                <input type="file" class="form-control mb-2" name="warna[<?php echo $idx; ?>][thumbnail]" accept="image/*">
                                                <input type="hidden" name="warna[<?php echo $idx; ?>][existing_thumbnail]" value="<?php echo htmlspecialchars($img_data['foto_thumbnail']); ?>">
                                                <?php if(!empty($img_data['foto_thumbnail'])): ?>
                                                    <div class="preview-item">
                                                        <img src="../../uploads/<?php echo htmlspecialchars($img_data['foto_thumbnail']); ?>" title="Current Thumbnail">
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Foto Produk (Upload baru menambah)</label>
                                                <input type="file" class="form-control mb-2" name="warna[<?php echo $idx; ?>][product_images][]" multiple accept="image/*">
                                                <?php foreach($photos as $photo): ?>
                                                    <div class="preview-item">
                                                        <img src="../../uploads/<?php echo htmlspecialchars($photo); ?>">
                                                        <input type="hidden" name="warna[<?php echo $idx; ?>][existing_images][]" value="<?php echo htmlspecialchars($photo); ?>">
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn-danger-sm" onclick="removeColor(this)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <?php $idx++; endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-outline-primary mt-2" onclick="addColor()">
                            <i class="fas fa-plus"></i> Tambah Warna
                        </button>
                    </div>
                    
                    <!-- Penyimpanan -->
                    <div class="form-section">
                        <h4>Penyimpanan & Harga Dasar</h4>
                        <div id="storagesContainer">
                            <?php 
                            $idx = 0;
                            foreach($unique_storages as $size => $price_data): 
                            ?>
                            <div class="storage-option" data-storage-index="<?php echo $idx; ?>">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Ukuran</label>
                                        <input type="text" class="form-control" name="penyimpanan[<?php echo $idx; ?>][size]" value="<?php echo htmlspecialchars($size); ?>" required onchange="generateCombinations()">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Harga Normal</label>
                                        <input type="number" class="form-control" name="penyimpanan[<?php echo $idx; ?>][harga]" value="<?php echo $price_data['harga']; ?>" required onchange="generateCombinations()">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Harga Diskon</label>
                                        <input type="number" class="form-control" name="penyimpanan[<?php echo $idx; ?>][harga_diskon]" value="<?php echo $price_data['harga_diskon']; ?>" onchange="generateCombinations()">
                                    </div>
                                </div>
                                <button type="button" class="btn-danger-sm" onclick="removeStorage(this)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <?php $idx++; endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-outline-primary mt-2" onclick="addStorage()">
                            <i class="fas fa-plus"></i> Tambah Penyimpanan
                        </button>
                    </div>

                    <!-- Konektivitas -->
                    <div class="form-section">
                        <h4>Konektivitas</h4>
                        <div id="connectivitiesContainer">
                            <?php 
                            $idx = 0;
                            foreach($unique_connectivities as $konek): 
                            ?>
                            <div class="connectivity-option">
                                <input type="text" class="form-control" name="konektivitas[<?php echo $idx; ?>]" value="<?php echo htmlspecialchars($konek); ?>" required onchange="generateCombinations()">
                                <button type="button" class="btn-danger-sm" onclick="removeConnectivity(this)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <?php $idx++; endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-outline-primary mt-2" onclick="addConnectivity()">
                            <i class="fas fa-plus"></i> Tambah Konektivitas
                        </button>
                    </div>

                    <!-- Kombinasi -->
                    <div class="form-section">
                        <h4>Stok Kombinasi Variabel</h4>
                        <div class="table-responsive">
                            <div id="combinationsContainer">
                                <!-- Table generated by JS -->
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between pt-3">
                        <a href="iphone.php" class="btn-back"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
                        <button type="submit" class="btn-submit"><i class="fas fa-save me-2"></i>Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Pass Existing Combinations to JS -->
    <script>
        const existingCombinations = <?php echo json_encode($combinations); ?>;
        let colorCount = <?php echo count($images_by_color); ?>;
        let storageCount = <?php echo count($unique_storages); ?>;
        let connectivityCount = <?php echo count($unique_connectivities); ?>;
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Init
        document.addEventListener('DOMContentLoaded', function() {
            generateCombinations();
        });

        document.getElementById('editIphoneForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const overlay = document.getElementById('loadingOverlay');
            overlay.style.display = 'flex';
            
            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Berhasil: ' + data.message);
                    window.location.href = 'iphone.php';
                } else {
                    alert('Gagal: ' + data.message);
                    overlay.style.display = 'none';
                }
            })
            .catch(err => {
                alert('Error sistem');
                console.error(err);
                overlay.style.display = 'none';
            });
        });

        function addColor() {
            const container = document.getElementById('colorsContainer');
            const newIndex = colorCount++;
            const div = document.createElement('div');
            div.className = 'color-option';
            div.innerHTML = `
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Nama Warna</label>
                        <input type="text" class="form-control" name="warna[${newIndex}][nama]" placeholder="Ex: Midnight" required onchange="generateCombinations()">
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Thumbnail</label>
                                <input type="file" class="form-control" name="warna[${newIndex}][thumbnail]" accept="image/*" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">foto Produk</label>
                                <input type="file" class="form-control" name="warna[${newIndex}][product_images][]" multiple accept="image/*">
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-danger-sm" onclick="removeColor(this)">
                    <i class="fas fa-times"></i>
                </button>
            `;
            container.appendChild(div);
        }

        function removeColor(btn) {
            if(document.querySelectorAll('.color-option').length <= 1) {
                alert('Cant remove last color'); return;
            }
            btn.parentElement.remove();
            generateCombinations();
        }

        function addStorage() {
            const container = document.getElementById('storagesContainer');
            const newIndex = storageCount++;
            const div = document.createElement('div');
            div.className = 'storage-option';
            div.innerHTML = `
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Ukuran</label>
                        <input type="text" class="form-control" name="penyimpanan[${newIndex}][size]" placeholder="Ex: 512GB" required onchange="generateCombinations()">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Harga</label>
                        <input type="number" class="form-control" name="penyimpanan[${newIndex}][harga]" min="0" required onchange="generateCombinations()">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Diskon</label>
                        <input type="number" class="form-control" name="penyimpanan[${newIndex}][harga_diskon]" min="0" onchange="generateCombinations()">
                    </div>
                </div>
                <button type="button" class="btn-danger-sm" onclick="removeStorage(this)">
                    <i class="fas fa-times"></i>
                </button>
            `;
            container.appendChild(div);
        }

        function removeStorage(btn) {
            btn.parentElement.remove();
            generateCombinations();
        }

        function addConnectivity() {
            const container = document.getElementById('connectivitiesContainer');
            const newIndex = connectivityCount++;
            const div = document.createElement('div');
            div.className = 'connectivity-option';
            div.innerHTML = `
                <input type="text" class="form-control" name="konektivitas[${newIndex}]" required onchange="generateCombinations()">
                <button type="button" class="btn-danger-sm" onclick="removeConnectivity(this)">
                    <i class="fas fa-times"></i>
                </button>
            `;
            container.appendChild(div);
        }

        function removeConnectivity(btn) {
            btn.parentElement.remove();
            generateCombinations();
        }

        function generateCombinations() {
            const container = document.getElementById('combinationsContainer');
            
            // Get current values
            const colors = [];
            document.querySelectorAll('.color-option input[name$="[nama]"]').forEach(i => {
                if(i.value) colors.push(i.value);
            });
            
            const storages = [];
            document.querySelectorAll('.storage-option').forEach(div => {
                const s = div.querySelector('input[name$="[size]"]').value;
                const p = div.querySelector('input[name$="[harga]"]').value;
                const d = div.querySelector('input[name$="[harga_diskon]"]').value;
                if(s && p) storages.push({size: s, price: p, discount: d});
            });
            
            const connectivities = [];
            document.querySelectorAll('.connectivity-option input[type="text"]').forEach(i => {
                if(i.value) connectivities.push(i.value);
            });

            if(colors.length === 0 || storages.length === 0 || connectivities.length === 0) {
                container.innerHTML = '<p class="text-muted">Lengkapi data di atas untuk melihat tabel stok.</p>';
                return;
            }

            let html = `
                <table class="combination-table">
                    <thead>
                        <tr>
                            <th>Warna</th>
                            <th>Storage</th>
                            <th>Konektivitas</th>
                            <th>Harga</th>
                            <th>Stok</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            let counter = 0;
            colors.forEach(c => {
                storages.forEach(s => {
                    connectivities.forEach(k => {
                        // Find existing stock if matches
                        let stock = 0;
                        const match = existingCombinations.find(ex => 
                            ex.warna === c && ex.penyimpanan === s.size && ex.konektivitas === k
                        );
                        if(match) stock = match.jumlah_stok;

                        html += `
                            <tr>
                                <td>${c}</td>
                                <td>${s.size}</td>
                                <td>${k}</td>
                                <td>
                                    Rp ${parseInt(s.price).toLocaleString()}
                                    ${s.discount ? `<br><small class='text-danger'>Disc: ${parseInt(s.discount).toLocaleString()}</small>` : ''}
                                    <input type="hidden" name="combinations[${counter}][warna]" value="${c}">
                                    <input type="hidden" name="combinations[${counter}][penyimpanan]" value="${s.size}">
                                    <input type="hidden" name="combinations[${counter}][konektivitas]" value="${k}">
                                    <input type="hidden" name="combinations[${counter}][harga]" value="${s.price}">
                                    <input type="hidden" name="combinations[${counter}][harga_diskon]" value="${s.discount}">
                                </td>
                                <td>
                                    <input type="number" class="form-control" style="width:100px" name="combinations[${counter}][jumlah_stok]" value="${stock}" min="0">
                                </td>
                            </tr>
                        `;
                        counter++;
                    });
                });
            });
            
            html += '</tbody></table>';
            container.innerHTML = html;
        }
    </script>
</body>
</html>
