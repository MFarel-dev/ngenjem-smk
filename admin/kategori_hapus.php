<?php
// Wajib ada di atas, karena file ini akan me-redirect
include '../config/database.php'; 

// Cek Sesi Login
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

// Ambil ID dari URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['pesan_error'] = "Permintaan tidak valid. ID kategori tidak ditemukan.";
    header('Location: kategori.php');
    exit;
}

$id_kategori = $_GET['id'];

// PENTING: Cek dulu apakah kategori sedang dipakai oleh buku
$sql_cek = "SELECT COUNT(*) as total FROM tbl_buku WHERE id_kategori = ?";
$stmt_cek = mysqli_prepare($koneksi, $sql_cek);
mysqli_stmt_bind_param($stmt_cek, "i", $id_kategori);
mysqli_stmt_execute($stmt_cek);
$res_cek = mysqli_stmt_get_result($stmt_cek);
$cek = mysqli_fetch_assoc($res_cek);

if ($cek['total'] > 0) {
    // Jika kategori sedang dipakai, JANGAN HAPUS
    $_SESSION['pesan_error'] = "Gagal menghapus! Kategori ini sedang digunakan oleh (" . $cek['total'] . ") buku.";
    header('Location: kategori.php');
    exit;
}

// Jika aman, lanjutkan proses hapus
$sql_delete = "DELETE FROM tbl_kategori WHERE id_kategori = ?";
$stmt_delete = mysqli_prepare($koneksi, $sql_delete);
mysqli_stmt_bind_param($stmt_delete, "i", $id_kategori);

if (mysqli_stmt_execute($stmt_delete)) {
    $_SESSION['pesan_sukses'] = "Data kategori berhasil dihapus.";
} else {
    $_SESSION['pesan_error'] = "Gagal menghapus data kategori: " . mysqli_error($koneksi);
}

mysqli_stmt_close($stmt_cek);
mysqli_stmt_close($stmt_delete);

// Kembalikan ke halaman kategori
header('Location: kategori.php');
exit;
?>