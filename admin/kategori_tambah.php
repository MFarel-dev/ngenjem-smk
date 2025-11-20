<?php
include '../templates/header_admin.php';

$error_msg = '';

// Proses form jika di-submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil data dari form
    $nama_kategori = $_POST['nama_kategori'];
    
    // Validasi dasar
    if (empty($nama_kategori)) {
        $error_msg = "Nama Kategori wajib diisi!";
    } else {
        // Cek apakah nama kategori sudah ada
        $sql_cek = "SELECT nama_kategori FROM tbl_kategori WHERE nama_kategori = ?";
        $stmt_cek = mysqli_prepare($koneksi, $sql_cek);
        mysqli_stmt_bind_param($stmt_cek, "s", $nama_kategori);
        mysqli_stmt_execute($stmt_cek);
        mysqli_stmt_store_result($stmt_cek);
        
        if (mysqli_stmt_num_rows($stmt_cek) > 0) {
            $error_msg = "Gagal! Nama Kategori ini sudah ada.";
        } else {
            // Jika aman, masukkan data ke database
            $sql = "INSERT INTO tbl_kategori (nama_kategori) VALUES (?)";
            
            $stmt = mysqli_prepare($koneksi, $sql);
            mysqli_stmt_bind_param($stmt, "s", $nama_kategori);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['pesan_sukses'] = "Kategori baru berhasil ditambahkan!";
                header('Location: kategori.php');
                exit;
            } else {
                $error_msg = "Gagal menyimpan data: " . mysqli_error($koneksi);
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
                    <h4>Tambah Kategori Buku Perpustakaan</h4>
                </div>
                <div class="card-body">

<div class="col-md-6">
    
    <?php if (!empty($error_msg)): ?>
        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <form action="kategori_tambah.php" method="POST">
        <div class="mb-3">
            <label for="nama_kategori" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" required>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Kategori</button>
        <a href="kategori.php" class="btn btn-secondary">Batal</a>
    </form>
</div>

<?php
include '../templates/footer_admin.php';
?>