<?php

$server = "localhost";
$user = "root"; // Sesuaikan jika username database Anda berbeda
$password = ""; // Sesuaikan jika password database Anda berbeda
$nama_database = "project_kelompok";

// Membuat koneksi
$db = mysqli_connect($server, $user, $password, $nama_database);

// Mengecek koneksi
if (!$db) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

?>