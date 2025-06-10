<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    header("Location: ../login/login.php");
    exit();
}
$order_id = htmlspecialchars($_GET['order_id']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pesanan Berhasil</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f7fc; display: flex; justify-content: center; align-items: center; height: 100vh; text-align: center; }
        .container { background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .icon { font-size: 5em; color: #28a745; }
        h1 { color: #333; }
        p { color: #555; font-size: 1.1em; }
        .btn { display: inline-block; margin-top: 20px; padding: 12px 25px; background-color: #007bff; color: white; text-decoration: none; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">✓</div>
        <h1>Pesanan Anda Berhasil Dibuat!</h1>
        <p>Terima kasih telah berbelanja. Nomor pesanan Anda adalah <strong>#<?= $order_id ?></strong>.</p>
        <p>Kami akan segera memproses pesanan Anda.</p>
        <a href="menu_user.php" class="btn">Kembali ke Menu Utama</a>
    </div>
</body>
</html>