<?php 
// Include header publik BARU (layout-3 Stisla)
include 'config/database.php';
include 'templates/header_publik.php'; 

// Logika PHP untuk ambil data buku
$sql_buku = "SELECT tbl_buku.*, tbl_kategori.nama_kategori 
             FROM tbl_buku 
             LEFT JOIN tbl_kategori ON tbl_buku.id_kategori = tbl_kategori.id_kategori
             ORDER BY tbl_buku.judul_buku ASC";
$result_buku = mysqli_query($koneksi, $sql_buku);
?>

<div class="section-header">
    <h1>Katalog Buku</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item">Perpustakaan SMK Al-Asy'ari</div>
    </div>
</div>

<div class="section-body">
    <div class="row">
        <?php if(mysqli_num_rows($result_buku) > 0): ?>
            <?php while($buku = mysqli_fetch_assoc($result_buku)): ?>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card card-primary">
                        <div class="card-header">
                            <div class="card-header-action">
                                <a href="#" class="btn btn-primary">
                                    <?php echo htmlspecialchars($buku['nama_kategori'] ?? 'Umum'); ?>
                                </a>
                            </div>
                        </div>
                        
                        <?php 
                        $cover_path = "uploads/cover/" . htmlspecialchars($buku['cover_buku']);
                        if (empty($buku['cover_buku']) || !file_exists($cover_path)) {
                            $cover_path = "assets/img/news/img01.jpg"; // Placeholder Stisla
                        }
                        ?>
                        <div class="card-body p-0">
                           <img src="<?php echo $cover_path; ?>" class="card-img-top" alt="Cover <?php echo htmlspecialchars($buku['judul_buku']); ?>">
                        </div>

                        <div class="card-footer text-left" style="min-height: 150px;">
                            <h5 class="card-title"><?php echo htmlspecialchars($buku['judul_buku']); ?></h5>
                            <p class="text-muted" style="font-size: 13px;">
                                <?php echo htmlspecialchars($buku['pengarang']); ?> (<?php echo htmlspecialchars($buku['tahun_terbit']); ?>)
                            </p>
                            <p class="text-success font-weight-bold">
                                Stok: <?php echo htmlspecialchars($buku['jumlah_stok']); ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    Belum ada buku di katalog.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php 
// Include footer publik BARU
include 'templates/footer_publik.php'; 
?>