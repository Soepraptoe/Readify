```php
<?php
// Periksa apakah pengguna sudah login
require_once __DIR__ . '/auth_check.php';

// Periksa hak akses admin
if (
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    // Tolak akses untuk pengguna yang bukan admin
    http_response_code(403);

    exit('Akses ditolak! Halaman ini hanya untuk admin.');
}
```