<?php
// WAJIB: Mulai session di baris paling atas
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kio-Food</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        
        /* === STYLE UNTUK TOMBOL KEMBALI (BARU) === */
        .back-button {
            position: absolute;
            top: 25px;
            left: 25px;
            text-decoration: none;
            color: #333;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 10px 18px;
            border-radius: 25px;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: all 0.2s ease-in-out;
        }
        .back-button:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        /* ========================================= */

        .login-container { background-color: #fff; padding: 40px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 100%; max-width: 400px; position: relative; }
        h2 { text-align: center; color: #333; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; color: #555; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        .btn { background-color: #008CBA; color: white; padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; width: 100%; font-size: 16px; }
        .btn:hover { opacity: 0.9; }
        .message { text-align: center; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .text-center { text-align: center; margin-top: 20px; }
        .text-center a { color: #008CBA; text-decoration: none; }
    </style>
</head>
<body>

    <a href="../main/main.php" class="back-button">&larr; Kembali</a>

    <div class="login-container">
        <h2>Login CIANJURRR</h2>

        <?php
        // Menampilkan pesan error jika ada
        if (isset($_SESSION['login_error'])) {
            echo '<div class="message error">' . $_SESSION['login_error'] . '</div>';
            unset($_SESSION['login_error']);
        }

        // Menampilkan pesan sukses dari registrasi jika ada
        if (isset($_SESSION['success_message'])) {
            echo '<div class="message success">' . $_SESSION['success_message'] . '</div>';
            unset($_SESSION['success_message']);
        }
        ?>

        <form action="proses_login.php" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
        <div class="text-center">
            <p>Belum punya akun? <a href="../login/register.php">Daftar di sini</a></p>
        </div>
    </div>

</body>
</html>