
<?php
require_once __DIR__ . '/../auth/admin_check.php';
require_once __DIR__ . '/../config/koneksi.php';

$idBuku = filter_input(INPUT_GET, 'book_id', FILTER_VALIDATE_INT);

if (!$idBuku || $idBuku < 1) {
    exit('ID buku tidak valid.');
}

$stmt = mysqli_prepare($conn, "SELECT judul FROM books WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $idBuku);
mysqli_stmt_execute($stmt);
$buku = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$buku) {
    exit('Buku tidak ditemukan.');
}

$stmtBab = mysqli_prepare(
    $conn,
    "SELECT id, judul_bab FROM chapters WHERE book_id = ? ORDER BY id ASC"
);
mysqli_stmt_bind_param($stmtBab, "i", $idBuku);
mysqli_stmt_execute($stmtBab);
$daftarBab = mysqli_stmt_get_result($stmtBab);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Bab - Readify</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

    <h1>Kelola Bab Buku</h1>
    <h2><?= htmlspecialchars($buku['judul'], ENT_QUOTES, 'UTF-8') ?></h2>

    <p>
        <a href="buku.php">Kembali ke daftar buku</a>
        |
        <a href="tambahBab.php?book_id=<?= $idBuku ?>">+ Tambah Bab</a>
    </p>

    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>No.</th>
                <th>Judul Bab</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            while ($bab = mysqli_fetch_assoc($daftarBab)):
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($bab['judul_bab'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <a href="../baca/bab.php?id=<?= (int)$bab['id'] ?>"
                           target="_blank">Lihat</a>
                    </td>
                </tr>
            <?php endwhile; ?>

            <?php if (mysqli_num_rows($daftarBab) === 0): ?>
                <tr>
                    <td colspan="3">Belum ada bab untuk buku ini.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>
