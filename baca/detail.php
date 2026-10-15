
<?php
require_once __DIR__ . '/../config/koneksi.php';

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

$idBuku = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$idBuku || $idBuku < 1) {
    http_response_code(400);
    exit('ID buku tidak valid. <a href="../index.php">Kembali ke katalog</a>');
}

$stmt = mysqli_prepare($conn, "SELECT * FROM books WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $idBuku);
mysqli_stmt_execute($stmt);
$resultBuku = mysqli_stmt_get_result($stmt);
$buku = mysqli_fetch_assoc($resultBuku);

if (!$buku) {
    http_response_code(404);
    exit('Buku tidak ditemukan. <a href="../index.php">Kembali ke katalog</a>');
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
    <title><?= e($buku['judul']) ?> — Readify</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/pembaca.css">
</head>
<body>

<nav class="navbar">
    <a href="../index.php" class="logo">Readify<span>.</span></a>
    <div class="readify-nav-links">
        <a href="../index.php">Beranda</a>
        <a href="../index.php#koleksi">Koleksi Buku</a>
    </div>
</nav>

<main class="reader-container">
    <a class="back-link" href="../index.php#koleksi">
        ← Kembali ke katalog
    </a>

    <section class="detail-book">
        <div class="detail-cover">
            <?php if (!empty($buku['cover'])): ?>
                <img src="<?= e($buku['cover']) ?>"
                     alt="Sampul <?= e($buku['judul']) ?>">
            <?php else: ?>
                <div class="detail-cover-placeholder">📖</div>
            <?php endif; ?>
        </div>

        <div class="detail-info">
            <span class="reader-tag"><?= e($buku['genre'] ?: 'Umum') ?></span>
            <h1><?= e($buku['judul']) ?></h1>
            <p class="book-author">Ditulis oleh <?= e($buku['penulis']) ?></p>

            <h2>Sinopsis</h2>
            <p class="book-synopsis">
                <?= nl2br(e($buku['sinopsis'] ?: 'Sinopsis belum tersedia.')) ?>
            </p>

            <?php if (mysqli_num_rows($daftarBab) > 0): ?>
                <?php
                $babPertama = mysqli_fetch_assoc($daftarBab);
                ?>
                <a class="read-button"
                   href="bab.php?id=<?= (int)$babPertama['id'] ?>">
                    Mulai Membaca
                </a>
            <?php else: ?>
                <p class="notice">Bab buku ini belum tersedia.</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="chapter-section">
        <h2>Daftar Bab</h2>
        <p>Pilih bab untuk mulai membaca.</p>

        <?php
        // Jalankan ulang query agar seluruh daftar bab tetap tersedia.
        mysqli_stmt_execute($stmtBab);
        $daftarBab = mysqli_stmt_get_result($stmtBab);
        ?>

        <?php if (mysqli_num_rows($daftarBab) > 0): ?>
            <div class="chapter-list">
                <?php while ($bab = mysqli_fetch_assoc($daftarBab)): ?>
                    <a class="chapter-item"
                       href="bab.php?id=<?= (int)$bab['id'] ?>">
                        <span>📄 <?= e($bab['judul_bab']) ?></span>
                        <span> Baca → </span>
                    </a>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="reader-empty">
                Belum ada bab untuk buku ini.
            </div>
        <?php endif; ?>
    </section>
</main>

<footer>
    <p>&copy; <?= date('Y') ?> Readify. Read. Explore. Enjoy.</p>
</footer>

</body>
</html>
