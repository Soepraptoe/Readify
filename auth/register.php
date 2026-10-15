```php
<?php
session_start();

// Jika pengguna sudah login, arahkan ke halaman utama
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

// Koneksi database Readify
require_once __DIR__ . '/../config/koneksi.php';

$error = '';
$nama = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi = $_POST['konfirmasi_password'] ?? '';

    // Validasi input
    if (
        $nama === '' ||
        $email === '' ||
        $password === '' ||
        $konfirmasi === ''
    ) {
        $error = 'Semua kolom wajib diisi.';
    } elseif (strlen($nama) > 100) {
        $error = 'Nama maksimal 100 karakter.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($email) > 150) {
        $error = 'Email maksimal 150 karakter.';
    } elseif (strlen($password) < 8) {
        $error = 'Password minimal 8 karakter.';
    } elseif (strlen($password) > 4096) {
        $error = 'Password terlalu panjang.';
    } elseif ($password !== $konfirmasi) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        // Periksa apakah email sudah terdaftar
        $stmt = $conn->prepare(
            'SELECT id FROM users WHERE email = ? LIMIT 1'
        );

        if (!$stmt) {
            $error = 'Terjadi kesalahan. Silakan coba lagi.';
        } else {
            $stmt->bind_param('s', $email);

            if (!$stmt->execute()) {
                $error = 'Terjadi kesalahan. Silakan coba lagi.';
                $stmt->close();
            } else {
                $stmt->store_result();
                $emailAda = $stmt->num_rows > 0;
                $stmt->close();

                if ($emailAda) {
                    $error = 'Email sudah terdaftar. Gunakan email lain.';
                } else {
                    // Hash password sebelum disimpan
                    $passwordHash = password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    );

                    // Akun baru selalu memiliki role user
                    $role = 'user';

                    $stmt = $conn->prepare(
                        'INSERT INTO users (nama, email, password, role)
                         VALUES (?, ?, ?, ?)'
                    );

                    if (!$stmt) {
                        $error = 'Terjadi kesalahan saat membuat akun.';
                    } else {
                        $stmt->bind_param(
                            'ssss',
                            $nama,
                            $email,
                            $passwordHash,
                            $role
                        );

                        if ($stmt->execute()) {
                            $stmt->close();

                            header(
                                'Location: login.php?registered=1'
                            );
                            exit;
                        }

                        // Tangani kemungkinan email duplikat
                        if ($stmt->errno === 1062) {
                            $error = 'Email sudah terdaftar. Gunakan email lain.';
                        } else {
                            $error = 'Registrasi gagal. Silakan coba lagi.';
                        }

                        $stmt->close();
                    }
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Daftar Akun | Readify</title>

    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
    <main class="auth-container">
        <section class="auth-card">

            <a href="../index.php" class="brand">
                readify<span>.</span>
            </a>

            <h1>Buat Akun Baru</h1>

            <p class="subtitle">
                Bergabunglah dengan Readify dan temukan cerita favoritmu!
            </p>

            <?php if ($error !== ''): ?>
                <div class="alert error" role="alert">
                    <?= htmlspecialchars(
                        $error,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <label for="nama">Nama Lengkap</label>
                <input
                    type="text"
                    id="nama"
                    name="nama"
                    placeholder="Masukkan nama lengkap"
                    value="<?= htmlspecialchars(
                        $nama,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    maxlength="100"
                    autocomplete="name"
                    required
                >

                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="nama@email.com"
                    value="<?= htmlspecialchars(
                        $email,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    maxlength="150"
                    autocomplete="email"
                    required
                >

                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Minimal 8 karakter"
                    minlength="8"
                    maxlength="4096"
                    autocomplete="new-password"
                    required
                >

                <label for="konfirmasi_password">
                    Konfirmasi Password
                </label>
                <input
                    type="password"
                    id="konfirmasi_password"
                    name="konfirmasi_password"
                    placeholder="Ulangi password"
                    minlength="8"
                    maxlength="4096"
                    autocomplete="new-password"
                    required
                >

                <button type="submit" class="auth-button">
                    Daftar Sekarang
                </button>
            </form>

            <p class="auth-footer">
                Sudah punya akun?
                <a href="login.php">Login di sini</a>
            </p>

        </section>
    </main>
</body>
</html>
```