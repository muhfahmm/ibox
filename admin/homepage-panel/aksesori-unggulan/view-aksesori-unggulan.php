<?php
session_start();
require_once '../../db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../../auth/login.php?error=not_logged_in');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: aksesori-unggulan.php');
    exit();
}

$id = $_GET['id'];

// Ambil data detail dengan join
$query = "SELECT 
            ha.*,
            CASE 
                WHEN ha.tipe_produk = 'iphone' THEN iphone.nama_produk
                WHEN ha.tipe_produk = 'ipad' THEN ipad.nama_produk
                WHEN ha.tipe_produk = 'mac' THEN mac.nama_produk
                WHEN ha.tipe_produk = 'music' THEN music.nama_produk
                WHEN ha.tipe_produk = 'watch' THEN watch.nama_produk
                WHEN ha.tipe_produk = 'aksesoris' THEN aksesoris.nama_produk
                WHEN ha.tipe_produk = 'airtag' THEN airtag.nama_produk
            END as nama_produk,
            CASE 
                WHEN ha.tipe_produk = 'iphone' THEN iphone_gambar.foto_thumbnail
                WHEN ha.tipe_produk = 'ipad' THEN ipad_gambar.foto_thumbnail
                WHEN ha.tipe_produk = 'mac' THEN mac_gambar.foto_thumbnail
                WHEN ha.tipe_produk = 'music' THEN music_gambar.foto_thumbnail
                WHEN ha.tipe_produk = 'watch' THEN watch_gambar.foto_thumbnail
                WHEN ha.tipe_produk = 'aksesoris' THEN aksesoris_gambar.foto_thumbnail
                WHEN ha.tipe_produk = 'airtag' THEN airtag_gambar.foto_thumbnail
            END as foto_thumbnail,
            CASE 
                WHEN ha.tipe_produk = 'iphone' THEN iphone.deskripsi_produk
                WHEN ha.tipe_produk = 'ipad' THEN ipad.deskripsi_produk
                WHEN ha.tipe_produk = 'mac' THEN mac.deskripsi_produk
                WHEN ha.tipe_produk = 'music' THEN music.deskripsi_produk
                WHEN ha.tipe_produk = 'watch' THEN watch.deskripsi_produk
                WHEN ha.tipe_produk = 'aksesoris' THEN aksesoris.deskripsi_produk
                WHEN ha.tipe_produk = 'airtag' THEN airtag.deskripsi_produk
            END as deskripsi_produk
          FROM home_aksesori ha
          LEFT JOIN admin_produk_iphone iphone ON ha.tipe_produk = 'iphone' AND ha.produk_id = iphone.id
          LEFT JOIN admin_produk_ipad ipad ON ha.tipe_produk = 'ipad' AND ha.produk_id = ipad.id
          LEFT JOIN admin_produk_mac mac ON ha.tipe_produk = 'mac' AND ha.produk_id = mac.id
          LEFT JOIN admin_produk_music music ON ha.tipe_produk = 'music' AND ha.produk_id = music.id
          LEFT JOIN admin_produk_watch watch ON ha.tipe_produk = 'watch' AND ha.produk_id = watch.id
          LEFT JOIN admin_produk_aksesoris aksesoris ON ha.tipe_produk = 'aksesoris' AND ha.produk_id = aksesoris.id
          LEFT JOIN admin_produk_airtag airtag ON ha.tipe_produk = 'airtag' AND ha.produk_id = airtag.id
          LEFT JOIN admin_produk_iphone_gambar iphone_gambar ON ha.tipe_produk = 'iphone' AND ha.produk_id = iphone_gambar.produk_id
          LEFT JOIN admin_produk_ipad_gambar ipad_gambar ON ha.tipe_produk = 'ipad' AND ha.produk_id = ipad_gambar.produk_id
          LEFT JOIN admin_produk_mac_gambar mac_gambar ON ha.tipe_produk = 'mac' AND ha.produk_id = mac_gambar.produk_id
          LEFT JOIN admin_produk_music_gambar music_gambar ON ha.tipe_produk = 'music' AND ha.produk_id = music_gambar.produk_id
          LEFT JOIN admin_produk_watch_gambar watch_gambar ON ha.tipe_produk = 'watch' AND ha.produk_id = watch_gambar.produk_id
          LEFT JOIN admin_produk_aksesoris_gambar aksesoris_gambar ON ha.tipe_produk = 'aksesoris' AND ha.produk_id = aksesoris_gambar.produk_id
          LEFT JOIN admin_produk_airtag_gambar airtag_gambar ON ha.tipe_produk = 'airtag' AND ha.produk_id = airtag_gambar.produk_id
          WHERE ha.id = '$id'
          GROUP BY ha.id";
