
<?php
// Memeriksa apakah pengguna sudah login
require_once __DIR__ . '/auth_check.php';

// Memeriksa hak akses pengguna
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Jika bukan admin, tampilkan halaman akses ditolak
    http_response_code(403);
    exit('Akses ditolak! Halaman ini hanya untuk admin.');
}
