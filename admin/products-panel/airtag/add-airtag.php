<?php
session_start();
require_once '../../db.php';

// Jika belum login, redirect ke login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../../auth/login.php?error=not_logged_in');
    exit();
}

$admin_username = $_SESSION['admin_username'] ?? 'Admin';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk AirTag - iBox Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 20px;
        }
        
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border: none;
        }
        
        .card-header {
            background: linear-gradient(135deg, #4a6cf7 0%, #6a11cb 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 25px;
        }
        
        .card-header h2 {
            margin: 0;
            font-weight: 600;
        }
        
        .card-body {
            padding: 30px;
        }
        
        .form-label {
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px 15px;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #4a6cf7;
            box-shadow: 0 0 0 0.2rem rgba(74, 108, 247, 0.25);
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #444;
            margin: 25px 0 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }
        
        .option-card {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            position: relative;
            transition: all 0.3s;
        }
        
        .option-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            border-color: #d1d9ff;
        }
        
        .btn-add-option {
            background-color: #eef2ff;
            color: #4a6cf7;
            border: 1px dashed #4a6cf7;
            border-radius: 8px;
            padding: 12px;
            width: 100%;
            text-align: center;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-add-option:hover {
            background-color: #4a6cf7;
            color: white;
        }
        
        .btn-remove {
            position: absolute;
            top: 10px;
            right: 10px;
            color: #ff4757;
            background: none;
            border: none;
            font-size: 16px;
            cursor: pointer;
        }
        
        .table-combinations {
            width: 100%;
            margin-top: 20px;
            font-size: 14px;
        }
        
        .table-combinations th {
            background-color: #f8f9fa;
            padding: 12px;
            font-weight: 600;
            border-bottom: 2px solid #eaeaea;
        }
        
        .table-combinations td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #4a6cf7 0%, #6a11cb 100%);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            letter-spacing: 0.5px;
            box-shadow: 0 5px 15px rgba(74, 108, 247, 0.3);
        }
        
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            visibility: hidden;
            opacity: 0;
            transition: all 0.3s;
        }
        
        .loading-overlay.show {
            visibility: visible;
            opacity: 1;
        }
        
        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #4a6cf7;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        #feedback-message {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            padding: 15px 25px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            transform: translateX(150%);
            transition: transform 0.3s ease-out;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        #feedback-message.success {
            background: linear-gradient(135deg, #2ecc71, #26a65b);
            transform: translateX(0);
        }

        #feedback-message.error {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            transform: translateX(0);
        }
        
        /* Color Radio Button Styles */
        .color-radio-group {
            display: flex;
            gap: 12px;
            align-items: center;
            margin-top: 10px;
        }
        
        .color-radio-item {
            position: relative;
        }
        
        .color-radio-item input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .color-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #ddd;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .color-radio-item input[type="radio"]:checked + .color-circle {
            border: 3px solid #4a6cf7;
            box-shadow: 0 0 0 3px rgba(74, 108, 247, 0.2);
            transform: scale(1.1);
        }
        
        .color-circle:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        /* Hex Input with # prefix */
        .hex-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        
        .hex-input-wrapper::before {
            content: '#';
            position: absolute;
            left: 12px;
            color: #666;
            font-weight: 500;
            pointer-events: none;
            z-index: 1;
        }
        
        .hex-input-wrapper input {
            padding-left: 28px !important;
        }
        
        /* Color Picker Integration */
        .color-picker-input {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
            border: none;
        }
    </style>
