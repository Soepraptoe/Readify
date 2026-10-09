<?php
require_once "../config/koneksi.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $judul = trim($_POST["judul"] ?? "");
    $penulis = trim($_POST["penulis"] ?? "");
    $genre = trim($_POST["genre"] ?? "");
    $sinopsis = trim($_POST["sinopsis"] ?? "");

    if ($judul === "" || $penulis === "") {
        $error = "Judul dan penulis wajib diisi!";
    } else {
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO books (judul, penulis, genre, sinopsis)
             VALUES (?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssss",
            $judul,
            $penulis,
            $genre,
            $sinopsis
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: buku.php");
            exit;
        } else {
            $error = "Buku gagal ditambahkan.";
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Buku - Readify</title>
</head>
<body>

    <h1>Tambah Buku</h1>

    <?php if ($error !== "") { ?>
        <p style="color: red;">
            <?= htmlspecialchars($error); ?>
        </p>
    <?php } ?>

    <form method="POST">
        <label>Judul Buku:</label><br>
        <input type="text" name="judul" required>
        <br><br>

        <label>Penulis:</label><br>
        <input type="text" name="penulis" required>
        <br><br>

        <label>Genre:</label><br>
        <input type="text" name="genre">
        <br><br>

        <label>Sinopsis:</label><br>
        <textarea name="sinopsis" rows="5"></textarea>
        <br><br>

        <button type="submit">Simpan Buku</button>
        <a href="buku.php">Kembali</a>
    </form>

</body>
</html>