$result = mysqli_query($db, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    header('Location: aksesori-unggulan.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Aksesori Unggulan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .header h1 {
            font-size: 24px;
            color: #333;
        }
        .detail-grid {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 40px;
        }
        .product-image {
            width: 100%;
            border-radius: 15px;
            border: 1px solid #eee;
            padding: 10px;
        }
        .product-image img {
            width: 100%;
            height: auto;
            border-radius: 10px;
            object-fit: contain;
        }
        .info-group {
            margin-bottom: 25px;
        }
        .info-label {
            font-size: 13px;
            text-transform: uppercase;
            color: #999;
            font-weight: 600;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 18px;
            color: #333;
            font-weight: 500;
        }
        .badge {
            display: inline-block;
            background: #4a6cf7;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        .btn-group {
            margin-top: 30px;
            display: flex;
            gap: 15px;
        }
        .btn {
            padding: 12px 25px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #4a6cf7 0%, #6a11cb 100%);
            color: white;
        }
        .btn-secondary {
            background: #f0f0f0;
            color: #666;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .description-box {
            background: #f9f9fb;
            padding: 20px;
            border-radius: 12px;
            line-height: 1.6;
            color: #555;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-eye"></i> Detail Aksesori Unggulan</h1>
            <a href="aksesori-unggulan.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="detail-grid">
            <div class="product-image">
                <?php if(!empty($data['foto_thumbnail'])): ?>
                    <img src="../../uploads/<?php echo htmlspecialchars($data['foto_thumbnail']); ?>" alt="Thumbnail">
                <?php else: ?>
                    <div style="height: 300px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border-radius: 10px;">
                        <i class="fas fa-image" style="color: #ccc; font-size: 60px;"></i>
                    </div>
                <?php endif; ?>
            </div>

            <div class="product-info">
                <div class="info-group">
                    <div class="info-label">Tipe Produk</div>
                    <div class="info-value"><span class="badge"><?php echo strtoupper($data['tipe_produk']); ?></span></div>
                </div>

                <div class="info-group">
                    <div class="info-label">Nama Produk</div>
                    <div class="info-value"><?php echo htmlspecialchars($data['nama_produk'] ?? 'Produk Tidak Ditemukan'); ?></div>
                </div>

                <div class="info-group">
                    <div class="info-label">Label Tampil</div>
                    <div class="info-value"><?php echo htmlspecialchars($data['label']); ?></div>
                </div>

                <div class="info-group">
                    <div class="info-label">Urutan</div>
                    <div class="info-value"><strong><?php echo $data['urutan']; ?></strong></div>
                </div>

                <div class="info-group">
                    <div class="info-label">Deskripsi Produk</div>
                    <div class="description-box">
                        <?php echo nl2br(htmlspecialchars($data['deskripsi_produk'] ?? 'Tidak ada deskripsi')); ?>
                    </div>
                </div>

                <div class="btn-group">
                    <a href="edit-aksesori-unggulan.php?id=<?php echo $data['id']; ?>" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Aksesori
                    </a>
                    <a href="delete-aksesori-unggulan.php?id=<?php echo $data['id']; ?>" class="btn btn-secondary" 
                       style="color: #d32f2f;" onclick="return confirm('Yakin ingin menghapus ini?')">
                        <i class="fas fa-trash"></i> Hapus
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
