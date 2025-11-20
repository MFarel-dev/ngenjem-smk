<?php
include '../templates/header_admin.php';

// Ambil data kategori
$sql = "SELECT * FROM tbl_kategori ORDER BY nama_kategori ASC";
$result = mysqli_query($koneksi, $sql);
?>
<div class="section-body">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Data Kategori Buku Perpustakaan</h4>
                </div>
                <div class="card-body">
<a href="kategori_tambah.php" class="btn btn-primary mb-3">Tambah Kategori Baru</a>

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

<div class="col-md-8">
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col" style="width: 10%;">#</th>
                    <th scope="col" style="width: 60%;">Nama Kategori</th>
                    <th scope="col" style="width: 30%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if(mysqli_num_rows($result) > 0):
                    $no = 1;
                    while($kategori = mysqli_fetch_assoc($result)):
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($kategori['nama_kategori']); ?></td>
                    <td>
                        <a href="kategori_edit.php?id=<?php echo $kategori['id_kategori']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="kategori_hapus.php?id=<?php echo $kategori['id_kategori']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');">Hapus</a>
                    </td>
                </tr>
                <?php 
                    endwhile;
                else:
                ?>
                <tr>
                    <td colspan="3" class="text-center">Data kategori masih kosong.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
include '../templates/footer_admin.php';
?>