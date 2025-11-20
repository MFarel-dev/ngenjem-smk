<?php
include '../templates/header_admin.php';

// Ambil ID buku dari URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Jika tidak ada ID, kembalikan ke halaman buku
    $_SESSION['pesan_error'] = "Permintaan tidak valid. ID buku tidak ditemukan.";
    header('Location: buku.php');
    exit;
}

$id_buku = $_GET['id'];
$error_msg = '';

// Ambil data kategori untuk dropdown
$kategori_result = mysqli_query($koneksi, "SELECT * FROM tbl_kategori ORDER BY nama_kategori");

// --- PROSES UPDATE DATA JIKA FORM DISUBMIT ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil semua data dari form
    $judul       = $_POST['judul_buku'];
    $id_kategori = $_POST['id_kategori'];
    $pengarang   = $_POST['pengarang'];
    $penerbit    = $_POST['penerbit'];
    $tahun       = $_POST['tahun_terbit'];
    $isbn        = $_POST['isbn'];
    $stok        = $_POST['jumlah_stok'];
    $lokasi      = $_POST['lokasi_rak'];
    $cover_lama  = $_POST['cover_lama']; // Ambil nama cover lama

    $nama_file_cover = $cover_lama; // Default pakai nama cover lama

    // Validasi dasar
    if (empty($judul) || empty($id_kategori) || empty($stok)) {
        $error_msg = "Judul, Kategori, dan Jumlah Stok wajib diisi!";
    } else {
        
        // --- PROSES UPLOAD COVER BARU (JIKA ADA) ---
        if (isset($_FILES['cover_buku']) && $_FILES['cover_buku']['error'] == 0) {
            $target_dir = "../uploads/cover/";
            $nama_file_unik = time() . '_' . basename($_FILES["cover_buku"]["name"]);
            $target_file = $target_dir . $nama_file_unik;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Cek validasi gambar (sama seperti di buku_tambah.php)
            $check = getimagesize($_FILES["cover_buku"]["tmp_name"]);
            if ($check !== false) {
                if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                    $error_msg = "Maaf, hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.";
                } else {
                    // Jika lolos validasi, upload file baru
                    if (move_uploaded_file($_FILES["cover_buku"]["tmp_name"], $target_file)) {
                        $nama_file_cover = $nama_file_unik; // Gunakan nama file baru
                        
                        // Hapus file cover lama jika ada
                        if (!empty($cover_lama) && file_exists($target_dir . $cover_lama)) {
                            unlink($target_dir . $cover_lama);
                        }
                    } else {
                        $error_msg = "Maaf, terjadi error saat mengupload file.";
                    }
                }
            } else {
                $error_msg = "File bukan gambar.";
            }
        }
        // --- Akhir Proses Upload ---

        // Jika tidak ada error, lakukan UPDATE ke database
        if (empty($error_msg)) {
            $sql = "UPDATE tbl_buku SET 
                        id_kategori = ?, 
                        judul_buku = ?, 
                        pengarang = ?, 
                        penerbit = ?, 
                        tahun_terbit = ?, 
                        isbn = ?, 
                        jumlah_stok = ?, 
                        lokasi_rak = ?, 
                        cover_buku = ? 
                    WHERE id_buku = ?";
            
            $stmt = mysqli_prepare($koneksi, $sql);
            mysqli_stmt_bind_param($stmt, "issssisssi", 
                $id_kategori, $judul, $pengarang, $penerbit, $tahun, $isbn, $stok, $lokasi, $nama_file_cover, $id_buku
            );

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['pesan_sukses'] = "Data buku berhasil diperbarui!";
                header('Location: buku.php');
                exit;
            } else {
                $error_msg = "Gagal memperbarui data: " . mysqli_error($koneksi);
            }
            mysqli_stmt_close($stmt);
        }
    }
}
// --- AKHIR PROSES UPDATE ---


