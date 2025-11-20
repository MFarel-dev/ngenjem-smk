<?php
// File ini akan di-include di setiap halaman admin
// Kita panggil database.php, yang otomatis juga memanggil session_start()
include '../config/database.php';

// CEK SESSION
// Jika tidak ada session 'admin_id', artinya belum login
if (!isset($_SESSION['admin_id'])) {
    // Lempar kembali ke halaman login
    header('Location: ../login.php');
    exit; // Pastikan skrip berhenti
}

// Ambil nama admin dari session untuk sapaan
$admin_nama = htmlspecialchars($_SESSION['admin_nama']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Admin Dashboard - Ngenjem</title>

  <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/components.css">
  
  </head>

<body>
  <div id="app">
    <div class="main-wrapper">
      <div class="navbar-bg"></div>
      <nav class="navbar navbar-expand-lg main-navbar">
        <form class="form-inline mr-auto">
          <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
          </ul>
        </form>
        <ul class="navbar-nav navbar-right">
          <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
            <img alt="image" src="../assets/img/avatar/avatar-1.png" class="rounded-circle mr-1">
            <div class="d-sm-none d-lg-inline-block">Hi, <?php echo $admin_nama; ?></div></a>
            <div class="dropdown-menu dropdown-menu-right">
              <div class="dropdown-title">Logged in</div>
              
              <div class="dropdown-divider"></div>
              <a href="../logout.php" class="dropdown-item has-icon text-danger">
                <i class="fas fa-sign-out-alt"></i> Logout
              </a>
            </div>
          </li>
        </ul>
      </nav>
      
      <div class="main-sidebar">
        <aside id="sidebar-wrapper">
          <div class="sidebar-brand">
            <a href="index.php">Ngenjem Perpus</a>
          </div>
          <div class="sidebar-brand sidebar-brand-sm">
            <img src="../assets/img/logo.png" alt="logo" width="40">

          </div>
          <ul class="sidebar-menu">
              <li class="menu-header">Dashboard</li>
              <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
                  <a class="nav-link" href="index.php"><i class="fas fa-fire"></i> <span>Dashboard</span></a>
              </li>

              <li class="menu-header">Manajemen</li>
              <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'buku.php') ? 'active' : ''; ?>">
                  <a class="nav-link" href="buku.php"><i class="fas fa-book"></i> <span>Manajemen Buku</span></a>
              </li>
              <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'anggota.php') ? 'active' : ''; ?>">
                  <a class="nav-link" href="anggota.php"><i class="fas fa-users"></i> <span>Manajemen Anggota</span></a>
              </li>
              <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'kategori.php') ? 'active' : ''; ?>">
                  <a class="nav-link" href="kategori.php"><i class="fas fa-tags"></i> <span>Manajemen Kategori</span></a>
              </li>

              <li class="menu-header">Transaksi</li>
              <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'pinjam.php') ? 'active' : ''; ?>">
                  <a class="nav-link" href="pinjam.php"><i class="fas fa-hand-holding"></i> <span>Peminjaman</span></a>
              </li>
              <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'kembali.php') ? 'active' : ''; ?>">
                  <a class="nav-link" href="kembali.php"><i class="fas fa-undo"></i> <span>Pengembalian</span></a>
              </li>
              <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'laporan.php') ? 'active' : ''; ?>">
                  <a class="nav-link" href="laporan.php"><i class="fas fa-file-alt"></i> <span>Laporan</span></a>
              </li>
            </ul>

            <div class="mt-4 mb-4 p-3 hide-sidebar-mini">
              <a href="../logout.php" class="btn btn-primary btn-lg btn-block btn-icon-split">
                <i class="fas fa-sign-out-alt"></i> Logout
              </a>
            </div>
        </aside>
      </div>

      <?php // INI BAGIAN PENTING YANG DIPERBAIKI ?>
      <div class="main-content">
        <section class="section">
        <?php // JANGAN DITUTUP TAGNYA DI SINI ?>