<?php
include '../templates/header_admin.php';

// Ambil ID anggota dari URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['pesan_error'] = "Permintaan tidak valid. ID anggota tidak ditemukan.";
    header('Location: anggota.php');
    exit;
}

$id_anggota = $_GET['id'];
$error_msg = '';

// --- PROSES UPDATE DATA JIKA FORM DISUBMIT ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil semua data dari form
    $nis   = $_POST['nis'];
    $nama  = $_POST['nama_anggota'];
    $kelas = $_POST['kelas'];
    $no_hp = $_POST['no_hp'];

    // Validasi dasar
    if (empty($nis) || empty($nama)) {
        $error_msg = "NIS dan Nama Anggota wajib diisi!";
    } else {
        // Cek apakah NIS baru sudah dipakai oleh anggota LAIN
        $sql_cek = "SELECT id_anggota FROM tbl_anggota WHERE nis = ? AND id_anggota != ?";
        $stmt_cek = mysqli_prepare($koneksi, $sql_cek);
        mysqli_stmt_bind_param($stmt_cek, "si", $nis, $id_anggota);
        mysqli_stmt_execute($stmt_cek);
        mysqli_stmt_store_result($stmt_cek);
        
        if (mysqli_stmt_num_rows($stmt_cek) > 0) {
            $error_msg = "Gagal! NIS ini sudah terdaftar untuk anggota lain.";
        } else {
            // Jika aman, lakukan UPDATE ke database
            $sql = "UPDATE tbl_anggota SET 
                        nis = ?, 
                        nama_anggota = ?, 
                        kelas = ?, 
                        no_hp = ? 
                    WHERE id_anggota = ?";
            
            $stmt = mysqli_prepare($koneksi, $sql);
            mysqli_stmt_bind_param($stmt, "ssssi", 
                $nis, $nama, $kelas, $no_hp, $id_anggota
            );

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['pesan_sukses'] = "Data anggota berhasil diperbarui!";
                header('Location: anggota.php');
                exit;
            } else {
                $error_msg = "Gagal memperbarui data: " . mysqli_error($koneksi);
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_stmt_close($stmt_cek);
    }
}
// --- AKHIR PROSES UPDATE ---


// --- AMBIL DATA LAMA ANGGOTA UNTUK DITAMPILKAN DI FORM ---
$sql_get = "SELECT * FROM tbl_anggota WHERE id_anggota = ?";
$stmt_get = mysqli_prepare($koneksi, $sql_get);
mysqli_stmt_bind_param($stmt_get, "i", $id_anggota);
mysqli_stmt_execute($stmt_get);
$result_get = mysqli_stmt_get_result($stmt_get);
$anggota = mysqli_fetch_assoc($result_get);

if (!$anggota) {
    $_SESSION['pesan_error'] = "Data anggota tidak ditemukan.";
    header('Location: anggota.php');
    exit;
}
mysqli_stmt_close($stmt_get);
// --- AKHIR AMBIL DATA LAMA ---

?>

<div class="section-body">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Edit Anggota Perpustakaan</h4>
                </div>
                <div class="card-body">

<div class="col-md-8">
    
    <?php if (!empty($error_msg)): ?>
        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <form action="anggota_edit.php?id=<?php echo $id_anggota; ?>" method="POST">
        <div class="mb-3">
            <label for="nis" class="form-label">NIS (Nomor Induk Siswa) <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="nis" name="nis" value="<?php echo htmlspecialchars($anggota['nis']); ?>" required>
        </div>
        
        <div class="mb-3">
            <label for="nama_anggota" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="nama_anggota" name="nama_anggota" value="<?php echo htmlspecialchars($anggota['nama_anggota']); ?>" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="kelas" class="form-label">Kelas</label>
                <input type="text" class="form-control" id="kelas" name="kelas" value="<?php echo htmlspecialchars($anggota['kelas']); ?>" placeholder="Contoh: XII TKJ 1">
            </div>
            <div class="col-md-6 mb-3">
                <label for="no_hp" class="form-label">No. HP (WhatsApp)</label>
                <input type="text" class="form-control" id="no_hp" name="no_hp" value="<?php echo htmlspecialchars($anggota['no_hp']); ?>" placeholder="Contoh: 0812...">
            </div>
        </div>

        <hr class="my-4">

        <button type="submit" class="btn btn-primary">Update Data</button>
        <a href="anggota.php" class="btn btn-secondary">Batal</a>
    </form>
</div>

<?php
include '../templates/footer_admin.php';
?>