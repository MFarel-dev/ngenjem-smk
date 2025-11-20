<?php
// File ini harus ada di root folder (sejajar dengan index.php dan login.php)

// Mulai session untuk mengaksesnya
session_start();

// Hapus semua variabel session
$_SESSION = array();

// Hancurkan session
session_destroy();

// Redirect ke halaman login
header("location: login.php");
exit;
?>