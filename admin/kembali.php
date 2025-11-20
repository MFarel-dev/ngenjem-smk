<?php
include '../templates/header_admin.php';

// Atur tarif denda per hari (misal: Rp 1000)
define('TARIF_DENDA_PER_HARI', 1000);

$error_msg = '';
$sukses_msg = '';
$tgl_sekarang = date('Y-m-d'); // Tanggal hari ini

// --- PROSES PENGEMBALIAN JIKA ADA POST REQUEST ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil data dari form 'Aksi'
    $id_peminjaman = $_POST['id_peminjaman'];
    $denda_final = $_POST['denda_final'];
    
    // Update data di tabel peminjaman
    $sql_update = "UPDATE tbl_peminjaman SET 
                        status = 'Kembali', 
                        tgl_kembali = ?, 
                        denda = ? 
                   WHERE id_peminjaman = ?";
                   
    $stmt_update = mysqli_prepare($koneksi, $sql_update);
    mysqli_stmt_bind_param($stmt_update, "sii", $tgl_sekarang, $denda_final, $id_peminjaman);
    
    if (mysqli_stmt_execute($stmt_update)) {
        // Set pesan sukses untuk ditampilkan setelah redirect
        $_SESSION['pesan_sukses'] = "Buku berhasil dikembalikan!";
    } else {
        $_SESSION['pesan_error'] = "Gagal memproses pengembalian: " . mysqli_error($koneksi);
    }
    mysqli_stmt_close($stmt_update);
    
    // Redirect kembali ke halaman ini (Pola Post-Redirect-Get)
    // untuk mencegah form disubmit ulang jika di-refresh
    header('Location: kembali.php');
    exit;
}
// --- AKHIR PROSES POST ---


// --- AMBIL DATA PINJAMAN AKTIF (UNTUK DITAMPILKAN) ---
$sql = "SELECT 
            p.id_peminjaman,
            p.tgl_pinjam,
            p.tgl_batas_kembali,
            a.nama_anggota,
            a.nis,
            b.judul_buku
        FROM tbl_peminjaman p
        JOIN tbl_anggota a ON p.id_anggota = a.id_anggota
        JOIN tbl_buku b ON p.id_buku = b.id_buku
        WHERE p.status = 'Dipinjam'
        ORDER BY p.tgl_batas_kembali ASC"; // Tampilkan yang paling telat di atas

$result = mysqli_query($koneksi, $sql);

?>

<div class="section-body">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Pengembalian Buku Dan Peminjaman Aktif</h4>
                </div>
                <div class="card-body">
<?php 
// Tampilkan pesan sukses/error (dari hasil redirect POST)
if(isset($_SESSION['pesan_sukses'])): ?>
    <div class="alert alert-success">
        <?php echo $_SESSION['pesan_sukses']; unset($_SESSION['pesan_sukses']); ?>
    </div>
<?php elseif(isset($_SESSION['pesan_error'])): ?>
    <div class="alert alert-danger">
        <?php echo $_SESSION['pesan_error']; unset($_SESSION['pesan_error']); ?>
    </div>
<?php endif; ?>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Anggota</th>
                <th scope="col">Judul Buku</th>
                <th scope="col">Tgl Pinjam</th>
                <th scope="col">Batas Kembali</th>
                <th scope="col">Keterlambatan</th>
                <th scope="col">Denda (Rp)</th>
                <th scope="col">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if(mysqli_num_rows($result) > 0):
                $no = 1;
                while($pinjam = mysqli_fetch_assoc($result)):
                    
                    // --- Logika Perhitungan Denda ---
                    $keterlambatan = 0;
                    $denda = 0;
                    
                    // Ubah tanggal string ke objek DateTime
                    $tgl_batas = new DateTime($pinjam['tgl_batas_kembali']);
                    $tgl_today = new DateTime($tgl_sekarang);
                    
                    // Cek apakah hari ini sudah melewati batas kembali
                    if ($tgl_today > $tgl_batas) {
                        $interval = $tgl_today->diff($tgl_batas);
                        $keterlambatan = $interval->days; // Ambil selisih hari
                        $denda = $keterlambatan * TARIF_DENDA_PER_HARI;
                    }
                    // --- Akhir Logika Denda ---
            ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo htmlspecialchars($pinjam['nama_anggota'] . ' (' . $pinjam['nis'] . ')'); ?></td>
                <td><?php echo htmlspecialchars($pinjam['judul_buku']); ?></td>
                <td><?php echo date('d-m-Y', strtotime($pinjam['tgl_pinjam'])); ?></td>
                <td><?php echo date('d-m-Y', strtotime($pinjam['tgl_batas_kembali'])); ?></td>
                <td>
                    <?php if ($keterlambatan > 0): ?>
                        <span class="badge bg-danger"><?php echo $keterlambatan; ?> Hari</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">0 Hari</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php echo number_format($denda, 0, ',', '.'); ?>
                </td>
                <td>
                    <form action="kembali.php" method="POST" onsubmit="return confirm('Konfirmasi pengembalian buku: <?php echo htmlspecialchars(addslashes($pinjam['judul_buku'])); ?>?');">
                        <input type="hidden" name="id_peminjaman" value="<?php echo $pinjam['id_peminjaman']; ?>">
                        <input type="hidden" name="denda_final" value="<?php echo $denda; ?>">
                        <button type="submit" class="btn btn-success btn-sm">Kembalikan</button>
                    </form>
                </td>
            </tr>
            <?php 
                endwhile;
            else:
            ?>
            <tr>
                <td colspan="8" class="text-center">Tidak ada buku yang sedang dipinjam.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
include '../templates/footer_admin.php';
?>