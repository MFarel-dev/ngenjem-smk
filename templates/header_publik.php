<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Jem-Ngenjem Buku - Perpus SMK Al-Asy'ari</title>

  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css"> <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/components.css">
  
  <style>
      .card-img-top {
          width: 100%;
          height: 300px; /* Seragamkan tinggi cover */
          object-fit: cover; /* Biar gambar tidak gepeng */
      }
  </style>
</head>

<body class="layout-3">
  <div id="app">
    <div class="main-wrapper container">
      <div class="navbar-bg"></div>
      <nav class="navbar navbar-expand-lg main-navbar">
        <img src="assets/img/logo.png" alt="logo" width="60">
        <a href="index.php" class="navbar-brand sidebar-gone-hide">Jem-Ngenjem Buku</a>
        <a href="index.php" class="navbar-brand sidebar-gone-show">Ngenjem</a>
        <div class="navbar-nav ml-auto">
          </div>
      </nav>

      <nav class="navbar navbar-secondary navbar-expand-lg">
        <div class="container">
          <ul class="navbar-nav">
            <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
              <a href="index.php" class="nav-link"><i class="fas fa-book-open"></i><span>Katalog Buku</span></a>
            </li>
            <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'login.php') ? 'active' : ''; ?>">
              <a href="login.php" class="nav-link"><i class="fas fa-sign-in-alt"></i><span>Login Admin</span></a>
            </li>
          </ul>
        </div>
      </nav>

      <main class="main-content">
        <section class="section">
          