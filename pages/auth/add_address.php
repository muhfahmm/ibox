<?php
session_start();
require '../../db/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Default user data for form
$query = "SELECT firstname, lastname, no_hp, email FROM user_autentikasi WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Alamat - iBox Indonesia</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background-color: #f5f5f7;
            color: #1d1d1f;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .auth-card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .form-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 25px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
            color: #1d1d1f;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #d2d2d7;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.2s;
            outline: none;
        }

        .form-input:focus {
            border-color: #007aff;
            box-shadow: 0 0 0 4px rgba(0, 122, 255, 0.1);
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background-color: #007aff;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background-color: #0071e3;
        }
        
        .btn-cancel {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #86868b;
            text-decoration: none;
            font-weight: 500;
        }
        
        .btn-cancel:hover {
            color: #1d1d1f;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="auth-card">
            <h1 class="form-title">Tambah Alamat Baru</h1>
            
            <form action="api/proses-tambah-alamat.php" method="POST">
                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                
                <div class="form-group">
                    <label class="form-label">Label Alamat</label>
                    <input type="text" name="label_alamat" class="form-input" placeholder="Contoh: Rumah, Kantor">
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="form-label">Nama Penerima</label>
                        <input type="text" name="username" class="form-input" value="<?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?>" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label">Nomor HP</label>
                        <input type="tel" name="no_hp" class="form-input" value="<?php echo htmlspecialchars($user['no_hp']); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat Lengkap (Jalan, No. Rumah, RT/RW)</label>
                    <textarea name="alamat_lengkap" class="form-input" rows="3" required></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="form-label">Kota</label>
                        <input type="text" name="kota" class="form-input" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label">Kecamatan</label>
                        <input type="text" name="kecamatan" class="form-input" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="form-label">Provinsi</label>
                        <input type="text" name="provinsi" class="form-input" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label">Kode Pos</label>
                        <input type="text" name="kode_post" class="form-input" required>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Simpan Alamat</button>
                <a href="profile.php" class="btn-cancel">Batal</a>
            </form>
        </div>
    </div>

</body>
</html>