</head>
<body>
    
    <!-- Feedback Message -->
    <div id="feedback-message"></div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="text-center">
            <div class="spinner mb-3"></div>
            <h5>Menyimpan Produk...</h5>
            <p class="text-muted">Mohon tunggu sebentar</p>
        </div>
    </div>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="airtag.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
            <div class="text-muted">
                Admin Panel / AirTag / Tambah Produk
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-plus-circle me-2"></i> Tambah Produk AirTag Baru</h2>
                <p class="mb-0 opacity-75">Isi formulir lengkap untuk menambahkan produk AirTag baru</p>
            </div>
            
            <form id="addProductForm" enctype="multipart/form-data" autocomplete="off">
                <div class="card-body">
                    
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="nama_produk" class="form-label">Nama Produk</label>
                                <input type="text" class="form-control" id="nama_produk" name="nama_produk" placeholder="Contoh: AirTag" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="kategori" class="form-label">Kategori</label>
                                <input type="text" class="form-control" id="kategori" name="kategori" placeholder="Contoh: AirTag, AirTag 4 Pack, Aksesoris AirTag" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="deskripsi_produk" class="form-label">Deskripsi Produk</label>
                                <textarea class="form-control" id="deskripsi_produk" name="deskripsi_produk" rows="5" placeholder="Tuliskan deskripsi lengkap produk..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Pilihan Warna & Gambar -->
                    <div class="section-title">
                        <i class="fas fa-palette me-2"></i> Pilihan Warna & Gambar
                    </div>
                    
                    <div id="colors-container">
                        <!-- Default Color Added by JS -->
                    </div>
                    
                    <button type="button" class="btn-add-option" onclick="addColorOption()">
                        <i class="fas fa-plus me-1"></i> Tambah Pilihan Warna Lain
                    </button>

                    <!-- Pilihan Pack -->
                    <div class="section-title">
                        <i class="fas fa-box me-2"></i> Pilihan Pack
                    </div>
                    
                    <div id="pack-container">
                         <!-- Default Pack Added by JS -->
                    </div>
                    
                    <button type="button" class="btn-add-option" onclick="addPackOption()">
                        <i class="fas fa-plus me-1"></i> Tambah Opsi Pack Lain
                    </button>

                    <!-- Pilihan Aksesoris -->
                    <div class="section-title">
                        <i class="fas fa-key me-2"></i> Pilihan Aksesoris (Opsional)
                    </div>
                    
                    <div id="aksesoris-container">
                        <!-- Default Aksesoris Added by JS -->
                    </div>
                    <button type="button" class="btn-add-option" onclick="addAksesorisOption()">
                        <i class="fas fa-plus me-1"></i> Tambah Opsi Aksesoris Lain
                    </button>

                    <!-- Tabel Kombinasi -->
                    <div class="section-title">
                        <i class="fas fa-layer-group me-2"></i> Kombinasi & Stok
                        <button type="button" class="btn btn-sm btn-outline-primary float-end" onclick="generateCombinations()">
                            <i class="fas fa-sync me-1"></i> Generate Tabel
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table-combinations" id="combinations-table">
                            <thead>
                                <tr>
                                    <th>Warna</th>
                                    <th>Pack</th>
                                    <th>Aksesoris</th>
                                    <th>Harga (Rp)</th>
                                    <th>Diskon (%)</th>
                                    <th>Stok</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="combinations-body">
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        Lengkapi data di atas untuk melihat kombinasi
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-grid gap-2 mt-5">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i> Simpan Produk
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let colorIndex = 0;
        let packIndex = 0;
        let aksesorisIndex = 0;
        
        // --- Add Color ---
        function addColorOption() {
            const container = document.getElementById('colors-container');
            const html = `
                <div class="option-card color-option position-relative" data-idx="${colorIndex}">
                    <button type="button" class="btn-remove" onclick="removeOption(this)"><i class="fas fa-times"></i></button>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Nama Warna</label>
                                <input type="text" class="form-control color-name" name="warna[${colorIndex}][nama]" placeholder="Contoh: Putih" required onkeyup="updateCombinations()">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Kode Warna (Hex)</label>
                                <div class="hex-input-wrapper">
                                    <input type="text" class="form-control color-hex" name="warna[${colorIndex}][hex_code]" placeholder="000000" oninput="updateColorPreview(this, ${colorIndex})">
                                </div>
                                <small class="text-muted">Contoh: 2c3e50</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Preview Warna</label>
                                <div class="color-radio-group">
                                    <label class="color-radio-item">
                                        <input type="radio" name="color_preview_${colorIndex}" checked>
                                        <div class="color-circle" id="color-preview-${colorIndex}" style="background-color: #cccccc;" title="Klik untuk memilih warna">
                                            <input type="color" class="color-picker-input" id="color-picker-${colorIndex}" onchange="handleColorPicker(this, ${colorIndex})">
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Foto Thumbnail</label>
                                <input type="file" class="form-control" name="warna[${colorIndex}][thumbnail]" accept="image/*" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Foto Galeri</label>
                                <input type="file" class="form-control" name="warna[${colorIndex}][product_images][]" accept="image/*" multiple>
                            </div>
                        </div>
                    </div>
                </div>`;
            container.insertAdjacentHTML('beforeend', html);
            colorIndex++;
            updateCombinations();
        }

        // --- Add Pack ---
        function addPackOption(val = '') {
            const container = document.getElementById('pack-container');
            const html = `
                <div class="option-card pack-option position-relative">
                    <button type="button" class="btn-remove" onclick="removeOption(this)"><i class="fas fa-times"></i></button>
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <label class="form-label">Pack</label>
                            <input type="text" class="form-control pack-value" name="pack[]" value="${val}" placeholder="Contoh: 1 Pack" required onkeyup="updateCombinations()">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Harga Bawaan (Rp)</label>
                            <input type="number" class="form-control base-price" name="pack_harga[]" placeholder="0" required onkeyup="updateCombinations()">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Diskon (%)</label>
                            <input type="number" class="form-control" name="pack_diskon_persen[]" placeholder="0" min="0" max="100" onkeyup="updateCombinations()">
                        </div>
                    </div>
                </div>`;
            container.insertAdjacentHTML('beforeend', html);
            packIndex++;
            updateCombinations();
        }

        // --- Add Aksesoris ---
        function addAksesorisOption(val = '') {
            const container = document.getElementById('aksesoris-container');
            const html = `
                <div class="option-card aksesoris-option position-relative">
                    <button type="button" class="btn-remove" onclick="removeOption(this)"><i class="fas fa-times"></i></button>
                    <div class="row align-items-center">
                        <div class="col-md-11">
                            <input type="text" class="form-control aksesoris-value" name="aksesoris[]" value="${val}" placeholder="Contoh: Leather Key Ring" onkeyup="updateCombinations()">
                        </div>
                    </div>
                </div>`;
            container.insertAdjacentHTML('beforeend', html);
            aksesorisIndex++;
            updateCombinations();
        }

        function removeOption(btn) {
            btn.closest('.option-card').remove();
            updateCombinations();
        }
        
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
                // If invalid, show gray
                preview.style.backgroundColor = '#cccccc';
            }
        }
        
        // Handle color picker selection
        function handleColorPicker(picker, idx) {
            const selectedColor = picker.value; // Format: #rrggbb
            const preview = document.getElementById(`color-preview-${idx}`);
            const hexInput = document.querySelector(`input[name="warna[${idx}][hex_code]"]`);
            
            // Update preview
            if (preview) {
                preview.style.backgroundColor = selectedColor;
            }
            
            // Update hex input (remove # since we show it as prefix)
            if (hexInput) {
                hexInput.value = selectedColor.substring(1); // Remove # from #rrggbb
            }
        }

        function generateCombinations() {
            const colors = Array.from(document.querySelectorAll('.color-name')).map(i => i.value).filter(v => v);
            const packs = Array.from(document.querySelectorAll('.pack-option')).map(row => {
                return {
                    val: row.querySelector('.pack-value').value,
                    price: row.querySelector('.base-price').value,
                    discount: row.querySelector('input[name*="diskon_persen"]').value
                };
            }).filter(p => p.val);
            
            let aksesoris = Array.from(document.querySelectorAll('.aksesoris-value')).map(i => i.value).filter(v => v);
            if(aksesoris.length === 0) aksesoris = ['-'];

            const tbody = document.getElementById('combinations-body');
            tbody.innerHTML = '';

            if (colors.length === 0 || packs.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Data belum lengkap</td></tr>';
                return;
            }

            let idx = 0;
            colors.forEach(c => {
                packs.forEach(p => {
                    aksesoris.forEach(aks => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${c}<input type="hidden" name="combinations[${idx}][warna]" value="${c}"></td>
                            <td>${p.val}<input type="hidden" name="combinations[${idx}][pack]" value="${p.val}"></td>
                            <td>${aks}<input type="hidden" name="combinations[${idx}][aksesoris]" value="${aks}"></td>
                            <td><input type="number" class="form-control form-control-sm" name="combinations[${idx}][harga]" value="${p.price}" required></td>
                            <td><input type="number" class="form-control form-control-sm" name="combinations[${idx}][diskon_persen]" value="${p.discount || 0}" min="0" max="100"></td>
                            <td><input type="number" class="form-control form-control-sm" name="combinations[${idx}][jumlah_stok]" value="" placeholder="0"></td>
                            <td><span class="badge bg-secondary">Draft</span></td>
                        `;
                        tbody.appendChild(tr);
                        idx++;
                    });
                });
            });
        }

        let timeout = null;
        function updateCombinations() {
            clearTimeout(timeout);
            timeout = setTimeout(generateCombinations, 800);
        }

        // --- Init ---
        window.addEventListener('DOMContentLoaded', () => {
            addColorOption();
            addPackOption('1 Pack');
            addAksesorisOption('-');
        });

        // --- Submit ---
        document.getElementById('addProductForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            document.getElementById('loadingOverlay').classList.add('show');

            fetch('api/api-add-airtag.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('loadingOverlay').classList.remove('show');
                if (data.success) {
                    showFeedback(data.message, 'success');
                    setTimeout(() => window.location.href = 'airtag.php', 1500);
                } else {
                    showFeedback(data.message, 'error');
                }
            })
            .catch(err => {
                document.getElementById('loadingOverlay').classList.remove('show');
                showFeedback('Error: ' + err.message, 'error');
            });
        });

        function showFeedback(msg, type) {
            const f = document.getElementById('feedback-message');
            f.textContent = msg;
            f.className = type;
            f.style.transform = 'translateX(0)';
            setTimeout(() => f.style.transform = 'translateX(150%)', 3000);
        }
    </script>
</body>
</html>
