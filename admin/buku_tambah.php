<?php
include '../templates/header_admin.php';

$error_msg = '';
$sukses_msg = '';

// Ambil data kategori untuk dropdown
$kategori_result = mysqli_query($koneksi, "SELECT * FROM tbl_kategori ORDER BY nama_kategori");

// Proses form jika di-submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil data dari form
    $judul = $_POST['judul_buku'];
    $id_kategori = $_POST['id_kategori'];
    $pengarang = $_POST['pengarang'];
    $penerbit = $_POST['penerbit'];
    $tahun = $_POST['tahun_terbit'];
    $isbn = $_POST['isbn'];
    $stok = $_POST['jumlah_stok'];
    $lokasi = $_POST['lokasi_rak'];

    $nama_file_cover = '';

    // Validasi dasar
    if (empty($judul) || empty($id_kategori) || empty($stok)) {
        $error_msg = "Judul, Kategori, dan Jumlah Stok wajib diisi!";
    } else {

        // Proses Upload Cover
        if (isset($_FILES['cover_buku']) && $_FILES['cover_buku']['error'] == 0) {
            $target_dir = "../uploads/cover/";
            // Buat nama file unik: timestamp + nama asli
            $nama_file_unik = time() . '_' . basename($_FILES["cover_buku"]["name"]);
            $target_file = $target_dir . $nama_file_unik;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Cek apakah file adalah gambar
            $check = getimagesize($_FILES["cover_buku"]["tmp_name"]);
            if ($check !== false) {
                // Cek ekstensi
                if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                    $error_msg = "Maaf, hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.";
                } else {
                    // Coba pindahkan file
                    if (move_uploaded_file($_FILES["cover_buku"]["tmp_name"], $target_file)) {
                        $nama_file_cover = $nama_file_unik;
                    } else {
                        $error_msg = "Maaf, terjadi error saat mengupload file.";
                    }
                }
            } else {
                $error_msg = "File bukan gambar.";
            }
        }

        // Jika tidak ada error validasi atau upload
        if (empty($error_msg)) {
            // Masukkan data ke database menggunakan Prepared Statement
            $sql = "INSERT INTO tbl_buku 
                    (id_kategori, judul_buku, pengarang, penerbit, tahun_terbit, isbn, jumlah_stok, lokasi_rak, cover_buku) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = mysqli_prepare($koneksi, $sql);

            // 'issssisss' = tipe data: 
            // i: integer (id_kategori)
            // s: string (judul_buku)
            // s: string (pengarang)
            // s: string (penerbit)
            // s: string (tahun_terbit - bisa 's' atau 'i')
            // s: string (isbn)
            // i: integer (jumlah_stok)
            // s: string (lokasi_rak)
            // s: string (nama_file_cover)

            mysqli_stmt_bind_param(
                $stmt,
                "issssisss",
                $id_kategori,
                $judul,
                $pengarang,
                $penerbit,
                $tahun,
                $isbn,
                $stok,
                $lokasi,
                $nama_file_cover
            );

            if (mysqli_stmt_execute($stmt)) {
                // Jika sukses, set session flash message dan redirect
                $_SESSION['pesan_sukses'] = "Buku baru berhasil ditambahkan!";
                header('Location: buku.php');
                exit;
            } else {
                $error_msg = "Gagal menyimpan data ke database: " . mysqli_error($koneksi);
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<div class="section-body">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Tambah Buku Perpustakaan</h4>
                </div>
                <div class="card-body">

                    <?php if (!empty($error_msg)): ?>
                        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
                    <?php endif; ?>

                    <form action="buku_tambah.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="judul_buku" class="form-label">Judul Buku <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="judul_buku" name="judul_buku" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="id_kategori" class="form-label">Kategori <span
                                        class="text-danger">*</span></label>
                                <select class="form-control selectric" id="id_kategori" name="id_kategori" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php while ($kat = mysqli_fetch_assoc($kategori_result)): ?>
                                        <option value="<?php echo $kat['id_kategori']; ?>">
                                            <?php echo htmlspecialchars($kat['nama_kategori']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pengarang" class="form-label">Pengarang</label>
                                <input type="text" class="form-control" id="pengarang" name="pengarang">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="penerbit" class="form-label">Penerbit</label>
                                <input type="text" class="form-control" id="penerbit" name="penerbit">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tahun_terbit" class="form-label">Tahun Terbit</label>
                                <input type="number" class="form-control" id="tahun_terbit" name="tahun_terbit"
                                    placeholder="Contoh: 2023" min="1900" max="<?php echo date('Y'); ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="jumlah_stok" class="form-label">Jumlah Stok <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="jumlah_stok" name="jumlah_stok" required
                                    min="0">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="lokasi_rak" class="form-label">Lokasi Rak</label>
                                <input type="text" class="form-control" id="lokasi_rak" name="lokasi_rak"
                                    placeholder="Contoh: A-01">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="isbn" class="form-label">ISBN</label>
                                <input type="text" class="form-control" id="isbn" name="isbn">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="cover_buku" class="form-label">Cover Buku</label>
                            <input class="form-control" type="file" id="cover_buku" name="cover_buku">
                            <div class="form-text">Hanya file .jpg, .jpeg, .png, .gif. Ukuran maks 2MB.</div>
                        </div>

                        <hr class="my-4">

                        <button type="submit" class="btn btn-primary">Simpan Buku</button>
                        <a href="buku.php" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include '../templates/footer_admin.php';
?>