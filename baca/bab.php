
<?php
require_once __DIR__ . '/../config/koneksi.php';

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

$idBab = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$idBab || $idBab < 1) {
    http_response_code(400);
    exit('ID bab tidak valid. <a href="../index.php">Kembali ke katalog</a>');
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT chapters.id AS chapter_id,
            chapters.judul_bab,
            chapters.isi,
            chapters.book_id,
            books.judul AS judul_buku
     FROM chapters
     INNER JOIN books ON books.id = chapters.book_id
     WHERE chapters.id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $idBab);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$bab = mysqli_fetch_assoc($result);

if (!$bab) {
    http_response_code(404);
    exit('Bab tidak ditemukan. <a href="../index.php">Kembali ke katalog</a>');
}

$idBuku = (int)$bab['book_id'];

// Navigasi mengikuti urutan ID bab dalam buku yang sama.
$stmtNav = mysqli_prepare(
    $conn,
    "SELECT id, judul_bab FROM chapters
     WHERE book_id = ? ORDER BY id ASC"
);
mysqli_stmt_bind_param($stmtNav, "i", $idBuku);
mysqli_stmt_execute($stmtNav);
$hasilNavigasi = mysqli_stmt_get_result($stmtNav);

$babSebelumnya = null;
$babBerikutnya = null;
$babKe = 0;
$totalBab = 0;
$daftarNavigasi = [];

while ($item = mysqli_fetch_assoc($hasilNavigasi)) {
    $daftarNavigasi[] = $item;
}

$totalBab = count($daftarNavigasi);

foreach ($daftarNavigasi as $index => $item) {
    if ((int)$item['id'] === $idBab) {
        $babKe = $index + 1;
        $babSebelumnya = $index > 0 ? $daftarNavigasi[$index - 1] : null;
        $babBerikutnya = $index < $totalBab - 1
            ? $daftarNavigasi[$index + 1]
            : null;
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($bab['judul_bab']) ?> — Readify</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/pembaca.css">
</head>
<body>

<nav class="navbar">
    <a href="../index.php" class="logo">Readify<span>.</span></a>
    <div class="readify-nav-links">
        <a href="../index.php">Beranda</a>
        <a href="detail.php?id=<?= $idBuku ?>">Detail Buku</a>
    </div>
</nav>

<main class="reader-container">
    <a class="back-link" href="detail.php?id=<?= $idBuku ?>">
        ← Kembali ke detail buku
    </a>

    <article class="reading-page">
        <p class="reader-tag"><?= e($bab['judul_buku']) ?></p>
        <h1><?= e($bab['judul_bab']) ?></h1>
        <p class="chapter-progress">
            Bab <?= $babKe ?> dari <?= $totalBab ?>
        </p>

        <div class="reading-content"><?=
            nl2br(e($bab['isi']))
        ?></div>

        <nav class="chapter-navigation" aria-label="Navigasi bab">
            <?php if ($babSebelumnya): ?>
                <a class="chapter-nav-button"
                   href="bab.php?id=<?= (int)$babSebelumnya['id'] ?>">
                    ← Bab Sebelumnya
                </a>
            <?php else: ?>
                <span></span>
            <?php endif; ?>

            <?php if ($babBerikutnya): ?>
                <a class="chapter-nav-button"
                   href="bab.php?id=<?= (int)$babBerikutnya['id'] ?>">
                    Bab Berikutnya →
                </a>
            <?php else: ?>
                <a class="chapter-nav-button"
                   href="detail.php?id=<?= $idBuku ?>">
                    Selesai Membaca
                </a>
            <?php endif; ?>
        </nav>
    </article>
</main>

<footer>
    <p>&copy; <?= date('Y') ?> Readify. Read. Explore. Enjoy.</p>
</footer>

</body>
</html>
