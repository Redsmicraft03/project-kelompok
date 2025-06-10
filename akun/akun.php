<?php
session_start();
include '../koneksi/koneksi.php'; // Sesuaikan path koneksi Anda

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data pengguna saat ini dari database
// Kita tidak mengambil password untuk alasan keamanan
$query = "SELECT nama_lengkap, email, usia, jenis_kelamin FROM users WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();

if (!$user_data) {
    // Jika data tidak ditemukan (kasus langka), logout saja
    session_destroy();
    header("Location: ../login/login.php");
    exit();
}

// Cek jika ada pesan status dari halaman update
$status_message = '';
if (isset($_SESSION['update_status'])) {
    if ($_SESSION['update_status'] == 'sukses') {
        $status_message = '<div class="alert alert-success">Profil berhasil diperbarui!</div>';
    } else {
        $status_message = '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['update_message']) . '</div>';
    }
    // Hapus session setelah ditampilkan
    unset($_SESSION['update_status']);
    unset($_SESSION['update_message']);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Akun Saya</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fc;
            color: #333;
            margin: 0;
            padding: 30px 0;
        }
        .container {
            max-width: 700px;
            margin: auto;
            background: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
        }
        .password-section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }
        .button-group {
            margin-top: 30px;
            display: flex;
            gap: 15px;
        }
        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
        }
        .btn-save {
            background-color: #007bff;
            color: white;
        }
        .btn-back {
            background-color: #6c757d;
            color: white;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Profil Akun Saya</h1>

    <?= $status_message ?>

    <form action="update_akun.php" method="POST">
        <div class="form-group">
            <label for="nama">Nama Lengkap</label>
            <input type="text" id="nama" name="nama" class="form-control" value="<?= htmlspecialchars($user_data['nama_lengkap']) ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($user_data['email']) ?>" required>
        </div>
        <div class="form-group">
            <label for="usia">Usia</label>
            <input type="number" id="usia" name="usia" class="form-control" value="<?= htmlspecialchars($user_data['usia']) ?>">
        </div>
        <div class="form-group">
            <label for="jenis_kelamin">Jenis Kelamin</label>
            <input type="text" id="jenis_kelamin" name="jenis_kelamin" class="form-control" value="<?= htmlspecialchars($user_data['jenis_kelamin']) ?>">
        </div>

        <div class="password-section">
            <h2>Ubah Password (Opsional)</h2>
            <div class="form-group">
                <label for="password_baru">Password Baru</label>
                <input type="password" id="password_baru" name="password_baru" class="form-control" placeholder="Kosongkan jika tidak ingin diubah">
            </div>
            <div class="form-group">
                <label for="konfirmasi_password">Konfirmasi Password Baru</label>
                <input type="password" id="konfirmasi_password" name="konfirmasi_password" class="form-control">
            </div>
        </div>

        <div class="button-group">
            <a href="../user/menu_user.php" class="btn btn-back">Kembali ke Menu</a>
            <button type="submit" class="btn btn-save">Simpan Perubahan</button>
        </div>
    </form>
</div>

</body>
</html>