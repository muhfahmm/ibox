<?php
session_start();
require_once '../../db.php';

// Redirect if not logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../../auth/login.php');
    exit();
}

$admin_username = $_SESSION['admin_username'] ?? 'Admin';

// Get product ID
if (!isset($_GET['id'])) {
    header('Location: airtag.php');
    exit();
}

$product_id = mysqli_real_escape_string($db, $_GET['id']);
$product = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM admin_produk_airtag WHERE id = '$product_id'"));
if (!$product) {
    header('Location: airtag.php?error=not_found');
    exit();
}

// Fetch colors & images
$colors = [];
$res = mysqli_query($db, "SELECT * FROM admin_produk_airtag_gambar WHERE produk_id='$product_id'");
while ($row = mysqli_fetch_assoc($res)) {
    $colors[] = [
        'warna' => $row['warna'],
        'thumbnail' => $row['foto_thumbnail'],
        'gallery' => json_decode($row['foto_produk'], true) ?? []
    ];
}

// Fetch packs & aksesoris & combos
$packs = [];
$aksesoris = [];
$combos = [];
$res = mysqli_query($db, "SELECT * FROM admin_produk_airtag_kombinasi WHERE produk_id='$product_id'");
while ($row = mysqli_fetch_assoc($res)) {
    $combos[] = $row;
    $packs[$row['pack']] = $row['pack'];
    if (!empty($row['aksesoris'])) {
        $aksesoris[$row['aksesoris']] = $row['aksesoris'];
    }
}
$packs = array_values($packs);
$aksesoris = array_values($aksesoris);

