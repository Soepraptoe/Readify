
<?php
require_once __DIR__ . '/config/koneksi.php';

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

$genre = trim($_GET['genre'] ?? '');
$cari = trim($_GET['cari'] ?? '');

$kategoriQuery = mysqli_query(
    $conn,
    "SELECT DISTINCT genre FROM books
     WHERE genre IS NOT NULL AND genre <> ''
     ORDER BY genre ASC"
);

$sql = "SELECT * FROM books WHERE 1=1";
$types = "";
$params = [];

if ($genre !== '') {
    $sql .= " AND genre = ?";
    $types .= "s";
    $params[] = $genre;
}

if ($cari !== '') {
    $sql .= " AND (judul LIKE ? OR penulis LIKE ?)";
    $types .= "ss";
    $keyword = "%" . $cari . "%";
    $params[] = $keyword;
    $params[] = $keyword;
}

$sql .= " ORDER BY id DESC";

$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    error_log("Readify query error: " . mysqli_error($conn));
    http_response_code(500);
    exit("Terjadi kendala saat memuat katalog. Silakan periksa konfigurasi database.");
}

if ($types !== '') {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$hasilBuku = mysqli_stmt_get_result($stmt);

if (!$hasilBuku) {
    error_log("Readify result error: " . mysqli_error($conn));
    http_response_code(500);
    exit("Data katalog belum dapat dimuat. Silakan periksa database.");
}

$jumlahBuku = mysqli_num_rows($hasilBuku);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Readify — Temukan Ceritamu</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .readify-search {
            display:flex; flex-wrap:wrap; gap:10px;
            margin:22px 0; align-items:center;
        }
        .readify-search input {
            flex:1; min-width:180px; padding:12px 14px;
            border:1px solid #d8ded7; border-radius:10px;
            font:inherit;
        }
        .readify-search button {
            padding:12px 18px; border:0; border-radius:10px;
            background:#52796f; color:white; cursor:pointer;
            font:inherit;
        }
        .book-cover {
            width:100%; height:250px; object-fit:cover;
            display:block; background:#e8eee7;
        }
        .cover-placeholder {
            height:250px; display:flex; align-items:center;
            justify-content:center; background:#e8eee7;
            color:#354f52; font-size:56px;
        }
        .book-info h3 { margin:12px 0 6px; }
        .book-info p { line-height:1.6; }
        .empty-state {
            padding:30px; text-align:center; background:#fff;
            border-radius:14px; color:#52645b;
        }
        .hero-text p { line-height:1.8; }
        .readify-nav-links { display:flex; gap:20px; align-items:center; }
        .readify-nav-links a { text-decoration:none; }
        @media(max-width:600px) {
            .readify-nav-links { gap:10px; flex-wrap:wrap; }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <a href="index.php" class="logo">Readify<span>.</span></a>
    <div class="readify-nav-links">
        <a href="index.php">Beranda</a>
        <a href="#koleksi">Koleksi Buku</a>
        <a href="#kategori">Kategori</a>
    </div>
</nav>

<header class="hero">
    <div class="hero-text">
        <p class="tagline">Satu buku, banyak kemungkinan</p>
        <h1>Read. Explore. Enjoy.</h1>
        <p>
            Temukan cerita baru, jelajahi dunia melalui kata-kata,
            dan nikmati pengalaman membaca digital bersama Readify.
        </p>
        <a href="#koleksi" class="read-button">Jelajahi Buku</a>
    </div>
    <div class="hero-image">
        <div class="floating-note">
            <span>📚</span>
            <p>Temukan cerita favoritmu di sini.</p>
        </div>
    </div>
</header>

<section id="kategori" class="collection">
    <div class="section-heading">
        <p class="tagline">Pilih sesuai minatmu</p>
        <h2>Kategori Buku</h2>
    </div>

    <div class="category-list">
        <a class="category" href="index.php#koleksi">Semua</a>
        <?php if ($kategoriQuery): ?>
            <?php while ($kat = mysqli_fetch_assoc($kategoriQuery)): ?>
                <a class="category"
                   href="index.php?genre=<?= urlencode($kat['genre']) ?>#koleksi">
                    <?= e($kat['genre']) ?>
                </a>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</section>

<section id="koleksi" class="collection">
    <div class="section-heading">
        <p class="tagline">Baca sesuatu yang baru</p>
        <h2><?= $genre !== '' ? 'Genre ' . e($genre) : 'Koleksi Buku' ?></h2>
        <p>Jelajahi koleksi bacaan yang tersedia di Readify.</p>
    </div>

    <form class="readify-search" method="get" action="index.php#koleksi">
        <?php if ($genre !== ''): ?>
            <input type="hidden" name="genre" value="<?= e($genre) ?>">
        <?php endif; ?>
        <input
            type="search"
            name="cari"
            placeholder="Cari judul buku atau nama penulis..."
            value="<?= e($cari) ?>"
        >
        <button type="submit">Cari Buku</button>
    </form>

    <p><?= $jumlahBuku ?> buku ditemukan.</p>

    <div class="book-grid">
        <?php if ($jumlahBuku > 0): ?>
            <?php while ($buku = mysqli_fetch_assoc($hasilBuku)): ?>
                <article class="book-card">
                    <?php if (!empty($buku['cover'])): ?>
                        <img class="book-cover"
                             src="<?= e($buku['cover']) ?>"
                             alt="Sampul <?= e($buku['judul']) ?>"
                             loading="lazy">
                    <?php else: ?>
                        <div class="cover-placeholder" aria-label="Sampul belum tersedia">
                            📖
                        </div>
                    <?php endif; ?>

                    <div class="book-info">
                        <span class="category"><?= e($buku['genre'] ?: 'Umum') ?></span>
                        <h3><?= e($buku['judul']) ?></h3>
                        <p><?= e($buku['penulis']) ?></p>
                        <a class="read-button"
                           href="baca/detail.php?id=<?= (int)$buku['id'] ?>">
                            Lihat Detail
                        </a>
                    </div>
                </article>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <h3>Belum ada buku yang cocok 📚</h3>
                <p>Coba kata kunci lain atau pilih kategori Semua.</p>
                <a class="read-button" href="index.php#koleksi">Lihat Semua Buku</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<footer>
    <p>&copy; <?= date('Y') ?> Readify. Read. Explore. Enjoy.</p>
</footer>

</body>
</html>
