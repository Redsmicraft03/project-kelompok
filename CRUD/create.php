<?php
session_start();
include("../koneksi/koneksi.php");

// Proteksi Halaman: Hanya untuk admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../login/login.php");
    exit();
}

$error_message = '';
$success_message = '';

// Logika untuk memproses form saat disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_produk = trim($_POST['nama_produk']);
    $jenis = $_POST['jenis'];
    $harga = $_POST['harga'];
    $deskripsi = trim($_POST['deskripsi']);
    $gambar_path = null;

    // Validasi input
    if (empty($nama_produk) || empty($jenis) || empty($harga)) {
        $error_message = "Nama produk, jenis, dan harga harus diisi!";
    } else {
        // Proses upload gambar
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $upload_dir = "../upload_gambar/";
            
            // Buat folder jika belum ada
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (in_array($file_extension, $allowed_extensions)) {
                $max_size = 5 * 1024 * 1024; // 5MB
                if ($_FILES['gambar']['size'] <= $max_size) {
                    // Generate nama file unik
                    $file_name = uniqid() . '_' . time() . '.' . $file_extension;
                    $target_path = $upload_dir . $file_name;
                    
                    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_path)) {
                        $gambar_path = "upload_gambar/" . $file_name;
                    } else {
                        $error_message = "Gagal mengupload gambar.";
                    }
                } else {
                    $error_message = "Ukuran gambar terlalu besar. Maksimal 5MB.";
                }
            } else {
                $error_message = "Format gambar tidak didukung. Gunakan JPG, JPEG, PNG, GIF, atau WebP.";
            }
        }
        
        // Jika tidak ada error, simpan ke database
        if (empty($error_message)) {
            // Query untuk memasukkan data baru menggunakan prepared statements
            $query = "INSERT INTO produk (nama_produk, jenis, harga, deskripsi, gambar) VALUES (?, ?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($db, $query);
            mysqli_stmt_bind_param($stmt, "ssiss", $nama_produk, $jenis, $harga, $deskripsi, $gambar_path);
            
            if (mysqli_stmt_execute($stmt)) {
                $success_message = "Produk berhasil ditambahkan!";
                // Reset form
                $_POST = array();
            } else {
                $error_message = "Error: " . mysqli_error($db);
                // Hapus file yang sudah diupload jika gagal simpan ke database
                if ($gambar_path && file_exists("../" . $gambar_path)) {
                    unlink("../" . $gambar_path);
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk Baru - Kio-Food</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #FF6B35;
            --secondary-color: #F7931E;
            --dark-color: #2c3e50;
            --light-bg: #f8f9fa;
            --white: #ffffff;
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-300: #dee2e6;
            --gray-500: #6c757d;
            --gray-700: #495057;
            --gray-800: #343a40;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 8px 25px rgba(0, 0, 0, 0.15);
            --border-radius: 12px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--light-bg) 0%, #e3f2fd 100%);
            color: var(--gray-800);
            line-height: 1.6;
            min-height: 100vh;
            padding: 20px 0;
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .card-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" patternUnits="userSpaceOnUse" width="100" height="100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="80" r="1.5" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="70" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        }

        .card-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            position: relative;
            z-index: 1;
        }

        .card-header p {
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .card-body {
            padding: 40px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
            animation: fadeIn 0.3s ease-out;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--gray-700);
            font-size: 14px;
        }

        .form-group .required {
            color: var(--danger);
        }

        .form-control {
            width: 100%;
            padding: 15px;
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: var(--white);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
        }

        .form-control:hover {
            border-color: var(--gray-300);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
            font-family: inherit;
        }

        .file-upload-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .file-upload {
            display: none;
        }

        .file-upload-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 15px;
            border: 2px dashed var(--gray-300);
            border-radius: 8px;
            background: var(--gray-100);
            color: var(--gray-600);
            cursor: pointer;
            transition: all 0.3s ease;
            min-height: 120px;
            flex-direction: column;
        }

        .file-upload-btn:hover {
            border-color: var(--primary-color);
            background: rgba(255, 107, 53, 0.05);
            color: var(--primary-color);
        }

        .file-upload-btn i {
            font-size: 32px;
            margin-bottom: 8px;
        }

        .file-info {
            margin-top: 10px;
            padding: 10px;
            background: var(--gray-100);
            border-radius: 6px;
            font-size: 14px;
            color: var(--gray-600);
            display: none;
        }

        .file-info.show {
            display: block;
            animation: fadeIn 0.3s ease-out;
        }

        .file-info i {
            color: var(--success);
            margin-right: 8px;
        }

        .image-preview {
            margin-top: 15px;
            text-align: center;
            display: none;
        }

        .image-preview.show {
            display: block;
            animation: fadeIn 0.3s ease-out;
        }

        .image-preview img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            box-shadow: var(--shadow);
        }

        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            padding: 15px 25px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
            flex: 1;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }

        .btn-secondary {
            background: var(--gray-500);
            color: white;
        }

        .btn-secondary:hover {
            background: var(--gray-700);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .loading {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .form-text {
            font-size: 12px;
            color: var(--gray-500);
            margin-top: 5px;
        }

        .breadcrumb {
            margin-bottom: 20px;
            padding: 15px 0;
        }

        .breadcrumb a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .breadcrumb span {
            color: var(--gray-500);
            margin: 0 8px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 15px;
            }

            .card-body {
                padding: 25px 20px;
            }

            .card-header {
                padding: 25px 20px;
            }

            .card-header h1 {
                font-size: 24px;
            }

            .btn-group {
                flex-direction: column;
            }

            .form-control {
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="breadcrumb">
            <a href="../admin/admin_dashboard.php">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <span>/</span>
            <span>Tambah Produk</span>
        </div>

        <div class="card">
            <div class="card-header">
                <h1>
                    <i class="fas fa-plus-circle"></i>
                    Tambah Produk Baru
                </h1>
                <p>Tambahkan produk cireng baru ke dalam menu</p>
            </div>

            <div class="card-body">
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?php echo htmlspecialchars($success_message); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <form action="create.php" method="POST" enctype="multipart/form-data" id="productForm">
                    <div class="form-group">
                        <label for="nama_produk">
                            <i class="fas fa-tag"></i>
                            Nama Produk <span class="required">*</span>
                        </label>
                        <input type="text" 
                               id="nama_produk" 
                               name="nama_produk" 
                               class="form-control" 
                               placeholder="Masukkan nama produk"
                               value="<?php echo isset($_POST['nama_produk']) ? htmlspecialchars($_POST['nama_produk']) : ''; ?>"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="jenis">
                            <i class="fas fa-layer-group"></i>
                            Jenis Produk <span class="required">*</span>
                        </label>
                        <select id="jenis" name="jenis" class="form-control" required>
                            <option value="">Pilih jenis produk</option>
                            <option value="Makanan" <?php echo (isset($_POST['jenis']) && $_POST['jenis'] == 'Makanan') ? 'selected' : ''; ?>>
                                Makanan
                            </option>
                            <option value="Minuman" <?php echo (isset($_POST['jenis']) && $_POST['jenis'] == 'Minuman') ? 'selected' : ''; ?>>
                                Minuman
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="harga">
                            <i class="fas fa-dollar-sign"></i>
                            Harga <span class="required">*</span>
                        </label>
                        <input type="number" 
                               id="harga" 
                               name="harga" 
                               class="form-control" 
                               placeholder="0"
                               min="0"
                               step="500"
                               value="<?php echo isset($_POST['harga']) ? htmlspecialchars($_POST['harga']) : ''; ?>"
                               required>
                        <div class="form-text">Masukkan harga dalam Rupiah</div>
                    </div>

                    <div class="form-group">
                        <label for="gambar">
                            <i class="fas fa-image"></i>
                            Gambar Produk
                        </label>
                        <div class="file-upload-wrapper">
                            <input type="file" 
                                   id="gambar" 
                                   name="gambar" 
                                   class="file-upload" 
                                   accept="image/*">
                            <label for="gambar" class="file-upload-btn">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>Klik untuk upload gambar</span>
                                <small>atau drag & drop file di sini</small>
                            </label>
                        </div>
                        <div class="file-info" id="fileInfo">
                            <i class="fas fa-file-image"></i>
                            <span id="fileName"></span>
                        </div>
                        <div class="image-preview" id="imagePreview">
                            <img id="previewImg" src="" alt="Preview">
                        </div>
                        <div class="form-text">Format yang didukung: JPG, JPEG, PNG, GIF, WebP (Maksimal 5MB)</div>
                    </div>

                    <div class="form-group">
                        <label for="deskripsi">
                            <i class="fas fa-align-left"></i>
                            Deskripsi
                        </label>
                        <textarea id="deskripsi" 
                                  name="deskripsi" 
                                  class="form-control" 
                                  placeholder="Masukkan deskripsi produk (opsional)"><?php echo isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : ''; ?></textarea>
                        <div class="form-text">Jelaskan detail produk, bahan, atau keistimewaan lainnya</div>
                    </div>

                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-save"></i>
                            Simpan Produk
                        </button>
                        <a href="../admin/admin_dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('gambar');
            const fileInfo = document.getElementById('fileInfo');
            const fileName = document.getElementById('fileName');
            const imagePreview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            const form = document.getElementById('productForm');
            const submitBtn = document.getElementById('submitBtn');

            // File upload handling
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                
                if (file) {
                    // Show file info
                    fileName.textContent = file.name;
                    fileInfo.classList.add('show');
                    
                    // Show image preview
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        imagePreview.classList.add('show');
                    };
                    reader.readAsDataURL(file);
                    
                    // Validate file
                    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                    const maxSize = 5 * 1024 * 1024; // 5MB
                    
                    if (!allowedTypes.includes(file.type)) {
                        alert('Format gambar tidak didukung. Gunakan JPG, JPEG, PNG, GIF, atau WebP.');
                        fileInput.value = '';
                        fileInfo.classList.remove('show');
                        imagePreview.classList.remove('show');
                        return;
                    }
                    
                    if (file.size > maxSize) {
                        alert('Ukuran gambar terlalu besar. Maksimal 5MB.');
                        fileInput.value = '';
                        fileInfo.classList.remove('show');
                        imagePreview.classList.remove('show');
                        return;
                    }
                } else {
                    fileInfo.classList.remove('show');
                    imagePreview.classList.remove('show');
                }
            });

            // Form submission handling
            form.addEventListener('submit', function(e) {
                const originalContent = submitBtn.innerHTML;
                submitBtn.innerHTML = '<div class="loading"></div> Menyimpan...';
                submitBtn.disabled = true;
                
                // Re-enable if form doesn't submit for some reason
                setTimeout(() => {
                    submitBtn.innerHTML = originalContent;
                    submitBtn.disabled = false;
                }, 10000);
            });

            // Format harga input
            const hargaInput = document.getElementById('harga');
            hargaInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/[^0-9]/g, '');
                e.target.value = value;
            });

            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        alert.remove();
                    }, 300);
                }, 5000);
            });
        });
    </script>
</body>
</html>