// Prepare initial data for JS
$initialData = [
    'product' => $product,
    'colors' => $colors,
    'packs' => $packs,
    'aksesoris' => $aksesoris,
    'combos' => $combos
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit AirTag - iBox Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {background:#f5f7fb;font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;}
        .container{max-width:1400px;margin:30px auto;padding:20px;}
        .card{border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,0.08);border:none;background:#fff;}
        .card-header{background:linear-gradient(135deg,#4a6cf7 0%,#6a11cb 100%);color:#fff;padding:25px;border-radius:15px 15px 0 0;}
        .card-body{padding:30px;}
        .form-label{font-weight:500;color:#333;margin-bottom:8px;}
        .form-control,.form-select{border:1px solid #ddd;border-radius:8px;padding:10px 15px;transition:all .3s;}
        .form-control:focus,.form-select:focus{border-color:#4a6cf7;box-shadow:0 0 0 .2rem rgba(74,108,247,.25);}
        .section-title{font-size:18px;font-weight:600;color:#444;margin:25px 0 15px;padding-bottom:10px;border-bottom:2px solid #eee;}
        .option-card{background:#fff;border:1px solid #eee;border-radius:10px;padding:15px;margin-bottom:15px;position:relative;transition:all .3s;}
        .option-card:hover{box-shadow:0 5px 15px rgba(0,0,0,.05);border-color:#d1d9ff;}
        .btn-add-option{background:#eef2ff;color:#4a6cf7;border:1px dashed #4a6cf7;border-radius:8px;padding:12px;width:100%;text-align:center;font-weight:500;cursor:pointer;transition:all .3s;}
        .btn-add-option:hover{background:#4a6cf7;color:#fff;}
        .btn-remove{position:absolute;top:10px;right:10px;color:#ff4757;background:none;border:none;font-size:16px;cursor:pointer;}
        .table-combinations{width:100%;margin-top:20px;font-size:14px;}
        .table-combinations th{background:#f8f9fa;padding:12px;font-weight:600;border-bottom:2px solid #eaeaea;}
        .table-combinations td{padding:12px;border-bottom:1px solid #eee;vertical-align:middle;}
        .btn-primary{background:linear-gradient(135deg,#4a6cf7 0%,#6a11cb 100%);border:none;padding:12px 30px;font-weight:600;letter-spacing:.5px;box-shadow:0 5px 15px rgba(74,108,247,.3);}
        .loading-overlay{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(255,255,255,.8);display:flex;justify-content:center;align-items:center;z-index:9999;visibility:hidden;opacity:0;transition:all .3s;}
        .loading-overlay.show{visibility:visible;opacity:1;}
        .spinner{width:50px;height:50px;border:5px solid #f3f3f3;border-top:5px solid #4a6cf7;border-radius:50%;animation:spin 1s linear infinite;}
        @keyframes spin{0%{transform:rotate(0deg);}100%{transform:rotate(360deg);}}
        #feedback-message{position:fixed;top:20px;right:20px;z-index:10000;padding:15px 25px;border-radius:8px;color:#fff;font-weight:500;transform:translateX(150%);transition:transform .3s ease-out;box-shadow:0 5px 15px rgba(0,0,0,.2);}
        #feedback-message.success{background:linear-gradient(135deg,#2ecc71,#26a65b);transform:translateX(0);}
        #feedback-message.error{background:linear-gradient(135deg,#e74c3c,#c0392b);transform:translateX(0);}
    </style>
</head>
<body>
    <div id="feedback-message"></div>
    <div class="loading-overlay" id="loadingOverlay"><div class="text-center"><div class="spinner mb-3"></div><h5>Menyimpan Perubahan...</h5></div></div>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="airtag.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i> Kembali</a>
            <div class="text-muted">Admin Panel / AirTag / Edit Produk</div>
        </div>
        <div class="card">
            <div class="card-header"><h2><i class="fas fa-edit me-2"></i> Edit AirTag</h2></div>
            <form id="editForm" enctype="multipart/form-data" autocomplete="off">
                <div class="card-body">
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Nama Produk</label>
                                <input type="text" class="form-control" name="nama_produk" value="<?php echo htmlspecialchars($product['nama_produk']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kategori</label>
                                <input type="text" class="form-control" name="kategori" value="<?php echo htmlspecialchars($product['kategori'] ?? ''); ?>" placeholder="Contoh: AirTag, AirTag 4 Pack, Aksesoris AirTag" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Deskripsi Produk</label>
                                <textarea class="form-control" name="deskripsi_produk" rows="4"><?php echo htmlspecialchars($product['deskripsi_produk']); ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="section-title"><i class="fas fa-palette me-2"></i> Warna & Gambar</div>
                    <div id="colors-container"></div>
                    <button type="button" class="btn-add-option" onclick="addColorOption()"><i class="fas fa-plus me-1"></i> Tambah Warna</button>
                    <div class="section-title"><i class="fas fa-box me-2"></i> Pilihan Pack</div>
                    <div id="pack-container"></div>
                    <button type="button" class="btn-add-option" onclick="addPackOption()"><i class="fas fa-plus me-1"></i> Tambah Pack</button>
                    <div class="section-title"><i class="fas fa-key me-2"></i> Pilihan Aksesoris (Opsional)</div>
                    <div id="aksesoris-container"></div>
                    <button type="button" class="btn-add-option" onclick="addAksesorisOption()"><i class="fas fa-plus me-1"></i> Tambah Aksesoris</button>
                    <div class="section-title"><i class="fas fa-layer-group me-2"></i> Kombinasi & Stok
                        <button type="button" class="btn btn-sm btn-outline-primary float-end" onclick="generateCombinations()"><i class="fas fa-sync me-1"></i> Generate Tabel</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table-combinations" id="combinations-table">
                            <thead>
                                <tr><th>Warna</th><th>Pack</th><th>Aksesoris</th><th>Harga</th><th>Diskon</th><th>Stok</th><th>Status</th></tr>
                            </thead>
                            <tbody id="combinations-body"><tr><td colspan="7" class="text-center text-muted py-4">Lengkapi data di atas untuk melihat kombinasi</td></tr></tbody>
                        </table>
                    </div>
                    <div class="d-grid gap-2 mt-5">
                        <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save me-2"></i> Update Produk</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const initialData = <?php echo json_encode($initialData); ?>;
        let colorIdx = 0;
        // Add color option (prefill if data provided)
        function addColorOption(name = '', thumb = '', gallery = []) {
            const container = document.getElementById('colors-container');
            const html = `
                <div class="option-card color-option" data-idx="${colorIdx}">
                    <button type="button" class="btn-remove" onclick="removeOption(this)"><i class="fas fa-times"></i></button>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small">Nama Warna</label>
                            <input type="text" class="form-control color-name" name="warna[${colorIdx}][nama]" value="${name}" required onkeyup="generateCombinations()">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Thumbnail</label>
                            <input type="file" class="form-control" name="warna[${colorIdx}][thumbnail]" accept="image/*" ${name ? '' : 'required'}>
                            ${thumb ? `<img src="../../uploads/${thumb}" class="img-thumbnail mt-2" style="width:80px;height:80px;object-fit:cover;">` : ''}
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Galeri</label>
                            <input type="file" class="form-control" name="warna[${colorIdx}][product_images][]" accept="image/*" multiple>
                            ${gallery.length ? gallery.map(g=>`<img src="../../uploads/${g}" class="img-thumbnail mt-2" style="width:60px;height:60px;object-fit:cover; margin-right:4px;">`).join('') : ''}
                        </div>
                    </div>
                </div>`;
            container.insertAdjacentHTML('beforeend', html);
            colorIdx++;
            generateCombinations();
        }
        // Add pack option
        function addPackOption(val = '') {
            const container = document.getElementById('pack-container');
            const html = `
                <div class="option-card pack-option">
                    <button type="button" class="btn-remove" onclick="removeOption(this)"><i class="fas fa-times"></i></button>
                    <div class="row align-items-center">
                        <div class="col-md-4"><label class="form-label">Pack</label><input type="text" class="form-control pack-value" name="pack[]" value="${val}" required onkeyup="generateCombinations()"></div>
                        <div class="col-md-4"><label class="form-label">Harga</label><input type="number" class="form-control base-price" name="pack_harga[]" required onkeyup="generateCombinations()"></div>
                        <div class="col-md-4"><label class="form-label">Diskon (Opsional)</label><input type="number" class="form-control" name="pack_harga_diskon[]"></div>
                    </div>
                </div>`;
            container.insertAdjacentHTML('beforeend', html);
            generateCombinations();
        }
        // Add aksesoris option
        function addAksesorisOption(val = '') {
            const container = document.getElementById('aksesoris-container');
            const html = `
                <div class="option-card aksesoris-option">
                    <button type="button" class="btn-remove" onclick="removeOption(this)"><i class="fas fa-times"></i></button>
                    <input type="text" class="form-control aksesoris-value" name="aksesoris[]" value="${val}" placeholder="Contoh: Leather Key Ring" onkeyup="generateCombinations()">
                </div>`;
            container.insertAdjacentHTML('beforeend', html);
            generateCombinations();
        }
        function removeOption(btn){ btn.closest('.option-card').remove(); generateCombinations(); }
        function generateCombinations(){
            const colors = Array.from(document.querySelectorAll('.color-name')).map(i=>i.value).filter(v=>v);
            const packs = Array.from(document.querySelectorAll('.pack-option')).map(row=>({
                val: row.querySelector('.pack-value').value,
                price: row.querySelector('.base-price').value,
                discount: row.querySelector('input[name*="diskon"]').value
            })).filter(p=>p.val);
            let aks = Array.from(document.querySelectorAll('.aksesoris-value')).map(i=>i.value).filter(v=>v);
            if(aks.length===0) aks=['-'];
            const tbody = document.getElementById('combinations-body');
            tbody.innerHTML='';
            if(colors.length===0||packs.length===0){
                tbody.innerHTML='<tr><td colspan="7" class="text-center text-muted py-4">Data belum lengkap</td></tr>';
                return;
            }
            let idx=0;
            colors.forEach(c=>{
                packs.forEach(p=>{
                    aks.forEach(a=>{
                        const tr=document.createElement('tr');
                        tr.innerHTML=`
                            <td>${c}<input type="hidden" name="combinations[${idx}][warna]" value="${c}"></td>
                            <td>${p.val}<input type="hidden" name="combinations[${idx}][pack]" value="${p.val}"></td>
                            <td>${a}<input type="hidden" name="combinations[${idx}][aksesoris]" value="${a}"></td>
                            <td><input type="number" class="form-control form-control-sm" name="combinations[${idx}][harga]" value="${p.price}" required></td>
                            <td><input type="number" class="form-control form-control-sm" name="combinations[${idx}][harga_diskon]" value="${p.discount||0}"></td>
                            <td><input type="number" class="form-control form-control-sm" name="combinations[${idx}][jumlah_stok]" value="" placeholder="0"></td>
                            <td><span class="badge bg-secondary">Draft</span></td>
                        `;
                        tbody.appendChild(tr);
                        idx++;
                    });
                });
            });
        }
        // Init with existing data
        window.addEventListener('DOMContentLoaded',()=>{
            // Colors
            initialData.colors.forEach(c=>addColorOption(c.warna, c.thumbnail, c.gallery));
            // Packs
            if(initialData.packs.length>0) initialData.packs.forEach(p=>addPackOption(p));
            else addPackOption('1 Pack');
            // Aksesoris
            if(initialData.aksesoris.length>0) initialData.aksesoris.forEach(a=>addAksesorisOption(a));
            else addAksesorisOption('-');
            generateCombinations();
        });
        // Submit
        document.getElementById('editForm').addEventListener('submit',function(e){
            e.preventDefault();
            const formData = new FormData(this);
            document.getElementById('loadingOverlay').classList.add('show');
            fetch('api/api-edit-airtag.php',{
                method:'POST',
                body:formData
            })
            .then(r=>r.json())
            .then(res=>{
                document.getElementById('loadingOverlay').classList.remove('show');
                if(res.success){
                    showFeedback(res.message,'success');
                    setTimeout(()=>window.location.href='airtag.php',1500);
                }else{
                    showFeedback(res.message,'error');
                }
            })
            .catch(err=>{document.getElementById('loadingOverlay').classList.remove('show');showFeedback('Error: '+err.message,'error');});
        });
        function showFeedback(msg,type){
            const f=document.getElementById('feedback-message');
            f.textContent=msg;f.className=type;f.style.transform='translateX(0)';
            setTimeout(()=>f.style.transform='translateX(150%)',3000);
        }
    </script>
</body>
</html>
