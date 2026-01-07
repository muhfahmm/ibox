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
        'warna' => trim($row['warna']),
        'hex_code' => $row['hex_code'] ?? '',
        'thumbnail' => $row['foto_thumbnail'],
        'gallery' => json_decode($row['foto_produk'], true) ?? []
    ];
}

// Fetch packs & aksesoris & combos
$packs = [];
$aksesoris = [];
$combos = [];
$stocks_map = [];
$prices_map = [];
$discounts_map = [];

$res = mysqli_query($db, "SELECT * FROM admin_produk_airtag_kombinasi WHERE produk_id='$product_id'");
while ($row = mysqli_fetch_assoc($res)) {
    $combos[] = $row;
    
    $trimmed_pack = trim($row['pack']);
    $packs[$trimmed_pack] = $trimmed_pack;
    
    if (!empty($row['aksesoris'])) {
        $trimmed_aks = trim($row['aksesoris']);
        $aksesoris[$trimmed_aks] = $trimmed_aks;
    }
    
    // Create maps for stock, price, and discount lookup
    // Normalize aksesoris: use '-' if empty to match JavaScript
    $aks_value = trim($row['aksesoris']);
    if (empty($aks_value)) {
        $aks_value = '-';
    }
    
    $key = trim($row['warna']) . '|' . $trimmed_pack . '|' . $aks_value;
    $stocks_map[$key] = $row['jumlah_stok'];
    $prices_map[$key] = $row['harga'];
    $discounts_map[$key] = (!empty($row['harga_diskon']) && $row['harga_diskon'] > 0 && $row['harga'] > 0) 
        ? round((($row['harga'] - $row['harga_diskon']) / $row['harga']) * 100) 
        : '';
}
$packs = array_values($packs);
$aksesoris = array_values($aksesoris);

