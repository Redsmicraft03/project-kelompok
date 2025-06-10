<?php
session_start();

// Asumsikan saat user biasa login, Anda menyimpan 'user_id' dan 'user_nama'
// Proteksi Halaman: Jika user belum login, alihkan ke halaman login.
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}

// Ambil nama user dari session untuk sapaan personal
$nama_user = isset($_SESSION['user_nama']) ? $_SESSION['user_nama'] : 'Pengguna';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Utama - Kio-Food</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            border-bottom: 1px solid #ddd;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
            font-size: 1.1em;
        }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            text-align: center;
        }
        .menu-item {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            background-color: #fafafa;
            border: 1px solid #eee;
            border-radius: 8px;
            text-decoration: none;
            color: #333;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .menu-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }
        .menu-item .icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        .menu-item .title {
            font-weight: bold;
            font-size: 18px;
        }
        .logout-link {
            display: block;
            text-align: center;
            margin-top: 40px;
            color: #f44336;
            text-decoration: none;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="header">
            <h1>Selamat Datang di Kio-Food</h1>
            <p>Halo, <?php echo htmlspecialchars($nama_user); ?>!</p>
        </div>

        <div class="menu-grid">
            <a href="daftar_produk.php" class="menu-item">
                <div class="icon">🍔</div>
                <div class="title">Menu Makanan</div>
            </a>

            <a href="keranjang.php" class="menu-item">
                <div class="icon">🛒</div>
                <div class="title">Keranjang Saya</div>
            </a>

            <a href="riwayat_pesanan.php" class="menu-item">
                <div class="icon">🧾</div>
                <div class="title">Riwayat Pesanan</div>
            </a>
            
            <a href="promo.php" class="menu-item">
                <div class="icon">🎟️</div>
                <div class="title">Promo & Voucher</div>
            </a>
            
            <a href="akun.php" class="menu-item">
                <div class="icon">👤</div>
                <div class="title">Akun Saya</div>
            </a>
        </div>
        
        <a href="../login/logout.php" class="logout-link">Logout</a>
    </div>

</body>
</html>