<?php
session_start();
include("../koneksi/koneksi.php");

// Proteksi Halaman
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../login/login.php");
    exit();
}

// Cek apakah ID ada di URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query untuk menghapus produk
    $query = "DELETE FROM produk WHERE id = ?";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
}

// Redirect kembali ke dashboard admin setelah proses selesai
header("Location: ../admin/admin_dashboard.php");
exit();
?>