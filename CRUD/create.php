<?php
session_start();
include("../koneksi/koneksi.php");

// Proteksi Halaman: Hanya untuk admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../login/login.php");
    exit();
}

// Logika untuk memproses form saat disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_produk = $_POST['nama_produk'];
    $jenis = $_POST['jenis'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];

    // Query untuk memasukkan data baru menggunakan prepared statements (lebih aman)
    $query = "INSERT INTO produk (nama_produk, jenis, harga, deskripsi) VALUES (?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "ssis", $nama_produk, $jenis, $harga, $deskripsi);
    
    if (mysqli_stmt_execute($stmt)) {
        // Jika berhasil, redirect kembali ke dashboard admin
        header("Location: ../admin/admin_dashboard.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($db);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Produk Baru</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #333; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box;
        }
        .form-group textarea { resize: vertical; min-height: 100px; }
        .btn-group { display: flex; gap: 10px; }
        .btn { padding: 10px 15px; text-decoration: none; color: white; border-radius: 5px; border: none; cursor: pointer; text-align: center; }
        .btn-save { background-color: #4CAF50; flex-grow: 1; }
        .btn-cancel { background-color: #888; flex-grow: 1; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Tambah Produk Baru</h1>
        <form action="tambah_produk.php" method="POST">
            <div class="form-group">
                <label for="nama_produk">Nama Produk</label>
                <input type="text" id="nama_produk" name="nama_produk" required>
            </div>
            <div class="form-group">
                <label for="jenis">Jenis Produk</label>
                <select id="jenis" name="jenis" required>
                    <option value="Makanan">Makanan</option>
                    <option value="Minuman">Minuman</option>
                </select>
            </div>
            <div class="form-group">
                <label for="harga">Harga (Rp)</label>
                <input type="number" id="harga" name="harga" required>
            </div>
            <div class="form-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi"></textarea>
            </div>
            <div class="btn-group">
                <button type="submit" class="btn btn-save">Simpan</button>
                <a href="../admin/admin_dashboard.php" class="btn btn-cancel">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>