<?php
require_once "../config/koneksi.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $judul = trim($_POST["judul"] ?? "");
    $penulis = trim($_POST["penulis"] ?? "");
    $genre = trim($_POST["genre"] ?? "");
    $sinopsis = trim($_POST["sinopsis"] ?? "");

    $namaCover = null;
    $fileCover = $_FILES["cover"] ?? null;

    if ($judul === "" || $penulis === "") {
        $error = "Judul dan penulis wajib diisi!";
    } else {
        // Proses upload cover jika dipilih
        if ($fileCover && $fileCover["error"] !== UPLOAD_ERR_NO_FILE) {
            if ($fileCover["error"] !== UPLOAD_ERR_OK) {
                $error = "Upload cover gagal.";
            } elseif ($fileCover["size"] > 2 * 1024 * 1024) {
                $error = "Ukuran cover maksimal 2 MB.";
            } else {
                $info = new finfo(FILEINFO_MIME_TYPE);
                $tipe = $info->file($fileCover["tmp_name"]);

                $formatDiizinkan = [
                    "image/jpeg" => "jpg",
                    "image/png"  => "png",
                    "image/webp" => "webp"
                ];

                if (!isset($formatDiizinkan[$tipe])) {
                    $error = "Cover harus berformat JPG, PNG, atau WEBP.";
                } else {
                    $folderCover = __DIR__ . "/../assets/covers/";

                    if (!is_dir($folderCover)) {
                        $error = "Folder penyimpanan cover tidak ditemukan.";
                    } else {
                        $namaCover = bin2hex(random_bytes(16))
                            . "." . $formatDiizinkan[$tipe];

                        if (!move_uploaded_file(
                            $fileCover["tmp_name"],
                            $folderCover . $namaCover
                        )) {
                            $error = "Cover gagal disimpan.";
                            $namaCover = null;
                        }
                    }
                }
            }
        }

        // Simpan data buku jika tidak ada error
        if ($error === "") {
            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO books (judul, penulis, genre, cover, sinopsis)
                 VALUES (?, ?, ?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "sssss",
                $judul,
                $penulis,
                $genre,
                $namaCover,
                $sinopsis
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header("Location: buku.php");
                exit;
            } else {
                // Hapus file jika penyimpanan database gagal
                if ($namaCover !== null) {
                    $path = __DIR__ . "/../assets/covers/" . $namaCover;
                    if (is_file($path)) {
                        unlink($path);
                    }
                }

                mysqli_stmt_close($stmt);
                $error = "Buku gagal ditambahkan.";
            }
        }
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

    <form method="POST" enctype="multipart/form-data">
        <label>Judul Buku:</label><br>
        <input type="text" name="judul" required>
        <br><br>

        <label>Penulis:</label><br>
        <input type="text" name="penulis" required>
        <br><br>

        <label>Cover Buku (opsional):</label><br>
        <input
            type="file"
            name="cover"
            accept="image/jpeg,image/png,image/webp"
        >
        <br>
        <small>Format JPG, PNG, atau WEBP. Maksimal 2 MB.</small>
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