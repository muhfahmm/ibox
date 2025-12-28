<?php
session_start();
require_once '../../db.php';

// Jika belum login
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
    <title>Tambah Produk Aksesoris</title>
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
            <a href="aksesoris.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
            <div class="text-muted">
                Admin Panel / Aksesoris / Tambah Produk
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-plus-circle me-2"></i> Tambah Produk Aksesoris Baru</h2>
                <p class="mb-0 opacity-75">Isi formulir lengkap untuk menambahkan produk aksesoris baru</p>
            </div>
            
            <form id="addProductForm" enctype="multipart/form-data">
                <div class="card-body">
                    
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="nama_produk" class="form-label">Nama Produk</label>
                                <input type="text" class="form-control" id="nama_produk" name="nama_produk" placeholder="Contoh: Case iPhone 15 Pro Silicon" required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="kategori" class="form-label">Kategori</label>
                                        <select class="form-select" id="kategori" name="kategori" required>
                                            <option value="">Pilih Kategori</option>
                                            <option value="case">Case</option>
                                            <option value="charger">Charger</option>
                                            <option value="headphone">Headphone</option>
                                            <option value="keyboard">Keyboard</option>
                                            <option value="mouse">Mouse</option>
                                            <option value="trackpad">Trackpad</option>
                                            <option value="adapter">Adapter</option>
                                            <option value="other">Lainnya</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="deskripsi_produk" class="form-label">Deskripsi Produk</label>
                                <textarea class="form-control" id="deskripsi_produk" name="deskripsi_produk" rows="5" placeholder="Tuliskan deskripsi lengkap produk..."></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="kompatibel_dengan" class="form-label">Kompatibel Dengan (Opsional)</label>
                                <input type="text" class="form-control" id="kompatibel_dengan" name="kompatibel_dengan" placeholder="Contoh: iPhone 15, iPhone 14, iPad Pro">
                                <small class="text-muted">Pisahkan dengan koma untuk multiple devices</small>
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

                    <!-- Pilihan Tipe -->
                    <div class="section-title">
                        <i class="fas fa-cogs me-2"></i> Pilihan Tipe
                    </div>
                    
                    <div id="type-container">
                        <!-- Default Type Added by JS -->
                    </div>
                    
                    <button type="button" class="btn-add-option" onclick="addTypeOption()">
                        <i class="fas fa-plus me-1"></i> Tambah Opsi Tipe Lain
                    </button>

                    <!-- Pilihan Ukuran -->
                    <div class="section-title">
                        <i class="fas fa-expand-alt me-2"></i> Pilihan Ukuran (Opsional)
                    </div>
                    
                    <div id="size-container">
                        <!-- Default Size Added by JS -->
                    </div>
                    
                    <button type="button" class="btn-add-option" onclick="addSizeOption()">
                        <i class="fas fa-plus me-1"></i> Tambah Opsi Ukuran Lain
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
                                    <th>Tipe</th>
                                    <th>Ukuran</th>
                                    <th>Harga (Rp)</th>
                                    <th>Diskon (Rp)</th>
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
        let typeIndex = 0;
        let sizeIndex = 0;
        
        // --- Add Color ---
        function addColorOption() {
            const container = document.getElementById('colors-container');
            const html = `
                <div class="option-card color-option position-relative" data-idx="${colorIndex}">
                    <button type="button" class="btn-remove" onclick="removeOption(this, 'color')"><i class="fas fa-times"></i></button>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Nama Warna</label>
                                <input type="text" class="form-control color-name" name="warna[${colorIndex}][nama]" placeholder="Contoh: Midnight Blue, Silver" required onkeyup="updateCombinations()">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Foto Thumbnail (Utama)</label>
                                <input type="file" class="form-control" name="warna[${colorIndex}][thumbnail]" accept="image/*" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Foto Produk (Galeri)</label>
                                <input type="file" class="form-control" name="warna[${colorIndex}][product_images][]" accept="image/*" multiple>
                            </div>
                        </div>
                    </div>
                </div>`;
            container.insertAdjacentHTML('beforeend', html);
            colorIndex++;
            updateCombinations();
        }

        // --- Add Type ---
        function addTypeOption() {
            const container = document.getElementById('type-container');
            const html = `
                <div class="option-card type-option position-relative">
                    <button type="button" class="btn-remove" onclick="removeOption(this, 'type')"><i class="fas fa-times"></i></button>
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <label class="form-label">Tipe</label>
                            <input type="text" class="form-control type-value" name="tipe[${typeIndex}][nama]" placeholder="Contoh: Silicon, Leather" required onkeyup="updateCombinations()">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Harga Dasar (Rp)</label>
                            <input type="number" class="form-control base-price" name="tipe[${typeIndex}][harga]" placeholder="0" required onkeyup="updateCombinations()">
                        </div>
                    </div>
                </div>`;
            container.insertAdjacentHTML('beforeend', html);
            typeIndex++;
            updateCombinations();
        }

        // --- Add Size ---
        function addSizeOption() {
            const container = document.getElementById('size-container');
            const html = `
                <div class="option-card size-option position-relative">
                    <button type="button" class="btn-remove" onclick="removeOption(this, 'size')"><i class="fas fa-times"></i></button>
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <label class="form-label">Ukuran (Opsional)</label>
                            <input type="text" class="form-control size-value" name="ukuran[${sizeIndex}][size]" placeholder="Contoh: S, M, L atau 10 inch" onkeyup="updateCombinations()">
                        </div>
                    </div>
                </div>`;
            container.insertAdjacentHTML('beforeend', html);
            sizeIndex++;
            updateCombinations();
        }

        function removeOption(btn, type) {
            btn.closest('.option-card').remove();
            updateCombinations();
        }

        function generateCombinations() {
            const colors = Array.from(document.querySelectorAll('.color-name')).map(i => i.value).filter(v => v);
            const types = Array.from(document.querySelectorAll('.type-option')).map(row => {
                return {
                    name: row.querySelector('input[name*="[nama]"]').value,
                    price: row.querySelector('input[name*="[harga]"]').value
                };
            }).filter(t => t.name);
            
            const sizes = Array.from(document.querySelectorAll('.size-value')).map(i => i.value).filter(v => v);
            // Jika tidak ada ukuran, gunakan array dengan satu elemen kosong
            const sizeList = sizes.length > 0 ? sizes : ['-'];

            const tbody = document.getElementById('combinations-body');
            tbody.innerHTML = '';

            if (colors.length === 0 || types.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Data belum lengkap</td></tr>';
                return;
            }

            let idx = 0;
            colors.forEach(c => {
                types.forEach(t => {
                    sizeList.forEach(s => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${c}<input type="hidden" name="combinations[${idx}][warna]" value="${c}"></td>
                            <td>${t.name}<input type="hidden" name="combinations[${idx}][tipe]" value="${t.name}"></td>
                            <td>${s}<input type="hidden" name="combinations[${idx}][ukuran]" value="${s}"></td>
                            <td><input type="number" class="form-control form-control-sm" name="combinations[${idx}][harga]" value="${t.price}" required></td>
                            <td><input type="number" class="form-control form-control-sm" name="combinations[${idx}][harga_diskon]" placeholder="0"></td>
                            <td><input type="number" class="form-control form-control-sm" name="combinations[${idx}][jumlah_stok]" value="0" required></td>
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
            addTypeOption();
            addSizeOption();
        });

        // --- Submit ---
        document.getElementById('addProductForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            document.getElementById('loadingOverlay').classList.add('show');

            fetch('api/api-add-aksesoris.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('loadingOverlay').classList.remove('show');
                if (data.success) {
                    showFeedback(data.message, 'success');
                    setTimeout(() => window.location.href = 'aksesoris.php', 1500);
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