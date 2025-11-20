<?php 
include '../templates/header_admin.php'; 

// Logika PHP untuk mengambil data buku dari database
$sql_buku = "SELECT tbl_buku.*, tbl_kategori.nama_kategori 
             FROM tbl_buku 
             LEFT JOIN tbl_kategori ON tbl_buku.id_kategori = tbl_kategori.id_kategori
             ORDER BY tbl_buku.judul_buku ASC";
$result_buku = mysqli_query($koneksi, $sql_buku);
?>

<div class="section-header">
    <h1>Manajemen Buku</h1>
    <div class="section-header-breadcrumb">
        <a href="buku_tambah.php" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Buku</a>
    </div>
</div>

<div class="section-body">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Data Buku Perpustakaan</h4>
                </div>
                <div class="card-body">
                    
                    <?php if (isset($_SESSION['pesan_sukses'])): ?>
                    <div class="alert alert-success alert-dismissible show fade">
                        <div class="alert-body">
                            <button class="close" data-dismiss="alert"><span>&times;</span></button>
                            <?php 
                            echo $_SESSION['pesan_sukses']; 
                            unset($_SESSION['pesan_sukses']);
                            ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-md">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th>Pengarang</th>
                                <th>Tahun</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($result_buku) > 0): ?>
                                <?php $no = 1; ?>
                                <?php while($buku = mysqli_fetch_assoc($result_buku)): ?>
                               <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($buku['judul_buku']); ?></td> <?php // <-- UBAH INI JUGA ?>
                                <td><?php echo htmlspecialchars($buku['nama_kategori'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($buku['pengarang']); ?></td>
                                    <td><?php echo htmlspecialchars($buku['tahun_terbit']); ?></td>
                                    <td><?php echo htmlspecialchars($buku['jumlah_stok']); ?></td>
                                    <td>
                                        <a href="buku_edit.php?id=<?php echo $buku['id_buku']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                                        <a href="buku_hapus.php?id=<?php echo $buku['id_buku']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus buku ini?');"><i class="fas fa-trash"></i> Hapus</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">Data buku masih kosong.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
include '../templates/footer_admin.php'; 
?>