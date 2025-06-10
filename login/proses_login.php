<?php
session_start();
// Pastikan path ke file koneksi sudah benar
include("../koneksi/koneksi.php");

// 1. Validasi dasar: Cek apakah data POST ada dan tidak kosong
if (!isset($_POST['email'], $_POST['password']) || empty($_POST['email']) || empty($_POST['password'])) {
    // Jika data tidak lengkap, kembalikan ke login dengan pesan error
    $_SESSION['login_error'] = "Email dan Password wajib diisi!";
    header("Location: ../login/login.php");
    exit();
}

// 2. Ambil data dari form dan bersihkan (opsional tapi disarankan)
$email = mysqli_real_escape_string($db, $_POST['email']);
$password = $_POST['password'];

// 3. Siapkan query untuk mencari user berdasarkan email
// Menggunakan prepared statement untuk keamanan dari SQL Injection
$sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
$stmt = mysqli_prepare($db, $sql);

// Periksa apakah statement berhasil disiapkan
if ($stmt) {
    // Bind parameter email ke statement
    mysqli_stmt_bind_param($stmt, "s", $email);
    
    // Eksekusi statement
    mysqli_stmt_execute($stmt);
    
    // Ambil hasil query
    $result = mysqli_stmt_get_result($stmt);
    
    // 4. Cek apakah user dengan email tersebut ditemukan
    if ($user = mysqli_fetch_assoc($result)) {
        // User ditemukan, sekarang verifikasi password
        // password_verify() akan membandingkan password input dengan hash di database
        if (password_verify($password, $user['password'])) {
            // Password BENAR!
            
            // 5. Cek peran (role) user dan buat session sesuai perannya
            if ($user['role'] === 'admin') {
                // Jika user adalah admin
                $_SESSION['is_admin'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_email'] = $user['email'];
                header("Location: ../admin/admin_dashboard.php");
                exit();
            } else {
                // Jika user adalah user biasa
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nama'] = $user['nama_lengkap'];
                header("Location: ../user/menu_user.php"); // Ganti jika nama file menu user berbeda
                exit();
            }
        }
    }
}

// 6. Jika sampai di sini, artinya login gagal (email tidak ditemukan atau password salah)
$_SESSION['login_error'] = "Email atau Password salah!";
header("Location: ../login/login.php");
exit();

?>