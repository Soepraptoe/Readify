<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "readify_db";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>