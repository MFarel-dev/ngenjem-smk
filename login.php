<?php
// Hubungkan ke DB (untuk memulai session)
include 'config/database.php';

// Jika sudah login, lempar ke dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: admin/index.php');
    exit;
}

$error_msg = '';

// Proses login jika ada POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error_msg = 'Username dan Password tidak boleh kosong!';
    } else {
        // Ambil data admin berdasarkan username
        $sql = "SELECT id_admin, username, password, nama_lengkap FROM tbl_admin WHERE username = ?";
        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($admin = mysqli_fetch_assoc($result)) {
            // User ditemukan, verifikasi password
            if (password_verify($password, $admin['password'])) {
                // Password cocok! Buat session
                $_SESSION['admin_id'] = $admin['id_admin'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_nama'] = $admin['nama_lengkap'];
                
                // Redirect ke dashboard admin
                header('Location: admin/index.php');
                exit;
            } else {
                // Password salah
                $error_msg = 'Username atau Password salah!';
            }
        } else {
            // User tidak ditemukan
            $error_msg = 'Username atau Password salah!';
        }
        mysqli_stmt_close($stmt);
    }
}
mysqli_close($koneksi);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Login Admin - Jem-Ngenjem Buku</title>

  <link rel="stylesheet" href="assets/css/bootstrap.min.css"> <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css"> <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/components.css">
</head>

<body>
  <div id="app">
    <section class="section">
      <div class="container mt-5">
        <div class="row">
          <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
            <div class="login-brand">
              <img src="assets/img/logo.png" alt="logo" width="100">
            </div>

            <div class="card card-primary">
              <div class="card-header"><h4>Login Admin</h4></div>

              <div class="card-body">

                <?php if (!empty($error_msg)): ?>
                    <div class="alert alert-danger"><?php echo $error_msg; ?></div>
                <?php endif; ?>
                <form method="POST" action="login.php" class="needs-validation" novalidate="">
                  <div class="form-group">
                    <label for="username">Username</label>
                    <input id="username" type="text" class="form-control" name="username" tabindex="1" required autofocus>
                    <div class="invalid-feedback">
                      Mohon isi username Anda
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="d-block">
                    	<label for="password" class="control-label">Password</label>
                    </div>
                    <input id="password" type="password" class="form-control" name="password" tabindex="2" required>
                    <div class="invalid-feedback">
                      Mohon isi password Anda
                    </div>
                  </div>

                  <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                      Login
                    </button>
                  </div>
                </form>
                
              </div>
            </div>
            <div class="mt-5 text-muted text-center">
              Bukan admin? <a href="index.php">Kembali ke katalog</a>
            </div>
            <div class="simple-footer">
              Copyright &copy; <?php echo date('Y'); ?> Perpustakaan SMK Al-Asy'ari
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/popper.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>
  <script src="assets/js/jquery.nicescroll.min.js"></script>
  <script src="assets/js/stisla.js"></script>
  <script src="assets/js/scripts.js"></script>
  <script src="assets/js/custom.js"></script>

</body>
</html>