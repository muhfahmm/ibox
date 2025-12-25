<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Produk - Admin iBox</title>
    <link rel="stylesheet" href="../../assets/css/index.css">
    <link rel="stylesheet" href="../assets/css/1-categories/index.css">
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
                        <li class="active">
                            <a href="kategori.php">
                                <i class="fas fa-tags"></i>
                                <span>Kategori</span>
                            </a>
                        </li>
                        <li>
                            <a href="../2-products/produk.php">
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
                    <h1>Manajemen Kategori</h1>
                    <p class="breadcrumb">Admin Panel / Kategori</p>
                </div>
                <div class="header-right">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Cari kategori...">
                    </div>
                    <div class="header-actions">
                        <button class="notification-btn">
                            <i class="fas fa-bell"></i>
                            <span class="notification-count">3</span>
                        </button>
                        <button class="add-product-btn" id="addCategoryBtn">
                            <i class="fas fa-plus"></i>
                            Tambah Kategori
                        </button>
                    </div>
                </div>
            </header>

            <!-- Stats Cards untuk Kategori -->
            <div class="stats-container">
                <div class="stat-card stat-card-primary">
                    <div class="stat-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total iPhone</h3>
                        <p class="stat-number">24</p>
                        <p class="stat-change">
                            <span>Produk tersedia</span>
                        </p>
                    </div>
                </div>

                <div class="stat-card stat-card-success">
                    <div class="stat-icon">
                        <i class="fas fa-laptop"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Mac</h3>
                        <p class="stat-number">12</p>
                        <p class="stat-change">
                            <span>Produk tersedia</span>
                        </p>
                    </div>
                </div>

                <div class="stat-card stat-card-warning">
                    <div class="stat-icon">
                        <i class="fas fa-tablet-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total iPad</h3>
                        <p class="stat-number">8</p>
                        <p class="stat-change">
                            <span>Produk tersedia</span>
                        </p>
                    </div>
                </div>

                <div class="stat-card stat-card-danger">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Watch</h3>
                        <p class="stat-number">15</p>
                        <p class="stat-change">
                            <span>Produk tersedia</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Tabel Kategori -->
            <div class="card full-width">
                <div class="card-header">
                    <h3>Daftar Kategori Produk</h3>
                    <div class="filter-actions">
                        <select class="filter-select">
                            <option>Semua Status</option>
                            <option>Aktif</option>
                            <option>Nonaktif</option>
                        </select>
                        <button class="filter-btn">
                            <i class="fas fa-filter"></i>
                            Filter
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="category-table">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAll">
                                    </th>
                                    <th>ID Kategori</th>
                                    <th>Nama Kategori</th>
                                    <th>Icon</th>
                                    <th>Jumlah Produk</th>
                                    <th>Status</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="checkbox"></td>
                                    <td>CAT-001</td>
                                    <td>
                                        <div class="category-name">
                                            <div class="category-icon-small iphone">
                                                <i class="fas fa-mobile-alt"></i>
                                            </div>
                                            <span>iPhone</span>
                                        </div>
                                    </td>
                                    <td><i class="fas fa-mobile-alt"></i></td>
                                    <td>24 produk</td>
                                    <td><span class="status-badge status-active">Aktif</span></td>
                                    <td>15 Jan 2025</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn edit-btn">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="action-btn delete-btn">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox"></td>
                                    <td>CAT-002</td>
                                    <td>
                                        <div class="category-name">
                                            <div class="category-icon-small mac">
                                                <i class="fas fa-laptop"></i>
                                            </div>
                                            <span>Mac</span>
                                        </div>
                                    </td>
                                    <td><i class="fas fa-laptop"></i></td>
                                    <td>12 produk</td>
                                    <td><span class="status-badge status-active">Aktif</span></td>
                                    <td>15 Jan 2025</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn edit-btn">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="action-btn delete-btn">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox"></td>
                                    <td>CAT-003</td>
                                    <td>
                                        <div class="category-name">
                                            <div class="category-icon-small ipad">
                                                <i class="fas fa-tablet-alt"></i>
                                            </div>
                                            <span>iPad</span>
                                        </div>
                                    </td>
                                    <td><i class="fas fa-tablet-alt"></i></td>
                                    <td>8 produk</td>
                                    <td><span class="status-badge status-active">Aktif</span></td>
                                    <td>20 Jan 2025</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn edit-btn">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="action-btn delete-btn">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox"></td>
                                    <td>CAT-004</td>
                                    <td>
                                        <div class="category-name">
                                            <div class="category-icon-small watch">
                                                <i class="fas fa-clock"></i>
                                            </div>
                                            <span>Watch</span>
                                        </div>
                                    </td>
                                    <td><i class="fas fa-clock"></i></td>
                                    <td>15 produk</td>
                                    <td><span class="status-badge status-active">Aktif</span></td>
                                    <td>22 Jan 2025</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn edit-btn">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="action-btn delete-btn">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox"></td>
                                    <td>CAT-005</td>
                                    <td>
                                        <div class="category-name">
                                            <div class="category-icon-small music">
                                                <i class="fas fa-headphones-alt"></i>
                                            </div>
                                            <span>Music</span>
                                        </div>
                                    </td>
                                    <td><i class="fas fa-headphones-alt"></i></td>
                                    <td>10 produk</td>
                                    <td><span class="status-badge status-active">Aktif</span></td>
                                    <td>25 Jan 2025</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn edit-btn">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="action-btn delete-btn">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox"></td>
                                    <td>CAT-006</td>
                                    <td>
                                        <div class="category-name">
                                            <div class="category-icon-small accessory">
                                                <i class="fas fa-keyboard"></i>
                                            </div>
                                            <span>Aksesori</span>
                                        </div>
                                    </td>
                                    <td><i class="fas fa-keyboard"></i></td>
                                    <td>30 produk</td>
                                    <td><span class="status-badge status-inactive">Nonaktif</span></td>
                                    <td>28 Jan 2025</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn edit-btn">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="action-btn delete-btn">
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
                        <button class="pagination-btn">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal Tambah Kategori -->
            <div class="modal" id="addCategoryModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Tambah Kategori Baru</h3>
                        <button class="close-modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="categoryForm">
                            <div class="form-group">
                                <label for="categoryName">Nama Kategori *</label>
                                <input type="text" id="categoryName" placeholder="Contoh: iPhone" required>
                            </div>
                            <div class="form-group">
                                <label for="categoryIcon">Icon</label>
                                <div class="icon-picker">
                                    <div class="icon-option selected">
                                        <i class="fas fa-mobile-alt"></i>
                                    </div>
                                    <div class="icon-option">
                                        <i class="fas fa-laptop"></i>
                                    </div>
                                    <div class="icon-option">
                                        <i class="fas fa-tablet-alt"></i>
                                    </div>
                                    <div class="icon-option">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="icon-option">
                                        <i class="fas fa-headphones-alt"></i>
                                    </div>
                                    <div class="icon-option">
                                        <i class="fas fa-keyboard"></i>
                                    </div>
                                </div>
                                <input type="hidden" id="selectedIcon" value="fas fa-mobile-alt">
                            </div>
                            <div class="form-group">
                                <label for="categoryDescription">Deskripsi</label>
                                <textarea id="categoryDescription" rows="3" placeholder="Deskripsi kategori..."></textarea>
                            </div>
                            <div class="form-group">
                                <label for="categoryStatus">Status</label>
                                <select id="categoryStatus">
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Nonaktif</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button class="btn-secondary close-modal">Batal</button>
                        <button class="btn-primary" id="saveCategoryBtn">Simpan Kategori</button>
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

        // Modal functionality
        const addCategoryBtn = document.getElementById('addCategoryBtn');
        const addCategoryModal = document.getElementById('addCategoryModal');
        const closeModalBtns = document.querySelectorAll('.close-modal');
        const saveCategoryBtn = document.getElementById('saveCategoryBtn');
        const iconOptions = document.querySelectorAll('.icon-option');
        const selectedIconInput = document.getElementById('selectedIcon');

        // Open modal
        addCategoryBtn.addEventListener('click', function() {
            addCategoryModal.style.display = 'flex';
        });

        // Close modal
        closeModalBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                addCategoryModal.style.display = 'none';
            });
        });

        // Icon selection
        iconOptions.forEach(option => {
            option.addEventListener('click', function() {
                iconOptions.forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
                const iconClass = this.querySelector('i').className;
                selectedIconInput.value = iconClass;
            });
        });

        // Save category
        saveCategoryBtn.addEventListener('click', function() {
            const categoryName = document.getElementById('categoryName').value;
            if (!categoryName) {
                alert('Nama kategori wajib diisi!');
                return;
            }
            alert(`Kategori "${categoryName}" berhasil ditambahkan!`);
            addCategoryModal.style.display = 'none';
            document.getElementById('categoryForm').reset();
        });

        // Select all checkbox
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.category-table tbody input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Edit and delete buttons
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const categoryName = this.closest('tr').querySelector('.category-name span').textContent;
                alert(`Edit kategori: ${categoryName}`);
            });
        });

        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const categoryName = this.closest('tr').querySelector('.category-name span').textContent;
                if (confirm(`Apakah Anda yakin ingin menghapus kategori "${categoryName}"?`)) {
                    alert(`Kategori "${categoryName}" telah dihapus!`);
                }
            });
        });
    </script>
</body>
</html>