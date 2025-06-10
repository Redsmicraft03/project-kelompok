<?php
session_start();
// Proteksi halaman ini agar hanya bisa diakses oleh user yang sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Kontak</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #333;
        }
        .container {
            max-width: 500px;
            width: 100%;
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        .icon {
            font-size: 4.5em; /* Ukuran emoji sebagai ikon */
            margin-bottom: 20px;
            color: #007bff;
        }
        h1 {
            margin-top: 0;
            margin-bottom: 30px;
            color: #333;
        }
        .contact-info {
            text-align: left;
            margin-bottom: 30px;
        }
        /* Menggunakan Definition List <dl> untuk pasangan label dan data */
        .contact-info dt {
            font-weight: 600;
            color: #555;
            font-size: 0.9em;
            margin-top: 15px;
        }
        .contact-info dd {
            margin-left: 0;
            font-size: 1.2em;
            color: #333;
            padding-bottom: 15px;
            border-bottom: 1px solid #e9ecef;
        }
        .contact-info dd:last-of-type {
            border-bottom: none;
        }
        .btn-kembali {
            display: inline-block;
            background-color: #6c757d;
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.2s;
        }
        .btn-kembali:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="icon">👤</div>
        <h1>Informasi Kontak</h1>

        <dl class="contact-info">
            <dt>Nama Lengkap</dt>
            <dd>Hanif Wisanggeni P</dd>

            <dt>Nomor Telepon</dt>
            <dd>082114541807</dd>

            <dt>NIM</dt>
            <dd>202243501947</dd>
        </dl>

        <a href="../user/menu_user.php" class="btn-kembali">Kembali ke Menu Utama</a>
    </div>

</body>
</html>