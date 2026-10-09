<?php
require_once "../config/koneksi.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    die("ID buku tidak valid.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt = mysqli_prepare(
        $conn,
        "DELETE FROM books WHERE id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: buku.php");
        exit;
    } else {
        mysqli_stmt_close($stmt);
        die("Buku gagal dihapus.");
    }
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT judul FROM books WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$buku = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$buku) {
    die("Buku tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Buku - Readify</title>
</head>
<body>

    <h1>Hapus Buku</h1>

    <p>
        Apakah kamu yakin ingin menghapus buku
        <strong><?= htmlspecialchars($buku["judul"]); ?></strong>?
    </p>

    <form method="POST">
        <button type="submit">Ya, Hapus Buku</button>
        <a href="buku.php">Batal</a>
    </form>

</body>
</html>