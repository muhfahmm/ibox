# iBox Indonesia Clone - Proyek Pengembangan Web

## 📋 Ringkasan Proyek
Proyek ini adalah pengembangan ulang (clone) website iBox Indonesia dengan tingkat presisi tinggi (high-fidelity), dirancang sebagai platform e-commerce komprehensif khusus produk Apple. Website ini mensimulasikan pengalaman pengguna premium yang ditemukan pada situs aslinya, mengedepankan desain modern yang responsif serta fungsionalitas backend yang kuat untuk manajemen produk yang kompleks dan transaksi pengguna.

## ✨ Fitur Utama & Detail Implementasi

### 🎨 Antarmuka Pengguna (Front-end)
*   **Desain Modern & Responsif**:
    *   Dibangun murni menggunakan **HTML5, CSS3, dan JavaScript (ES6+)**.
    *   Layout yang beradaptasi sempurna di Desktop, Tablet, dan Mobile.
    *   Implementasi navigasi pintar dengan menu dropdown yang dinamis dan *sticky navbar*.
*   **Estetika Glassmorphism & Premium UI**:
    *   Penggunaan efek *backdrop-filter: blur* untuk menciptakan nuansa kaca pada modal, navigasi, dan kartu produk.
    *   Efek hover tingkat lanjut pada tombol (skala, bayangan, riak air/ripple) untuk interaksi yang memuaskan.
    *   Tipografi modern menggunakan font *San Francisco* style (Inter/Roboto) untuk keterbacaan maksimal.
*   **Halaman Produk & Checkout yang Dinamis**:
    *   **Sinkronisasi Varian Real-time**:
        *   Memilih warna pada *thumbnail* akan otomatis mengubah tombol radio varian yang aktif.
        *   Sebaliknya, memilih varian warna akan memfilter galeri foto utama dan grid foto di bawahnya.
        *   Sistem deteksi otomatis untuk menampilkan gambar yang sesuai dengan kode warna (Hex) yang dipilih.
    *   **Manajemen Stok Varian**: Harga dan ketersediaan stok berubah secara dinamis berdasarkan kombinasi (Warna + Kapasitas + Konektivitas) yang dipilih pengguna.
    *   **Galeri Foto Interaktif**: Fitur *lightbox* modal dengan navigasi (panah kiri/kanan & keyboard) untuk meninjau detail produk.
    *   **Manajemen Alamat Modular**: Pengguna dapat Tambah, Edit, Hapus, dan Pilih alamat pengiriman langsung di halaman checkout melalui modal tanpa *reload* halaman.
    *   **🗺️ Pemilihan Lokasi via Maps (NEW!)**:
        *   Integrasi **Leaflet.js** untuk peta interaktif di modal alamat pengiriman.
        *   Fitur **pencarian lokasi** menggunakan Nominatim API (OpenStreetMap).
        *   **Auto-detect lokasi** pengguna menggunakan Geolocation API.
        *   **Draggable marker** untuk penyesuaian posisi yang presisi.
        *   Penyimpanan **koordinat latitude/longitude** dan **Google Maps URL** ke database.
        *   Tampilan koordinat real-time saat memilih lokasi di peta.

### 🔧 Backend & Administrasi
*   **Arsitektur Sistem**: Menggunakan **PHP Native** dan database **MySQL** untuk performa yang optimal dan kontrol penuh atas query database.
*   **Panel Admin Canggih**:
    *   **CRUD Produk Multi-Kategori**: Dukungan khusus untuk iPhone, iPad, Mac, Watch, dan Aksesoris dengan struktur data unik untuk masing-masing tipe.
    *   **Editor Produk Visual**: 
        *   Fitur *preview* warna real-time saat mengedit data produk.
        *   Input Kode Hex yang tersinkronisasi otomatis dengan *Color Picker*.
        *   Upload multitipal gambar dengan preview instan sebelum disimpan.
    *   **Sistem Stok Kombinasi**: Logika backend untuk memetakan ribuan kemungkinan kombinasi varian produk (misal: iPhone 13 - Pink - 128GB) dan melacak stoknya secara akurat.
    *   **Manajemen Pesanan**: Pelacakan status pesanan dari "Menunggu Pembayaran" hingga "Selesai".
