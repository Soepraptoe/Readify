```php
<?php
// Memulai session jika belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Memeriksa status login pengguna
if (!isset($_SESSION['user_id'])) {
    // Arahkan pengguna yang belum login ke halaman login
    header('Location: login.php');
    exit;
}
```