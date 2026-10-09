<?php
require_once "../config/koneksi.php";

$query = mysqli_query($conn, "SELECT * FROM books ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Buku - Readify</title>
</head>
<body>

    <h1>Kelola Buku Readify</h1>

    <p>Daftar buku yang tersimpan di database.</p>

    <a href="tambah_buku.php">+ Tambah Buku</a>

    <br><br>

    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>No</th>
                <th>Judul</th>
                <th>Penulis</th>
                <th>Genre</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $no = 1;

            while ($buku = mysqli_fetch_assoc($query)) {
            ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($buku['judul']); ?></td>
                    <td><?= htmlspecialchars($buku['penulis']); ?></td>
                    <td><?= htmlspecialchars($buku['genre'] ?? ''); ?></td>
                    <td>
                        <a href="editBuku.php?id=<?= $buku['id']; ?>">Edit</a>
                        |
                        <a href="hapusBuku.php?id=<?= $buku['id']; ?>">Hapus</a>
                    </td>
                </tr>
            <?php } ?>

            <?php if (mysqli_num_rows($query) === 0) { ?>
                <tr>
                    <td colspan="5">Belum ada buku yang ditambahkan.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

</body>
</html>