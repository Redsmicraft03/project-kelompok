<?php
// WAJIB: Mulai session di baris paling atas
session_start();

// Sertakan file koneksi database
include("../koneksi/koneksi.php");

// Variabel untuk menyimpan pesan error
$error_message = "";

// Cek jika form telah disubmit (method == POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil data dari form
    $nama_lengkap = mysqli_real_escape_string($db, $_POST['nama_lengkap']);
    $jenis_kelamin = mysqli_real_escape_string($db, $_POST['jenis_kelamin']);
    $usia = mysqli_real_escape_string($db, $_POST['usia']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password

    // Siapkan query SQL untuk memasukkan data
    $sql = "INSERT INTO users (nama_lengkap, jenis_kelamin, usia, email, password) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "ssiss", $nama_lengkap, $jenis_kelamin, $usia, $email, $password);

    // Eksekusi statement
    if (mysqli_stmt_execute($stmt)) {
        // JIKA BERHASIL:
        // 1. Simpan pesan sukses ke dalam session
        $_SESSION['success_message'] = "Pendaftaran berhasil! Silakan login.";
        
        // 2. Alihkan (redirect) pengguna ke halaman login.php
        header("Location: login.php");
        
        // 3. Hentikan eksekusi script setelah redirect
        exit();

    } else {
        // JIKA GAGAL:
        if(mysqli_errno($db) == 1062) { // 1062 adalah kode error untuk duplicate entry
            $error_message = "Gagal mendaftar. Email ini sudah digunakan!";
        } else {
            $error_message = "Gagal mendaftar. Terjadi kesalahan pada database.";
        }
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Kio-Food</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f0f2f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 20px 0; }
        .register-container { background-color: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h2 { text-align: center; color: #333; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; color: #555; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        .form-group .radio-group { display: flex; gap: 20px; align-items: center; padding-top: 5px;}
        .btn { background-color: #f44336; color: white; padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; width: 100%; font-size: 16px; transition: background-color 0.3s; }
        .btn:hover { opacity: 0.9; }
        .message.error { text-align: center; padding: 10px; border-radius: 5px; margin-bottom: 15px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;}
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Buat Akun Kio-Food</h2>

        <?php if (!empty($error_message)): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" required>
            </div>
            <div class="form-group">
                <label>Jenis Kelamin</label>
                <div class="radio-group">
                    <label><input type="radio" name="jenis_kelamin" value="Laki-laki" required> Laki-laki</label>
                    <label><input type="radio" name="jenis_kelamin" value="Perempuan" required> Perempuan</label>
                </div>
            </div>
            <div class="form-group">
                <label for="usia">Usia</label>
                <input type="number" id="usia" name="usia" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Daftar</button>
        </form>
    </div>
</body>
</html>