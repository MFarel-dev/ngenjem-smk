<?php
include '../templates/header_admin.php';

$error_msg = '';

// Proses form jika di-submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil data dari form
    $nis   = $_POST['nis'];
    $nama  = $_POST['nama_anggota'];
    $kelas = $_POST['kelas'];
    $no_hp = $_POST['no_hp'];
    
    // Validasi dasar
    if (empty($nis) || empty($nama)) {
        $error_msg = "NIS dan Nama Anggota wajib diisi!";
    } else {
        // Cek apakah NIS sudah ada
        $sql_cek = "SELECT nis FROM tbl_anggota WHERE nis = ?";
        $stmt_cek = mysqli_prepare($koneksi, $sql_cek);
        mysqli_stmt_bind_param($stmt_cek, "s", $nis);
        mysqli_stmt_execute($stmt_cek);
        mysqli_stmt_store_result($stmt_cek);
        
        if (mysqli_stmt_num_rows($stmt_cek) > 0) {
            $error_msg = "Gagal! NIS (Nomor Induk Siswa) ini sudah terdaftar.";
        } else {
            // Jika aman, masukkan data ke database
            $sql = "INSERT INTO tbl_anggota (nis, nama_anggota, kelas, no_hp) 
                    VALUES (?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($koneksi, $sql);
            mysqli_stmt_bind_param($stmt, "ssss", $nis, $nama, $kelas, $no_hp);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['pesan_sukses'] = "Anggota baru berhasil ditambahkan!";
                header('Location: anggota.php');
                exit;
            } else {
                $error_msg = "Gagal menyimpan data ke database: " . mysqli_error($koneksi);
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_stmt_close($stmt_cek);
    }
}
?>

<div class="section-body">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Tambah Anggota Perpustakaan</h4>
                </div>
                <div class="card-body">

<div class="col-md-8">
    
    <?php if (!empty($error_msg)): ?>
        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <form action="anggota_tambah.php" method="POST">
        <div class="mb-3">
            <label for="nis" class="form-label">NIS (Nomor Induk Siswa) <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="nis" name="nis" required>
        </div>
        
        <div class="mb-3">
            <label for="nama_anggota" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="nama_anggota" name="nama_anggota" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="kelas" class="form-label">Kelas</label>
                <input type="text" class="form-control" id="kelas" name="kelas" placeholder="Contoh: XII TKJ 1">
            </div>
            <div class="col-md-6 mb-3">
                <label for="no_hp" class="form-label">No. HP (WhatsApp)</label>
                <input type="text" class="form-control" id="no_hp" name="no_hp" placeholder="Contoh: 0812...">
            </div>
        </div>

        <hr class="my-4">

        <button type="submit" class="btn btn-primary">Simpan Anggota</button>
        <a href="anggota.php" class="btn btn-secondary">Batal</a>
    </form>
</div>

<?php
include '../templates/footer_admin.php';
?>