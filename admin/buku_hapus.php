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
    $_SESSION['pesan_error'] = "Permintaan tidak valid. ID buku tidak ditemukan.";
    header('Location: buku.php');
    exit;
}

$id_buku = $_GET['id'];

// PENTING: Cek dulu apakah buku sedang dipinjam
$sql_cek = "SELECT COUNT(*) as total FROM tbl_peminjaman WHERE id_buku = ? AND status = 'Dipinjam'";
$stmt_cek = mysqli_prepare($koneksi, $sql_cek);
mysqli_stmt_bind_param($stmt_cek, "i", $id_buku);
mysqli_stmt_execute($stmt_cek);
$res_cek = mysqli_stmt_get_result($stmt_cek);
$cek = mysqli_fetch_assoc($res_cek);

if ($cek['total'] > 0) {
    // Jika buku sedang dipinjam, JANGAN HAPUS
    $_SESSION['pesan_error'] = "Gagal menghapus! Buku ini sedang dalam proses peminjaman oleh anggota.";
    header('Location: buku.php');
    exit;
}

// Jika aman, lanjutkan proses hapus
// 1. Ambil nama file cover untuk dihapus
$sql_get = "SELECT cover_buku FROM tbl_buku WHERE id_buku = ?";
$stmt_get = mysqli_prepare($koneksi, $sql_get);
mysqli_stmt_bind_param($stmt_get, "i", $id_buku);
mysqli_stmt_execute($stmt_get);
$result_get = mysqli_stmt_get_result($stmt_get);
$buku = mysqli_fetch_assoc($result_get);

// 2. Hapus data dari database
$sql_delete = "DELETE FROM tbl_buku WHERE id_buku = ?";
$stmt_delete = mysqli_prepare($koneksi, $sql_delete);
mysqli_stmt_bind_param($stmt_delete, "i", $id_buku);

if (mysqli_stmt_execute($stmt_delete)) {
    // 3. Jika data di DB berhasil dihapus, hapus file gambarnya
    if ($buku && !empty($buku['cover_buku'])) {
        $target_file = "../uploads/cover/" . $buku['cover_buku'];
        if (file_exists($target_file)) {
            unlink($target_file);
        }
    }
    
    $_SESSION['pesan_sukses'] = "Data buku berhasil dihapus.";
} else {
    $_SESSION['pesan_error'] = "Gagal menghapus data buku: " . mysqli_error($koneksi);
}

mysqli_stmt_close($stmt_cek);
mysqli_stmt_close($stmt_get);
mysqli_stmt_close($stmt_delete);

// Kembalikan ke halaman buku
header('Location: buku.php');
exit;
?>