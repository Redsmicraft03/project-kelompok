<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang di Kio-Food</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f2f5;
        }
        .welcome-container {
            text-align: center;
            background-color: #ffffff;
            padding: 50px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333333;
            margin-bottom: 30px;
        }
        .btn {
            background-color: #4CAF50;
            color: white;
            padding: 15px 25px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 10px 5px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .btn-login {
            background-color: #008CBA;
        }
        .btn-register {
            background-color: #f44336;
        }
        .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>

    <div class="welcome-container">
        <h1>Selamat datang di Kio-Food</h1>
        <div>
            <a href="login.php" class="btn btn-login">Login</a>
            <a href="register.php" class="btn btn-register">Register</a>
        </div>
    </div>

</body>
</html>