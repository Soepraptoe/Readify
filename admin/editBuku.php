<?php
require_once "../config/koneksi.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    die("ID buku tidak valid.");
}

$stmt = mysqli_prepare($conn, "SELECT * FROM books WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$buku = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$buku) {
    die("Buku tidak ditemukan.");
}

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
            "UPDATE books
             SET judul = ?, penulis = ?, genre = ?, sinopsis = ?
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssssi",
            $judul,
            $penulis,
            $genre,
            $sinopsis,
            $id
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: buku.php");
            exit;
        } else {
            $error = "Buku gagal diperbarui.";
            mysqli_stmt_close($stmt);
        }
    }

    $buku["judul"] = $judul;
    $buku["penulis"] = $penulis;
    $buku["genre"] = $genre;
    $buku["sinopsis"] = $sinopsis;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku - Readify</title>
</head>
<body>

    <h1>Edit Buku</h1>

    <?php if ($error !== "") { ?>
        <p style="color: red;">
            <?= htmlspecialchars($error); ?>
        </p>
    <?php } ?>

    <form method="POST">
        <label>Judul Buku:</label><br>
        <input
            type="text"
            name="judul"
            value="<?= htmlspecialchars($buku["judul"]); ?>"
            required
        >
        <br><br>

        <label>Penulis:</label><br>
        <input
            type="text"
            name="penulis"
            value="<?= htmlspecialchars($buku["penulis"]); ?>"
            required
        >
        <br><br>

        <label>Genre:</label><br>
        <input
            type="text"
            name="genre"
            value="<?= htmlspecialchars($buku["genre"] ?? ""); ?>"
        >
        <br><br>

        <label>Sinopsis:</label><br>
        <textarea name="sinopsis" rows="5"><?= htmlspecialchars($buku["sinopsis"] ?? ""); ?></textarea>
        <br><br>

        <button type="submit">Simpan Perubahan</button>
        <a href="buku.php">Kembali</a>
    </form>

</body>
</html>