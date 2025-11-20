<?php
// Include header Stisla BARU
include '../templates/header_admin.php';

// (Logika PHP untuk mengambil data statistik bisa ditambahkan di sini)
// Contoh:
 $query_anggota = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tbl_anggota");
 $total_anggota = mysqli_fetch_assoc($query_anggota)['total'];
 $query_buku = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tbl_buku");
 $total_buku = mysqli_fetch_assoc($query_buku)['total'];
 $query_pinjam = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tbl_peminjaman WHERE status = 'Dipinjam'");
 $total_pinjam = mysqli_fetch_assoc($query_pinjam)['total'];
?>

<div class="section-header">
    <h1>Selamat Datang, <?php echo $admin_nama; ?></h1>
</div>

<div class="section-body">

    <div class="row">
        <div class="col-lg-4 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                    <i class="fas fa-book"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Total Judul Buku</h4>
                    </div>
                    <div class="card-body">
                        <?php 
                        echo $total_buku; ?>
                        
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                    <i class="fas fa-users"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Total Anggota</h4>
                    </div>
                    <div class="card-body">
                        <?php  echo $total_anggota; ?>
                        
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-warning">
                    <i class="fas fa-hand-holding"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Buku Dipinjam</h4>
                    </div>
                    <div class="card-body">
                        <?php  echo $total_pinjam; ?>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer Stisla BARU
include '../templates/footer_admin.php';
?>