*   **Optimasi Database**: 
    *   Penyimpanan gambar dalam format array JSON untuk efisiensi.
    *   Relasi tabel yang terstruktur untuk pengguna, produk, keranjang, dan transaksi.
    *   **Index koordinat** untuk performa query lokasi yang optimal.

## 🛠️ Stack Teknologi
*   **Frontend**: HTML5, CSS3 (Custom Design System + Utilitas), JavaScript (Vanilla ES6)
*   **Backend**: PHP 7/8 (Procedural & Object Oriented)
*   **Database**: MySQL / MariaDB
*   **Server**: Apache (Lingkungan XAMPP)
*   **Maps**: Leaflet.js v1.9.4 + OpenStreetMap Tiles
*   **Geocoding**: Nominatim API (OpenStreetMap)

## 📦 Panduan Instalasi & Setup

1.  **Clone Repositori**
    ```bash
    git clone [url-repositori-anda]
    ```

2.  **Persiapan Server Lokal**
    *   Pastikan **XAMPP** (atau stack AMP serupa) sudah terinstall.
    *   Pindahkan folder proyek ke dalam direktori `htdocs` (biasanya `C:\xampp\htdocs\ibox`).

3.  **Konfigurasi Database**
    *   Buka **phpMyAdmin** (`http://localhost/phpmyadmin`).
    *   Buat database baru dengan nama `db_ibox`.
    *   Import file database yang tersedia.
    *   **Update Database untuk Fitur Maps**:
        ```sql
        ALTER TABLE `user_alamat` 
        ADD COLUMN `latitude` DECIMAL(10, 8) NULL DEFAULT NULL COMMENT 'Koordinat latitude dari maps' AFTER `kode_post`,
        ADD COLUMN `longitude` DECIMAL(11, 8) NULL DEFAULT NULL COMMENT 'Koordinat longitude dari maps' AFTER `latitude`,
        ADD COLUMN `maps_url` TEXT NULL DEFAULT NULL COMMENT 'URL Google Maps untuk lokasi' AFTER `longitude`;
        
        CREATE INDEX `idx_coordinates` ON `user_alamat` (`latitude`, `longitude`);
        ```
    *   (Opsional) Periksa file koneksi database dan sesuaikan *username* dan *password* jika konfigurasi lokal Anda berbeda.

4.  **Menjalankan Aplikasi**
    *   Nyalakan modul **Apache** dan **MySQL** di XAMPP Control Panel.
    *   Buka browser dan akses alamat: `http://localhost/ibox`.

## 📁 Struktur Folder Proyek
*   `/admin` - Skrip panel admin backend (manajemen produk, pesanan).
    *   `/products-panel` - Logika spesifik per kategori produk (edit-iphone.php, add-mac.php, dll).
*   `/assets` - Aset statis (gambar produk, ikon, font).
    *   `/img` - Gambar antarmuka umum.
    *   `/uploads` - Direktori penyimpanan gambar produk yang diupload admin.
*   `/db` - File koneksi database dan dump SQL.
*   `/pages` - Halaman-halaman utama aplikasi.
    *   `/checkout` - Logika halaman pembayaran dan keranjang.
    *   `/products` - Halaman listing dan detail produk.
*   `/auth` - Halaman Login, Register, dan manajemen sesi pengguna.

## 🗺️ Fitur Maps - Pemilihan Lokasi Pengiriman

### Cara Menggunakan:
1. Buka halaman checkout
2. Klik "Tambah Alamat" atau "Edit Alamat"
3. Klik tombol **"Pilih Lokasi di Peta"**
4. Modal peta interaktif akan terbuka dengan fitur:
   - 🔍 **Search Box**: Cari lokasi dengan mengetik alamat
   - 📍 **Click to Select**: Klik pada peta untuk memilih lokasi
   - 🎯 **Auto-detect**: Deteksi otomatis lokasi Anda (perlu izin browser)
   - ↔️ **Draggable Marker**: Drag marker untuk menyesuaikan posisi
5. Klik **"Konfirmasi Lokasi"** untuk menyimpan
6. Koordinat akan tersimpan dan ditampilkan di form

### Teknologi yang Digunakan:
- **Leaflet.js** - Library peta interaktif open-source
- **OpenStreetMap** - Tile provider untuk tampilan peta
- **Nominatim API** - Geocoding service untuk pencarian lokasi
- **Geolocation API** - Auto-detect lokasi pengguna

