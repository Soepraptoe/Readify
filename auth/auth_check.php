<?php
// Memulai session jika belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Memeriksa status login pengguna
if (!isset($_SESSION['user_id'])) {
    // Arahkan ke halaman login Readify
    header('Location: /Readify/auth/login.php');
    exit;
}
