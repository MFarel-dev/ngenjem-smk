<?php
include '../templates/header_admin.php';

$error_msg = '';
$sukses_msg = '';

// Ambil data Anggota untuk dropdown
$anggota_result = mysqli_query($koneksi, "SELECT id_anggota, nis, nama_anggota FROM tbl_anggota ORDER BY nama_anggota ASC");

// Ambil data Buku untuk dropdown
$buku_result = mysqli_query($koneksi, "SELECT id_buku, judul_buku, jumlah_stok FROM tbl_buku ORDER BY judul_buku ASC");

// --- PROSES SIMPAN PEMINJAMAN ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil data dari form
    $id_anggota = $_POST['id_anggota'];
    $id_buku    = $_POST['id_buku'];
    $tgl_pinjam = $_POST['tgl_pinjam'];
    $tgl_batas_kembali = $_POST['tgl_batas_kembali'];
    $id_admin_login = $_SESSION['admin_id']; // Ambil ID admin yang sedang login
    
    // Validasi dasar
    if (empty($id_anggota) || empty($id_buku) || empty($tgl_pinjam) || empty($tgl_batas_kembali)) {
        $error_msg = "Semua kolom wajib diisi!";
    } else {
        // --- Validasi Stok dan Duplikasi ---
        
        // 1. Cek stok tersedia
        $sql_stok = "SELECT jumlah_stok FROM tbl_buku WHERE id_buku = ?";
        $stmt_stok = mysqli_prepare($koneksi, $sql_stok);
        mysqli_stmt_bind_param($stmt_stok, "i", $id_buku);
        mysqli_stmt_execute($stmt_stok);
        $res_stok = mysqli_stmt_get_result($stmt_stok);
        $data_stok = mysqli_fetch_assoc($res_stok);
        $stok_total = $data_stok['jumlah_stok'];
        
        // Hitung buku yang sedang dipinjam
        $sql_dipinjam = "SELECT COUNT(id_peminjaman) as total_dipinjam FROM tbl_peminjaman WHERE id_buku = ? AND status = 'Dipinjam'";
        $stmt_dipinjam = mysqli_prepare($koneksi, $sql_dipinjam);
        mysqli_stmt_bind_param($stmt_dipinjam, "i", $id_buku);
        mysqli_stmt_execute($stmt_dipinjam);
        $res_dipinjam = mysqli_stmt_get_result($stmt_dipinjam);
        $total_dipinjam = mysqli_fetch_assoc($res_dipinjam)['total_dipinjam'];
        
        $stok_tersedia = $stok_total - $total_dipinjam;
        
        if ($stok_tersedia <= 0) {
            $error_msg = "Gagal! Stok buku yang dipilih sudah habis (Stok Tersedia: 0).";
        } else {
            // 2. Cek apakah anggota ini sudah meminjam buku yang SAMA dan BELUM dikembalikan
            $sql_cek_duplikat = "SELECT id_peminjaman FROM tbl_peminjaman 
                                 WHERE id_anggota = ? AND id_buku = ? AND status = 'Dipinjam'";
            $stmt_duplikat = mysqli_prepare($koneksi, $sql_cek_duplikat);
            mysqli_stmt_bind_param($stmt_duplikat, "ii", $id_anggota, $id_buku);
            mysqli_stmt_execute($stmt_duplikat);
            mysqli_stmt_store_result($stmt_duplikat);
            
            if (mysqli_stmt_num_rows($stmt_duplikat) > 0) {
                $error_msg = "Gagal! Anggota ini sudah meminjam buku yang sama dan belum dikembalikan.";
            } else {
                // --- Jika semua validasi lolos, SIMPAN DATA ---
                $sql_insert = "INSERT INTO tbl_peminjaman 
                               (id_anggota, id_buku, id_admin, tgl_pinjam, tgl_batas_kembali, status) 
                               VALUES (?, ?, ?, ?, ?, 'Dipinjam')";
                
                $stmt_insert = mysqli_prepare($koneksi, $sql_insert);
                mysqli_stmt_bind_param($stmt_insert, "iiiss", 
                    $id_anggota, $id_buku, $id_admin_login, $tgl_pinjam, $tgl_batas_kembali
                );
                
                if (mysqli_stmt_execute($stmt_insert)) {
                    $sukses_msg = "Transaksi peminjaman baru berhasil disimpan!";
                    // Kosongkan variabel agar form siap untuk input baru
                    unset($_POST); 
                } else {
                    $error_msg = "Gagal menyimpan data transaksi: " . mysqli_error($koneksi);
                }
                mysqli_stmt_close($stmt_insert);
            }
            mysqli_stmt_close($stmt_duplikat);
        }
        mysqli_stmt_close($stmt_stok);
        mysqli_stmt_close($stmt_dipinjam);
    }
}
?>
<div class="section-body">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Transaksi Peminjaman Buku</h4>
                </div>
                <div class="card-body">

<div class="col-md-8">
    
    <?php if (!empty($error_msg)): ?>
        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>
    
    <?php if (!empty($sukses_msg)): ?>
        <div class="alert alert-success"><?php echo $sukses_msg; ?></div>
    <?php endif; ?>

    <form action="pinjam.php" method="POST">
        
        <div class="mb-3">
            <label for="id_anggota" class="form-label">Anggota (Siswa) <span class="text-danger">*</span></label>
            <select class="form-control selectric" id="id_anggota" name="id_anggota" required>
                <option value="">-- Pilih Anggota --</option>
                <?php while($anggota = mysqli_fetch_assoc($anggota_result)): ?>
                    <option value="<?php echo $anggota['id_anggota']; ?>">
                        <?php echo htmlspecialchars($anggota['nis'] . ' - ' . $anggota['nama_anggota']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <div class="form-text">Cari berdasarkan NIS atau Nama.</div>
        </div>

        <div class="mb-3">
            <label for="id_buku" class="form-label">Buku <span class="text-danger">*</span></label>
            <select class="form-control selectric" id="id_buku" name="id_buku" required>
                <option value="">-- Pilih Buku --</option>
                <?php while($buku = mysqli_fetch_assoc($buku_result)): 
                    // Kita bisa tambahkan info stok di sini
                ?>
                    <option value="<?php echo $buku['id_buku']; ?>">
                        <?php echo htmlspecialchars($buku['judul_buku']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div class="row">
            <?php
                // Atur tanggal default
                $tgl_sekarang = date('Y-m-d');
                // Atur batas kembali default (misal: 7 hari dari sekarang)
                $tgl_kembali_default = date('Y-m-d', strtotime('+7 days'));
            ?>
            <div class="col-md-6 mb-3">
                <label for="tgl_pinjam" class="form-label">Tanggal Pinjam <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="tgl_pinjam" name="tgl_pinjam" value="<?php echo $tgl_sekarang; ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="tgl_batas_kembali" class="form-label">Batas Tanggal Kembali <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="tgl_batas_kembali" name="tgl_batas_kembali" value="<?php echo $tgl_kembali_default; ?>" required>
            </div>
        </div>

        <hr class="my-4">

        <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
        <button type="reset" class="btn btn-secondary">Reset Form</button>
    </form>
</div>

<?php
include '../templates/footer_admin.php';
?>