// --- AMBIL DATA LAMA BUKU UNTUK DITAMPILKAN DI FORM ---
$sql_get = "SELECT * FROM tbl_buku WHERE id_buku = ?";
$stmt_get = mysqli_prepare($koneksi, $sql_get);
mysqli_stmt_bind_param($stmt_get, "i", $id_buku);
mysqli_stmt_execute($stmt_get);
$result_get = mysqli_stmt_get_result($stmt_get);
$buku = mysqli_fetch_assoc($result_get);

if (!$buku) {
    // Jika data buku dengan ID tsb tidak ada
    $_SESSION['pesan_error'] = "Data buku tidak ditemukan.";
    header('Location: buku.php');
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
                    <h4>Edit Buku Perpustakaan</h4>
                </div>
                <div class="card-body">
    
    <?php if (!empty($error_msg)): ?>
        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <form action="buku_edit.php?id=<?php echo $id_buku; ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="cover_lama" value="<?php echo htmlspecialchars($buku['cover_buku']); ?>">

        <div class="mb-3">
            <label for="judul_buku" class="form-label">Judul Buku <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="judul_buku" name="judul_buku" value="<?php echo htmlspecialchars($buku['judul_buku']); ?>" required>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="id_kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                <select class="form-control selectric" id="id_kategori" name="id_kategori" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php mysqli_data_seek($kategori_result, 0); // Reset pointer hasil query kategori ?>
                    <?php while($kat = mysqli_fetch_assoc($kategori_result)): ?>
                        <option value="<?php echo $kat['id_kategori']; ?>" 
                            <?php echo ($kat['id_kategori'] == $buku['id_kategori']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($kat['nama_kategori']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="pengarang" class="form-label">Pengarang</label>
                <input type="text" class="form-control" id="pengarang" name="pengarang" value="<?php echo htmlspecialchars($buku['pengarang']); ?>">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="penerbit" class="form-label">Penerbit</label>
                <input type="text" class="form-control" id="penerbit" name="penerbit" value="<?php echo htmlspecialchars($buku['penerbit']); ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="tahun_terbit" class="form-label">Tahun Terbit</label>
                <input type="number" class="form-control" id="tahun_terbit" name="tahun_terbit" value="<?php echo htmlspecialchars($buku['tahun_terbit']); ?>" min="1900" max="<?php echo date('Y'); ?>">
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="jumlah_stok" class="form-label">Jumlah Stok <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="jumlah_stok" name="jumlah_stok" value="<?php echo htmlspecialchars($buku['jumlah_stok']); ?>" required min="0">
            </div>
            <div class="col-md-4 mb-3">
                <label for="lokasi_rak" class="form-label">Lokasi Rak</label>
                <input type="text" class="form-control" id="lokasi_rak" name="lokasi_rak" value="<?php echo htmlspecialchars($buku['lokasi_rak']); ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label for="isbn" class="form-label">ISBN</label>
                <input type="text" class="form-control" id="isbn" name="isbn" value="<?php echo htmlspecialchars($buku['isbn']); ?>">
            </div>
        </div>
        
        <div class="mb-3">
            <label for="cover_buku" class="form-label">Ganti Cover Buku</label>
            <input class="form-control" type="file" id="cover_buku" name="cover_buku">
            <div class="form-text">Kosongkan jika tidak ingin mengganti cover.</div>
            
            <?php if(!empty($buku['cover_buku']) && file_exists('../uploads/cover/' . $buku['cover_buku'])): ?>
                <div class.mt-2">
                    <img src="../uploads/cover/<?php echo htmlspecialchars($buku['cover_buku']); ?>" alt="Cover Lama" height="100">
                </div>
            <?php endif; ?>
        </div>

        <hr class="my-4">

        <button type="submit" class="btn btn-primary">Update Data</button>
        <a href="buku.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
</div></div></div></div>
<?php
include '../templates/footer_admin.php';
?>