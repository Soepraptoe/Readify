```php
<?php
session_start();

// Hapus seluruh data session pengguna
$_SESSION = [];

// Hapus cookie session jika digunakan
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        [
            'expires'  => time() - 42000,
            'path'     => $params['path'],
            'domain'   => $params['domain'],
            'secure'   => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => $params['samesite'] ?? 'Lax'
        ]
    );
}

// Hancurkan session
session_destroy();

// Arahkan pengguna ke halaman login Readify
header('Location: login.php');
exit;
```
