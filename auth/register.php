
<?php
session_start();

// Pesan untuk pengguna
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi = $_POST['konfirmasi_password'] ?? '';

    if (empty($nama) || empty($email) || empty($password) || empty($konfirmasi)) {
        $error = 'Semua kolom wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($password) < 8) {
        $error = 'Password minimal 8 karakter.';
    } elseif ($password !== $konfirmasi) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        // Penyimpanan akun akan dihubungkan ke database
        // setelah struktur database Readify disesuaikan.
        $success = 'Data berhasil divalidasi. Fitur penyimpanan akun belum dihubungkan.';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun | Readify</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #f5efff, #fff0f6);
            color: #302442;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .register-card {
            background: white;
            width: 100%;
            max-width: 430px;
            padding: 35px;
            border-radius: 18px;
            box-shadow: 0 10px 35px rgba(70, 40, 100, 0.12);
        }

        .brand {
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            color: #7138c8;
            margin-bottom: 10px;
        }

        .brand span {
            color: #ec4899;
        }

        h1 {
            text-align: center;
            font-size: 25px;
            margin-bottom: 10px;
        }

        .subtitle {
            text-align: center;
            color: #81788e;
            font-size: 14px;
            margin-bottom: 25px;
            line-height: 1.5;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border: 1px solid #ddd4e8;
            border-radius: 9px;
            font-size: 14px;
        }

        input:focus {
            outline: none;
            border-color: #7138c8;
        }

        button {
            width: 100%;
            padding: 13px;
            background: #7138c8;
            color: white;
            border: none;
            border-radius: 9px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #5b21b6;
        }

        .message {
            padding: 12px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 18px;
            line-height: 1.5;
        }

        .error {
            background: #fff0f0;
            color: #b42318;
        }

        .success {
            background: #ecfdf3;
            color: #027a48;
        }

        .login-link {
            text-align: center;
            margin-top: 22px;
            font-size: 14px;
            color: #81788e;
        }

        .login-link a {
            color: #7138c8;
            font-weight: bold;
            text-decoration: none;
        }

        @media (max-width: 480px) {
            .register-card {
                padding: 25px 20px;
            }
        }
    </style>
</head>

<body>
    <main class="register-card">
        <div class="brand">readify<span>.</span></div>

        <h1>Buat Akun Baru</h1>
        <p class="subtitle">
            Bergabunglah dengan Readify dan temukan cerita favoritmu!
        </p>

        <?php if ($error !== ''): ?>
            <div class="message error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success !== ''): ?>
            <div class="message success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="nama">Nama Lengkap</label>
            <input
                type="text"
                id="nama"
                name="nama"
                placeholder="Masukkan nama lengkap"
                value="<?= htmlspecialchars($nama ?? '') ?>"
                required
            >

            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                placeholder="nama@email.com"
                value="<?= htmlspecialchars($email ?? '') ?>"
                required
            >

            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="Minimal 8 karakter"
                minlength="8"
                required
            >

            <label for="konfirmasi_password">Konfirmasi Password</label>
            <input
                type="password"
                id="konfirmasi_password"
                name="konfirmasi_password"
                placeholder="Ulangi password"
                minlength="8"
                required
            >

            <button type="submit">Daftar Sekarang</button>
        </form>

        <p class="login-link">
            Sudah punya akun?
            <a href="login.php">Login di sini</a>
        </p>
    </main>
</body>
</html>
