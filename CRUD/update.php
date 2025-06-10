<?php
session_start();
include("../koneksi/koneksi.php");

// Proteksi Halaman
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../login/login.php");
    exit();
}

// Ambil ID dari URL
if (!isset($_GET['id'])) {
    header("Location: ../admin/admin_dashboard.php");
    exit();
}
$id = $_GET['id'];

// Logika untuk UPDATE data saat form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_produk = $_POST['nama_produk'];
    $jenis = $_POST['jenis'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];

    // Query untuk update data
    $query = "UPDATE produk SET nama_produk=?, jenis=?, harga=?, deskripsi=? WHERE id=?";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "ssisi", $nama_produk, $jenis, $harga, $deskripsi, $id);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../admin/admin_dashboard.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($db);
    }
}

// Ambil data produk yang akan diedit untuk ditampilkan di form
$query_select = "SELECT * FROM produk WHERE id = ?";
$stmt_select = mysqli_prepare($db, $query_select);
mysqli_stmt_bind_param($stmt_select, "i", $id);
mysqli_stmt_execute($stmt_select);
$result = mysqli_stmt_get_result($stmt_select);
$produk = mysqli_fetch_assoc($result);

// Jika produk dengan ID tsb tidak ditemukan, kembalikan ke dashboard
if (!$produk) {
    header("Location: ../admin/admin_dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Produk</title>
    <link rel="stylesheet" href="path/to/your/styles.css"> <style>
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
        .btn-save { background-color: #ffc107; color: #333; flex-grow: 1; }
        .btn-cancel { background-color: #888; flex-grow: 1; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Produk</h1>
        <form action="../CRUD/update.php?id=<?php echo $produk['id']; ?>" method="POST">
            <div class="form-group">
                <label for="nama_produk">Nama Produk</label>
                <input type="text" id="nama_produk" name="nama_produk" value="<?php echo htmlspecialchars($produk['nama_produk']); ?>" required>
            </div>
            <div class="form-group">
                <label for="jenis">Jenis Produk</label>
                <select id="jenis" name="jenis" required>
                    <option value="Makanan" <?php if($produk['jenis'] == 'Makanan') echo 'selected'; ?>>Makanan</option>
                    <option value="Minuman" <?php if($produk['jenis'] == 'Minuman') echo 'selected'; ?>>Minuman</option>
                </select>
            </div>
            <div class="form-group">
                <label for="harga">Harga (Rp)</label>
                <input type="number" id="harga" name="harga" value="<?php echo $produk['harga']; ?>" required>
            </div>
            <div class="form-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi"><?php echo htmlspecialchars($produk['deskripsi']); ?></textarea>
            </div>
            <div class="btn-group">
                <button type="submit" class="btn btn-save">Update</button>
                <a href="../admin/admin_dashboard.php" class="btn btn-cancel">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>