<?php
include '../templates/header_admin.php';

// --- AMBIL DATA SEMUA TRANSAKSI ---
$sql = "SELECT 
            p.*,
            a.nama_anggota,
            a.nis,
            b.judul_buku,
            adm.nama_lengkap as nama_admin
        FROM tbl_peminjaman p
        JOIN tbl_anggota a ON p.id_anggota = a.id_anggota
        JOIN tbl_buku b ON p.id_buku = b.id_buku
        JOIN tbl_admin adm ON p.id_admin = adm.id_admin
        ORDER BY p.id_peminjaman DESC"; // Tampilkan yang terbaru di atas

$result = mysqli_query($koneksi, $sql);

// --- Hitung Total Denda yang Terkumpul ---
$sql_denda = "SELECT SUM(denda) as total_denda FROM tbl_peminjaman WHERE status = 'Kembali'";
$res_denda = mysqli_query($koneksi, $sql_denda);
$total_denda = mysqli_fetch_assoc($res_denda)['total_denda'];

?>
<div class="section-body">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Laporan Riwayat Transaksi</h4>
                </div>
                <div class="card-body">

<div class="row">
    <div class="col-md-4">
        <div class="card text-white bg-info mb-3">
            <div class="card-header">Total Denda Terkumpul</div>
            <div class="card-body">
                <h5 class="card-title display-4">Rp <?php echo number_format($total_denda, 0, ',', '.'); ?></h5>
            </div>
        </div>
    </div>
</div>


<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Anggota</th>
                <th scope="col">Judul Buku</th>
                <th scope="col">Tgl Pinjam</th>
                <th scope="col">Batas Kembali</th>
                <th scope="col">Tgl Kembali</th>
                <th scope="col">Status</th>
                <th scope="col">Denda</th>
                <th scope="col">Admin</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if(mysqli_num_rows($result) > 0):
                $no = 1;
                while($trx = mysqli_fetch_assoc($result)):
            ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo htmlspecialchars($trx['nama_anggota']); ?></td>
                <td><?php echo htmlspecialchars($trx['judul_buku']); ?></td>
                <td><?php echo date('d-m-Y', strtotime($trx['tgl_pinjam'])); ?></td>
                <td><?php echo date('d-m-Y', strtotime($trx['tgl_batas_kembali'])); ?></td>
                <td>
                    <?php 
                    // Tampilkan tgl_kembali hanya jika statusnya 'Kembali'
                    if($trx['status'] == 'Kembali') {
                        echo date('d-m-Y', strtotime($trx['tgl_kembali']));
                    } else {
                        echo '-';
                    }
                    ?>
                </td>
                <td>
                    <?php 
                    if($trx['status'] == 'Dipinjam') {
                        echo '<span class="badge bg-warning">Dipinjam</span>';
                    } else {
                        echo '<span class="badge bg-success">Kembali</span>';
                    }
                    ?>
                </td>
                <td>Rp <?php echo number_format($trx['denda'], 0, ',', '.'); ?></td>
                <td><?php echo htmlspecialchars($trx['nama_admin']); ?></td>
            </tr>
            <?php 
                endwhile;
            else:
            ?>
            <tr>
                <td colspan="9" class="text-center">Belum ada riwayat transaksi.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
// Tutup koneksi jika sudah selesai
mysqli_close($koneksi);
include '../templates/footer_admin.php';
?>