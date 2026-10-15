
<?php
require_once __DIR__ . '/config/koneksi.php';

$nama = 'Admin Readify';
$email = 'admin@readify.local';
$password = 'AdminReadify123!';
$role = 'admin';

// Periksa apakah email sudah digunakan
$stmt = $conn->prepare(
    'SELECT id FROM users WHERE email = ? LIMIT 1'
);
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    exit('Email admin sudah terdaftar. Tidak ada akun baru yang dibuat.');
}
$stmt->close();

// Hash password sebelum disimpan
$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    'INSERT INTO users (nama, email, password, role)
     VALUES (?, ?, ?, ?)'
);
$stmt->bind_param('ssss', $nama, $email, $hash, $role);

if ($stmt->execute()) {
    echo 'Akun admin berhasil dibuat.<br>';
    echo 'Email: ' . htmlspecialchars($email) . '<br>';
    echo 'Password: ' . htmlspecialchars($password) . '<br>';
    echo 'Segera hapus file buat_admin.php setelah selesai.';
} else {
    echo 'Gagal membuat akun admin. Periksa struktur tabel users.';
}

$stmt->close();
$conn->close();
?>
