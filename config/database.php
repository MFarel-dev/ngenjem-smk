<?php
// Konfigurasi Database
$db_host = 'localhost';
$db_user = 'root'; // Ganti dengan user Anda
$db_pass = '';     // Ganti dengan password Anda
$db_name = 'db_jemngenjem';

// Buat Koneksi
$koneksi = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Cek Koneksi
if (mysqli_connect_errno()) {
    // Jika koneksi gagal, hentikan skrip dan tampilkan error
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set timezone (Penting untuk tanggal transaksi)
date_default_timezone_set('Asia/Jakarta');

// Mulai session di sini agar semua file yang meng-include file ini bisa memakai session
session_start();
?>