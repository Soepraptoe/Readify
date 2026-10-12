<?php
require_once "../config/koneksi.php";

// Menghitung jumlah buku
$queryBuku = mysqli_query($conn, "SELECT COUNT(*) AS total FROM books");
$dataBuku = mysqli_fetch_assoc($queryBuku);
$totalBuku = $dataBuku["total"];

// Menghitung jumlah genre berbeda
$queryGenre = mysqli_query(
    $conn,
    "SELECT COUNT(DISTINCT genre) AS total
     FROM books
     WHERE genre IS NOT NULL AND genre != ''"
);
$dataGenre = mysqli_fetch_assoc($queryGenre);
$totalGenre = $dataGenre["total"];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Readify</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6fb;
            color: #263247;
        }

        header {
            background: #243b64;
            color: white;
            padding: 22px 30px;
        }

        header h1 {
            margin: 0;
        }

        header p {
            margin-bottom: 0;
            color: #dce6f7;
        }

        main {
            max-width: 1000px;
            margin: 35px auto;
            padding: 0 20px;
        }

        .ringkasan {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin: 25px 0;
        }

        .kartu {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.06);
        }

        .kartu p {
            color: #64748b;
            margin-bottom: 8px;
        }

        .angka {
            font-size: 32px;
            font-weight: bold;
            color: #243b64;
        }

        .aksi {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 25px;
        }

        .tombol {
            display: inline-block;
            padding: 12px 18px;
            background: #243b64;
            color: white;
            text-decoration: none;
            border-radius: 7px;
        }

        .tombol:hover {
            background: #36558a;
        }

        .tombol-sekunder {
            background: #16805d;
        }

        .tombol-sekunder:hover {
            background: #116849;
        }

        @media (max-width: 500px) {
            header {
                padding: 20px;
            }

            main {
                margin-top: 20px;
            }
        }
    </style>
</head>
<body>

    <header>
        <h1>Readify Admin</h1>
        <p>Dashboard pengelolaan buku</p>
    </header>

    <main>
        <h2>Selamat Datang, Admin!</h2>
        <p>Berikut ringkasan data buku yang tersimpan di Readify.</p>

        <section class="ringkasan">
            <div class="kartu">
                <p>Total Buku</p>
                <div class="angka">
                    <?= $totalBuku; ?>
                </div>
            </div>

            <div class="kartu">
                <p>Total Genre</p>
                <div class="angka">
                    <?= $totalGenre; ?>
                </div>
            </div>
        </section>

        <h2>Menu Pengelolaan</h2>

        <div class="aksi">
            <a class="tombol" href="buku.php">
                Kelola Buku
            </a>

            <a class="tombol tombol-sekunder" href="tambahBuku.php">
                + Tambah Buku
            </a>
        </div>
    </main>

</body>
</html>