<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk - Admin iBox</title>
    <link rel="stylesheet" href="../assets/css/2-products/products.css">
    <link rel="stylesheet" href="../../assets/css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <i class="fas fa-apple-alt"></i>
                    <h2>iBox Admin</h2>
                </div>
                <button class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <div class="sidebar-menu">
                <div class="menu-section">
                    <h3 class="section-title">Menu Utama</h3>
                    <ul>
                        <li>
                            <a href="../../index.php">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="../1-categories/kategori.php">
                                <i class="fas fa-tags"></i>
                                <span>Kategori</span>
                            </a>
                        </li>
                        <li class="active">
                            <a href="produk.php">
                                <i class="fas fa-box"></i>
                                <span>Produk</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="menu-section">
                    <h3 class="section-title">Manajemen Produk</h3>
                    <ul>
                        <li>
                            <a href="#">
                                <i class="fas fa-mobile-alt"></i>
                                <span>iPhone</span>
                                <span class="badge">24</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fas fa-laptop"></i>
                                <span>Mac</span>
                                <span class="badge">12</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fas fa-tablet-alt"></i>
                                <span>iPad</span>
                                <span class="badge">8</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fas fa-clock"></i>
                                <span>Watch</span>
                                <span class="badge">15</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fas fa-headphones-alt"></i>
                                <span>Music</span>
                                <span class="badge">10</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="menu-section">
                    <h3 class="section-title">Lainnya</h3>
                    <ul>
                        <li>
                            <a href="#">
                                <i class="fas fa-users"></i>
                                <span>Pengguna</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fas fa-shopping-cart"></i>
                                <span>Pesanan</span>
                                <span class="badge badge-warning">5</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fas fa-chart-line"></i>
                                <span>Analitik</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fas fa-cog"></i>
                                <span>Pengaturan</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="sidebar-footer">
                <div class="user-profile">
                    <img src="https://ui-avatars.com/api/?name=Admin+iBox&background=4a6cf7&color=fff" alt="Admin">
                    <div class="user-info">
                        <h4>Admin iBox</h4>
                        <p>admin@ibox.co.id</p>
                    </div>
                    <a href="#" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="main-header">
                <div class="header-left">
                    <h1>Manajemen Produk</h1>
                    <p class="breadcrumb">Admin Panel / Produk</p>
                </div>
                <div class="header-right">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Cari produk, SKU, kategori...">
                    </div>
                    <div class="header-actions">
                        <button class="notification-btn">
                            <i class="fas fa-bell"></i>
                            <span class="notification-count">3</span>
                        </button>
                        <button class="add-product-btn" id="addProductBtn">
                            <i class="fas fa-plus"></i>
                            Tambah Produk
                        </button>
                    </div>
                </div>
            </header>

            <!-- Filter Section -->
            <div class="filter-section">
                <div class="filter-tabs">
                    <button class="filter-tab active">Semua Produk <span class="tab-count">89</span></button>
                    <button class="filter-tab">iPhone <span class="tab-count">24</span></button>
                    <button class="filter-tab">Mac <span class="tab-count">12</span></button>
                    <button class="filter-tab">iPad <span class="tab-count">8</span></button>
                    <button class="filter-tab">Watch <span class="tab-count">15</span></button>
                    <button class="filter-tab">Music <span class="tab-count">10</span></button>
                </div>
                <div class="filter-controls">
                    <select class="filter-select">
                        <option>Semua Status Stok</option>
                        <option>Tersedia</option>
                        <option>Menipis</option>
                        <option>Habis</option>
                    </select>
                    <select class="filter-select">
                        <option>Urutkan berdasarkan</option>
                        <option>Nama A-Z</option>
                        <option>Nama Z-A</option>
                        <option>Harga Tertinggi</option>
                        <option>Harga Terendah</option>
                        <option>Stok Terbanyak</option>
                    </select>
                    <button class="filter-btn">
                        <i class="fas fa-filter"></i>
                        Terapkan Filter
                    </button>
                    <button class="export-btn">
                        <i class="fas fa-download"></i>
                        Export
                    </button>
                </div>
            </div>

            <!-- Tabel Produk -->
            <div class="card full-width">
                <div class="card-header">
                    <h3>Daftar Produk</h3>
                    <div class="bulk-actions">
                        <select class="bulk-select">
                            <option>Aksi Massal</option>
                            <option>Ubah Status</option>
                            <option>Hapus Produk</option>
                            <option>Update Harga</option>
                            <option>Update Stok</option>
                        </select>
                        <button class="apply-bulk-btn">Terapkan</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="product-table">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAllProducts">
                                    </th>
                                    <th>Gambar</th>
                                    <th>Nama Produk</th>
                                    <th>SKU</th>
                                    <th>Kategori</th>
                                    <th>Stok</th>
                                    <th>Harga</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="checkbox"></td>
                                    <td>
                                        <div class="product-table-image">
                                            <img src="https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=80&h=80&fit=crop" alt="iPhone 17 Pro">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="product-table-info">
                                            <h4>iPhone 17 Pro</h4>
                                            <p>Model: Cosmic Orange • 256GB</p>
                                        </div>
                                    </td>
                                    <td>IP17P-256-OR</td>
                                    <td>
                                        <span class="category-tag iphone-tag">
                                            <i class="fas fa-mobile-alt"></i>
                                            iPhone
                                        </span>
                                    </td>
                                    <td>
                                        <div class="stock-info">
                                            <span class="stock-value">45</span>
                                            <div class="stock-bar">
                                                <div class="stock-fill high-stock"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="price-info">
                                            <span class="current-price">Rp 23.749.000</span>
                                            <span class="original-price">Rp 25.999.000</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-badge status-published">Published</span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn view-btn" title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="action-btn edit-btn" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="action-btn duplicate-btn" title="Duplikat">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                            <button class="action-btn delete-btn" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox"></td>
                                    <td>
                                        <div class="product-table-image">
                                            <img src="https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=80&h=80&fit=crop" alt="MacBook Pro">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="product-table-info">
                                            <h4>MacBook Pro 16"</h4>
                                            <p>M3 Pro • Space Gray • 1TB</p>
                                        </div>
                                    </td>
                                    <td>MBP16-M3P-1TB</td>
                                    <td>
                                        <span class="category-tag mac-tag">
                                            <i class="fas fa-laptop"></i>
                                            Mac
                                        </span>
                                    </td>
                                    <td>
                                        <div class="stock-info">
                                            <span class="stock-value">12</span>
                                            <div class="stock-bar">
                                                <div class="stock-fill medium-stock"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="price-info">
                                            <span class="current-price">Rp 38.999.000</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-badge status-published">Published</span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn view-btn" title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="action-btn edit-btn" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="action-btn duplicate-btn" title="Duplikat">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                            <button class="action-btn delete-btn" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox"></td>
                                    <td>
                                        <div class="product-table-image">
                                            <img src="https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=80&h=80&fit=crop" alt="iPad Pro">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="product-table-info">
                                            <h4>iPad Pro 12.9"</h4>
                                            <p>Silver • 512GB • Wi-Fi + Cellular</p>
                                        </div>
                                    </td>
                                    <td>IP12P-512-SL</td>
                                    <td>
                                        <span class="category-tag ipad-tag">
                                            <i class="fas fa-tablet-alt"></i>
                                            iPad
                                        </span>
                                    </td>
                                    <td>
                                        <div class="stock-info">
                                            <span class="stock-value">8</span>
                                            <div class="stock-bar">
                                                <div class="stock-fill low-stock"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="price-info">
                                            <span class="current-price">Rp 21.499.000</span>
                                            <span class="original-price">Rp 24.999.000</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-badge status-published">Published</span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn view-btn" title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="action-btn edit-btn" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="action-btn duplicate-btn" title="Duplikat">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                            <button class="action-btn delete-btn" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox"></td>
                                    <td>
                                        <div class="product-table-image">
                                            <img src="https://images.unsplash.com/photo-1434493650001-5d43a6fea0a5?w=80&h=80&fit=crop" alt="Apple Watch">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="product-table-info">
                                            <h4>Apple Watch Series 9</h4>
                                            <p>45mm • Midnight • GPS + Cellular</p>
                                        </div>
                                    </td>
                                    <td>AWS9-45-MD</td>
                                    <td>
                                        <span class="category-tag watch-tag">
                                            <i class="fas fa-clock"></i>
                                            Watch
                                        </span>
                                    </td>
                                    <td>
                                        <div class="stock-info">
                                            <span class="stock-value">0</span>
                                            <div class="stock-bar">
                                                <div class="stock-fill no-stock"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="price-info">
                                            <span class="current-price">Rp 8.999.000</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-badge status-draft">Draft</span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn view-btn" title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="action-btn edit-btn" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="action-btn duplicate-btn" title="Duplikat">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                            <button class="action-btn delete-btn" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox"></td>
                                    <td>
                                        <div class="product-table-image">
                                            <img src="https://images.unsplash.com/photo-1600294037681-c80b5cb6c7cb?w=80&h=80&fit=crop" alt="AirPods Pro">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="product-table-info">
                                            <h4>AirPods Pro (2nd Gen)</h4>
                                            <p>White • Wireless Charging Case</p>
                                        </div>
                                    </td>
                                    <td>APP2-WH</td>
                                    <td>
                                        <span class="category-tag music-tag">
                                            <i class="fas fa-headphones-alt"></i>
                                            Music
                                        </span>
                                    </td>
                                    <td>
                                        <div class="stock-info">
                                            <span class="stock-value">25</span>
                                            <div class="stock-bar">
                                                <div class="stock-fill high-stock"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="price-info">
                                            <span class="current-price">Rp 4.299.000</span>
                                            <span class="original-price">Rp 4.999.000</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-badge status-published">Published</span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn view-btn" title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="action-btn edit-btn" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="action-btn duplicate-btn" title="Duplikat">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                            <button class="action-btn delete-btn" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="pagination">
                        <button class="pagination-btn" disabled>
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="pagination-btn active">1</button>
                        <button class="pagination-btn">2</button>
                        <button class="pagination-btn">3</button>
                        <button class="pagination-btn">4</button>
                        <span class="pagination-ellipsis">...</span>
                        <button class="pagination-btn">10</button>
                        <button class="pagination-btn">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <div class="pagination-info">
                            Menampilkan 1-5 dari 89 produk
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="main-footer">
                <p>&copy; 2025 iBox Admin Panel. All rights reserved.</p>
                <p>v1.0.0</p>
            </footer>
        </main>
    </div>

    <script>
        // Toggle sidebar on mobile
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
        });

        // Filter tabs
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                const tabText = this.textContent.split(' ')[0];
                if (tabText !== 'Semua') {
                    alert(`Memfilter produk: ${tabText}`);
                }
            });
        });

        // Select all checkbox
        document.getElementById('selectAllProducts').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.product-table tbody input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Product action buttons
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const productName = this.closest('tr').querySelector('.product-table-info h4').textContent;
                alert(`Melihat detail: ${productName}`);
            });
        });

        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const productName = this.closest('tr').querySelector('.product-table-info h4').textContent;
                alert(`Mengedit: ${productName}`);
            });
        });

        document.querySelectorAll('.duplicate-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const productName = this.closest('tr').querySelector('.product-table-info h4').textContent;
                if (confirm(`Duplikat produk "${productName}"?`)) {
                    alert(`Produk "${productName}" telah diduplikat!`);
                }
            });
        });

        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const productName = this.closest('tr').querySelector('.product-table-info h4').textContent;
                if (confirm(`Apakah Anda yakin ingin menghapus produk "${productName}"?`)) {
                    alert(`Produk "${productName}" telah dihapus!`);
                }
            });
        });

        // Add product button
        document.getElementById('addProductBtn').addEventListener('click', function() {
            alert('Membuka form tambah produk baru');
            // In actual implementation, you would redirect to add product form or show modal
        });

        // Apply bulk action
        document.querySelector('.apply-bulk-btn').addEventListener('click', function() {
            const selectedAction = document.querySelector('.bulk-select').value;
            if (selectedAction !== 'Aksi Massal') {
                alert(`Melakukan aksi massal: ${selectedAction}`);
            }
        });
    </script>
</body>
</html>