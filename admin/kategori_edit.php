<?php
include '../templates/header_admin.php';

// Ambil ID kategori dari URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['pesan_error'] = "Permintaan tidak valid. ID kategori tidak ditemukan.";
    header('Location: kategori.php');
    exit;
}

$id_kategori = $_GET['id'];
$error_msg = '';

// --- PROSES UPDATE DATA JIKA FORM DISUBMIT ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nama_kategori = $_POST['nama_kategori'];

    if (empty($nama_kategori)) {
        $error_msg = "Nama Kategori wajib diisi!";
    } else {
        // Cek apakah nama baru sudah dipakai oleh kategori LAIN
        $sql_cek = "SELECT id_kategori FROM tbl_kategori WHERE nama_kategori = ? AND id_kategori != ?";
        $stmt_cek = mysqli_prepare($koneksi, $sql_cek);
        mysqli_stmt_bind_param($stmt_cek, "si", $nama_kategori, $id_kategori);
        mysqli_stmt_execute($stmt_cek);
        mysqli_stmt_store_result($stmt_cek);
        
        if (mysqli_stmt_num_rows($stmt_cek) > 0) {
            $error_msg = "Gagal! Nama Kategori ini sudah terdaftar.";
        } else {
            // Jika aman, lakukan UPDATE
            $sql = "UPDATE tbl_kategori SET nama_kategori = ? WHERE id_kategori = ?";
            $stmt = mysqli_prepare($koneksi, $sql);
            mysqli_stmt_bind_param($stmt, "si", $nama_kategori, $id_kategori);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['pesan_sukses'] = "Data kategori berhasil diperbarui!";
                header('Location: kategori.php');
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


// --- AMBIL DATA LAMA KATEGORI UNTUK FORM ---
$sql_get = "SELECT * FROM tbl_kategori WHERE id_kategori = ?";
$stmt_get = mysqli_prepare($koneksi, $sql_get);
mysqli_stmt_bind_param($stmt_get, "i", $id_kategori);
mysqli_stmt_execute($stmt_get);
$result_get = mysqli_stmt_get_result($stmt_get);
$kategori = mysqli_fetch_assoc($result_get);

if (!$kategori) {
    $_SESSION['pesan_error'] = "Data kategori tidak ditemukan.";
    header('Location: kategori.php');
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
                    <h4>Edit Kategori Buku Perpustakaan</h4>
                </div>
                <div class="card-body">

<div class="col-md-6">
    
    <?php if (!empty($error_msg)): ?>
        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <form action="kategori_edit.php?id=<?php echo $id_kategori; ?>" method="POST">
        <div class="mb-3">
            <label for="nama_kategori" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" value="<?php echo htmlspecialchars($kategori['nama_kategori']); ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Update Data</button>
        <a href="kategori.php" class="btn btn-secondary">Batal</a>
    </form>
</div>

<?php
include '../templates/footer_admin.php';
?>