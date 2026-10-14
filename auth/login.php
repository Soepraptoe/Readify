```php
<?php
session_start();

// Koneksi database Readify
require_once __DIR__ . '/../config/koneksi.php';

$error = '';

// Jika pengguna sudah login, arahkan ke halaman utama
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

// Proses login ketika form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Email dan password wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        $stmt = $conn->prepare(
            'SELECT id, nama, email, password, role
             FROM users
             WHERE email = ?
             LIMIT 1'
        );

        if ($stmt) {
            $stmt->bind_param('s', $email);

            if ($stmt->execute()) {
                $stmt->bind_result(
                    $id,
                    $nama,
                    $userEmail,
                    $hashedPassword,
                    $role
                );

                if (
                    $stmt->fetch() &&
                    password_verify($password, $hashedPassword)
                ) {
                    $stmt->close();

                    // Buat ID session baru setelah login berhasil
                    session_regenerate_id(true);

                    // Simpan informasi pengguna
                    $_SESSION['user_id'] = $id;
                    $_SESSION['nama'] = $nama;
                    $_SESSION['email'] = $userEmail;
                    $_SESSION['role'] = $role;

                    // Arahkan ke halaman utama Readify
                    header('Location: ../index.php');
                    exit;
                } else {
                    $error = 'Email atau password salah.';
                }
            } else {
                $error = 'Terjadi kesalahan. Silakan coba lagi.';
            }

            $stmt->close();
        } else {
            $error = 'Terjadi kesalahan. Silakan coba lagi.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | Readify</title>

    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
    <main class="auth-container">
        <section class="auth-card">

            <a href="../index.php" class="brand">
                readify<span>.</span>
            </a>

            <h1>Selamat Datang!</h1>

            <p class="subtitle">
                Masuk ke akunmu dan lanjutkan petualangan membaca.
            </p>

            <?php if (isset($_GET['registered'])): ?>
                <div class="alert success">
                    Akun berhasil dibuat. Silakan login.
                </div>
            <?php endif; ?>

            <?php if ($error !== ''): ?>
                <div class="alert error">
                    <?= htmlspecialchars(
                        $error,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <label for="email">Email</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Masukkan email"
                    autocomplete="email"
                    value="<?= htmlspecialchars(
                        $_POST['email'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    required
                >

                <label for="password">Password</label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Masukkan password"
                    autocomplete="current-password"
                    required
                >

                <button type="submit" class="auth-button">
                    Masuk
                </button>
            </form>

            <p class="auth-footer">
                Belum punya akun?
                <a href="register.php">Daftar sekarang</a>
            </p>

        </section>
    </main>
</body>
</html>
```