// Prepare initial data for JS
$initialData = [
    'product' => $product,
    'colors' => $colors,
    'packs' => $packs,
    'aksesoris' => $aksesoris,
    'combos' => $combos,
    'stocks' => $stocks_map,
    'prices' => $prices_map,
    'discounts' => $discounts_map
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
        .color-radio-group{display:flex;gap:12px;align-items:center;margin-top:10px;}
        .color-radio-item{position:relative;}
        .color-radio-item input[type="radio"]{position:absolute;opacity:0;width:0;height:0;}
        .color-circle{width:40px;height:40px;border-radius:50%;border:2px solid #ddd;cursor:pointer;transition:all .3s ease;position:relative;box-shadow:0 2px 5px rgba(0,0,0,.1);}
        .color-radio-item input[type="radio"]:checked + .color-circle{border:3px solid #4a6cf7;box-shadow:0 0 0 3px rgba(74,108,247,.2);transform:scale(1.1);}
        .color-circle:hover{transform:scale(1.05);box-shadow:0 4px 8px rgba(0,0,0,.15);}
        .hex-input-wrapper{position:relative;display:flex;align-items:center;}
        .hex-input-wrapper::before{content:'#';position:absolute;left:12px;color:#666;font-weight:500;pointer-events:none;z-index:1;}
        .hex-input-wrapper input{padding-left:28px !important;}
        .color-picker-input{position:absolute;top:0;left:0;width:100%;height:100%;opacity:0;cursor:pointer;border:none;}
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
                                <tr><th>Warna</th><th>Pack</th><th>Aksesoris</th><th>Harga</th><th>Diskon (%)</th><th>Stok</th><th>Status</th></tr>
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
        function addColorOption(name = '', hexCode = '', thumb = '', gallery = []) {
            const container = document.getElementById('colors-container');
            const html = `
                <div class="option-card color-option" data-idx="${colorIdx}">
                    <button type="button" class="btn-remove" onclick="removeOption(this)"><i class="fas fa-times"></i></button>
                    <!-- Hidden inputs for existing images -->
                    <input type="hidden" name="warna[${colorIdx}][existing_thumbnail]" value="${thumb}">
                    <input type="hidden" name="warna[${colorIdx}][existing_gallery]" value='${JSON.stringify(gallery)}'>

                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small">Nama Warna</label>
                            <input type="text" class="form-control color-name" name="warna[${colorIdx}][nama]" value="${name}" required onkeyup="generateCombinations()">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Kode Hex</label>
                            <div class="hex-input-wrapper">
                                <input type="text" class="form-control" name="warna[${colorIdx}][hex_code]" value="${hexCode.replace('#', '')}" placeholder="000000" oninput="updateColorPreview(this, ${colorIdx})">
                            </div>
                            <small class="text-muted">Contoh: 2c3e50</small>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label small">Preview</label>
                            <div class="color-radio-group">
                                <label class="color-radio-item">
                                    <input type="radio" name="color_preview_${colorIdx}" checked>
                                    <div class="color-circle" id="color-preview-${colorIdx}" style="background-color: ${hexCode || '#cccccc'};" title="Klik untuk memilih warna">
                                        <input type="color" class="color-picker-input" id="color-picker-${colorIdx}" value="${hexCode || '#cccccc'}" onchange="handleColorPicker(this, ${colorIdx})">
                                    </div>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Thumbnail</label>
                            <input type="file" class="form-control" name="warna[${colorIdx}][thumbnail]" accept="image/*" ${name ? '' : 'required'}>
                            ${thumb ? `<img src="../../uploads/${thumb}" class="img-thumbnail mt-2" style="width:80px;height:80px;object-fit:cover;">` : ''}
                        </div>
                        <div class="col-md-3">
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
        function addPackOption(data = null) {
            const val = typeof data === 'string' ? data : (data?.val || '');
            const price = data?.price || '';
            const discount = data?.discount || '';
            
            const container = document.getElementById('pack-container');
            const html = `
                <div class="option-card pack-option">
                    <button type="button" class="btn-remove" onclick="removeOption(this)"><i class="fas fa-times"></i></button>
                    <div class="row align-items-center">
                        <div class="col-md-4"><label class="form-label">Pack</label><input type="text" class="form-control pack-value" name="pack[]" value="${val}" required onkeyup="generateCombinations()"></div>
                        <div class="col-md-4"><label class="form-label">Harga</label><input type="number" class="form-control base-price" name="pack_harga[]" value="${price}" required onkeyup="generateCombinations()"></div>
                        <div class="col-md-4"><label class="form-label">Diskon (%)</label><input type="number" class="form-control" name="pack_diskon_persen[]" value="${discount}" min="0" max="100"></div>
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
        
        // Update color preview when hex code changes
        function updateColorPreview(input, idx) {
            let hexValue = input.value.trim();
            const preview = document.getElementById(`color-preview-${idx}`);
            
            // Add # if not present
            if (hexValue && !hexValue.startsWith('#')) {
                hexValue = '#' + hexValue;
            }
            
            // Validate hex color
            const hexRegex = /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/;
            if (hexRegex.test(hexValue)) {
                preview.style.backgroundColor = hexValue;
            } else {
                preview.style.backgroundColor = '#cccccc';
            }
        }
        
        // Handle color picker selection
        function handleColorPicker(picker, idx) {
            const selectedColor = picker.value;
            const preview = document.getElementById(`color-preview-${idx}`);
            const hexInput = document.querySelector(`input[name="warna[${idx}][hex_code]"]`);
            
            if (preview) {
                preview.style.backgroundColor = selectedColor;
            }
            
            if (hexInput) {
                hexInput.value = selectedColor.substring(1);
            }
        }
        function generateCombinations(){
            // Use colors from initialData to ensure exact match with database keys
            const colors = initialData.colors.map(c => c.warna);
            
            const packs = Array.from(document.querySelectorAll('.pack-option')).map(row=>({
                val: row.querySelector('.pack-value').value.trim(),
                price: row.querySelector('.base-price').value,
                discount: row.querySelector('input[name*="diskon_persen"]').value
            })).filter(p=>p.val);
            let aks = Array.from(document.querySelectorAll('.aksesoris-value')).map(i=>i.value.trim()).filter(v=>v);
            if(aks.length===0) aks=['-'];
            const tbody = document.getElementById('combinations-body');
            tbody.innerHTML='';
            if(colors.length===0||packs.length===0){
                tbody.innerHTML='<tr><td colspan="7" class="text-center text-muted py-4">Data belum lengkap</td></tr>';
                return;
            }
            let idx=0;
            
            // Debug: Log the initialData once
            console.log('=== DEBUG STOCKS ===');
            console.log('initialData.stocks:', initialData.stocks);
            console.log('initialData.prices:', initialData.prices);
            
            colors.forEach(c=>{
                packs.forEach(p=>{
                    aks.forEach(a=>{
                        // Create key for lookup - exact match with PHP
                        const key = `${c}|${p.val}|${a}`;
                        console.log('Generated key:', key);
                        
                        const existingStock = initialData.stocks[key] !== undefined ? initialData.stocks[key] : '';
                        const existingPrice = initialData.prices[key] !== undefined ? initialData.prices[key] : p.price;
                        const existingDiscount = initialData.discounts[key] !== undefined ? initialData.discounts[key] : (p.discount || '');
                        
                        console.log('Stock found:', existingStock, 'for key:', key);
                        
                        const tr=document.createElement('tr');
                        tr.innerHTML=`
                            <td>${c}<input type="hidden" name="combinations[${idx}][warna]" value="${c}"></td>
                            <td>${p.val}<input type="hidden" name="combinations[${idx}][pack]" value="${p.val}"></td>
                            <td>${a}<input type="hidden" name="combinations[${idx}][aksesoris]" value="${a}"></td>
                            <td><input type="number" class="form-control form-control-sm" name="combinations[${idx}][harga]" value="${existingPrice}" required></td>
                            <td><input type="number" class="form-control form-control-sm" name="combinations[${idx}][diskon_persen]" value="${existingDiscount}" placeholder="0" min="0" max="100"></td>
                            <td><input type="number" class="form-control form-control-sm" name="combinations[${idx}][jumlah_stok]" value="${existingStock}" placeholder="0"></td>
                            <td><span class="badge bg-${existingStock ? 'success' : 'secondary'}">${existingStock ? 'Ready' : 'Draft'}</span></td>
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
            initialData.colors.forEach(c=>addColorOption(c.warna, c.hex_code, c.thumbnail, c.gallery));
            
            // Packs - get price from combos
            if(initialData.packs.length>0) {
                initialData.packs.forEach(p=>{
                    // Find first combo with this pack to get price
                    const combo = initialData.combos.find(c => c.pack === p);
                    const packData = {
                        val: p,
                        price: combo ? combo.harga : '',
                        discount: combo && combo.harga_diskon > 0 && combo.harga > 0 ? Math.round(((combo.harga - combo.harga_diskon) / combo.harga) * 100) : ''
                    };
                    addPackOption(packData);
                });
            } else {
                addPackOption('1 Pack');
            }
            
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