### Data yang Tersimpan:
```sql
latitude:  -6.208800  (8 digit desimal)
longitude: 106.845600 (8 digit desimal)
maps_url:  https://www.google.com/maps?q=-6.208800,106.845600
```

## 📝 Pembaruan Terkini (Changelog)

### Version 2.1 (Januari 2026) - Glassmorphism & UI Consistency
*   **🎨 Glassmorphism UI Overhaul**:
    *   Penerapan desain *Glassmorphism* (efek kaca cair) yang konsisten dan premium pada seluruh Navigasi Bar (Navbar), Dropdown Menu, dan Sidebar.
    *   Update mencakup seluruh halaman utama: `index.php`, `products.php`, `cart.php`, `checkout.php`, `profile.php`, `produk-terbaru.php`, dan `produk-populer.php`.
    *   Styling dropdown dengan backdrop-filter blur, border gradient halus, dan shadow multi-layer.
*   **🛒 Cart & Checkout Improvements**:
    *   Perbaikan fungsionalitas "Hapus Item" pada keranjang belanja.
    *   Sinkronisasi tampilan dropdown keranjang dengan desain glassmorphism.
    *   Penyelesaian masalah "Network Error" pada halaman checkout.
*   **📱 Sidebar & Responsiveness**:
    *   Perbaikan krusial pada **Hamburger Menu** dan **Sidebar Mobile**.
    *   Penyelesaian konflik `z-index` yang menyebabkan sidebar tidak bisa dibuka/tutup.
    *   Transisi dan animasi sidebar yang lebih halus (iOS style).
*   **👤 User Profile**:
    *   Redesign halaman Profil Pengguna (`profile.php`) agar selaras dengan tema baru.
    *   Perbaikan link navigasi user pada navbar.

### Version 2.0 (Januari 2026)
*   **🗺️ Fitur Maps Interaktif**:
    *   Integrasi Leaflet.js untuk pemilihan lokasi pengiriman via peta.
    *   Pencarian lokasi dengan Nominatim API.
    *   Auto-detect lokasi pengguna dengan Geolocation API.
    *   Penyimpanan koordinat latitude/longitude dan Google Maps URL.
    *   Draggable marker untuk penyesuaian posisi yang presisi.
    *   Modal peta dengan desain modern dan responsif.
*   **Database Schema Update**:
    *   Penambahan kolom `latitude`, `longitude`, dan `maps_url` di tabel `user_alamat`.
    *   Index untuk performa query berdasarkan koordinat.

### Version 1.0
*   **Fitur Checkout**: Penambahan logika filter grid foto berdasarkan warna yang dipilih. Jika pengguna memilih warna "Midnight", hanya foto varian Midnight yang tampil di grid.
*   **UI/UX**: Penerapan gaya *Glassmorphism* pada modal preview gambar untuk estetika yang lebih modern.
*   **Admin Panel**: Perbaikan bug pada fitur "Edit iPhone" di mana loading tidak berhenti, serta penambahan sinkronisasi dua arah antara input teks Hex dan input warna visual.

## 🌐 Browser Compatibility

### Fitur Maps Support:
- ✅ Chrome 90+
- ✅ Firefox 88+
- ✅ Safari 14+
- ✅ Edge 90+
- ✅ Opera 76+

**Catatan**: Fitur Geolocation memerlukan HTTPS atau localhost untuk berfungsi.

## 🔒 Security Features

*   **Input Validation**: Validasi koordinat di server side
*   **XSS Prevention**: Semua output di-escape dengan `htmlspecialchars()`
*   **SQL Injection Protection**: Penggunaan prepared statements
*   **HTTPS Ready**: Geolocation API hanya bekerja di HTTPS

## 🚀 Future Enhancements

### Planned Features:
- [ ] Reverse geocoding (koordinat → alamat otomatis)
- [ ] Auto-fill form fields dari hasil geocoding
- [ ] Distance calculation untuk estimasi ongkir
- [ ] Multiple delivery points
- [ ] Integration dengan Google Maps API (jika ada budget)
- [ ] Progressive Web App (PWA) support

---

*Dikembangkan sebagai proyek portofolio pengembangan web Full Stack.*

**Tech Stack**: PHP • MySQL • JavaScript • Leaflet.js • OpenStreetMap
