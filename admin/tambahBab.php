
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

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judulBab = trim($_POST['judul_bab'] ?? '');
    $isi = trim($_POST['isi'] ?? '');

    if ($judulBab === '' || $isi === '') {
        $error = 'Judul bab dan isi bab wajib diisi.';
    } else {
        $stmtTambah = mysqli_prepare(
            $conn,
            "INSERT INTO chapters (book_id, judul_bab, isi)
             VALUES (?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmtTambah,
            "iss",
            $idBuku,
            $judulBab,
            $isi
        );

        if (mysqli_stmt_execute($stmtTambah)) {
            header("Location: bab.php?book_id=" . $idBuku);
            exit;
        } else {
            $error = 'Bab gagal disimpan. Silakan coba lagi.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Bab - Readify</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

    <h1>Tambah Bab Buku</h1>
    <h2><?= htmlspecialchars($buku['judul'], ENT_QUOTES, 'UTF-8') ?></h2>

    <?php if ($error !== ''): ?>
        <p style="color: red;">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <form method="POST"
          action="tambahBab.php?book_id=<?= $idBuku ?>">

        <p>
            <label for="judul_bab">Judul Bab</label><br>
            <input type="text" id="judul_bab" name="judul_bab"
                   value="<?= htmlspecialchars($_POST['judul_bab'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                   required>
        </p>

        <p>
            <label for="isi">Isi Bab</label><br>
            <textarea id="isi" name="isi" rows="15" required><?= htmlspecialchars($_POST['isi'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </p>

        <button type="submit">Simpan Bab</button>
        <a href="bab.php?book_id=<?= $idBuku ?>">Batal</a>

    </form>

</body>
</html>
