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
    $_SESSION['pesan_error'] = "Permintaan tidak valid. ID anggota tidak ditemukan.";
    header('Location: anggota.php');
    exit;
}

$id_anggota = $_GET['id'];

// PENTING: Cek dulu apakah anggota sedang meminjam buku
$sql_cek = "SELECT COUNT(*) as total FROM tbl_peminjaman WHERE id_anggota = ? AND status = 'Dipinjam'";
$stmt_cek = mysqli_prepare($koneksi, $sql_cek);
mysqli_stmt_bind_param($stmt_cek, "i", $id_anggota);
mysqli_stmt_execute($stmt_cek);
$res_cek = mysqli_stmt_get_result($stmt_cek);
$cek = mysqli_fetch_assoc($res_cek);

if ($cek['total'] > 0) {
    // Jika anggota sedang meminjam, JANGAN HAPUS
    $_SESSION['pesan_error'] = "Gagal menghapus! Anggota ini masih memiliki buku yang sedang dipinjam.";
    header('Location: anggota.php');
    exit;
}

// Jika aman, lanjutkan proses hapus
$sql_delete = "DELETE FROM tbl_anggota WHERE id_anggota = ?";
$stmt_delete = mysqli_prepare($koneksi, $sql_delete);
mysqli_stmt_bind_param($stmt_delete, "i", $id_anggota);

if (mysqli_stmt_execute($stmt_delete)) {
    $_SESSION['pesan_sukses'] = "Data anggota berhasil dihapus.";
} else {
    // Jika gagal, kemungkinan karena ada relasi di tbl_peminjaman (meski status 'Kembali')
    // Ini bisa diatasi dengan 'ON DELETE RESTRICT' di database, tapi pesan error ini cukup
    $_SESSION['pesan_error'] = "Gagal menghapus data anggota. Mungkin anggota ini memiliki riwayat peminjaman.";
}

mysqli_stmt_close($stmt_cek);
mysqli_stmt_close($stmt_delete);

// Kembalikan ke halaman anggota
header('Location: anggota.php');
exit;
?>