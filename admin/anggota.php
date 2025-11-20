<?php
include '../templates/header_admin.php';

// Ambil data anggota
$sql = "SELECT * FROM tbl_anggota ORDER BY nama_anggota ASC";
$result = mysqli_query($koneksi, $sql);
?>
<div class="section-body">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Data Anggota Perpustakaan</h4>
                </div>
                <div class="card-body">

<a href="anggota_tambah.php" class="btn btn-primary mb-3">Tambah Anggota Baru</a>

<?php 
// Tampilkan pesan sukses/error jika ada
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
                <th scope="col">NIS</th>
                <th scope="col">Nama Anggota</th>
                <th scope="col">Kelas</th>
                <th scope="col">No. HP</th>
                <th scope="col">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if(mysqli_num_rows($result) > 0):
                $no = 1;
                while($anggota = mysqli_fetch_assoc($result)):
            ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo htmlspecialchars($anggota['nis']); ?></td>
                <td><?php echo htmlspecialchars($anggota['nama_anggota']); ?></td>
                <td><?php echo htmlspecialchars($anggota['kelas']); ?></td>
                <td><?php echo htmlspecialchars($anggota['no_hp']); ?></td>
                <td>
                    <a href="anggota_edit.php?id=<?php echo $anggota['id_anggota']; ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="anggota_hapus.php?id=<?php echo $anggota['id_anggota']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus anggota ini?');">Hapus</a>
                </td>
            </tr>
            <?php 
                endwhile;
            else:
            ?>
            <tr>
                <td colspan="6" class="text-center">Data anggota masih kosong.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
include '../templates/footer_admin.php';
?>