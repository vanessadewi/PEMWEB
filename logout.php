<?php
session_start(); // Memulai session yang akan digunakan untuk mengakses atau memodifikasi data session yang ada.

session_unset(); // Menghapus semua variabel session yang ada. Ini memastikan bahwa semua data yang disimpan dalam session dihapus.

session_destroy(); // Menghancurkan session yang ada. Ini akan menghapus data session secara permanen.

header("Location: login.php"); // Mengarahkan pengguna ke halaman login.php setelah sesi dihancurkan. Ini adalah pengalihan setelah logout.

exit(); // Menghentikan eksekusi lebih lanjut pada skrip setelah pengalihan, memastikan tidak ada kode lain yang dijalankan setelah header().
?>

<!DOCTYPE html>
<html lang="en"> <!-- Menentukan bahwa dokumen ini menggunakan bahasa Inggris -->
  <head>
    <meta charset="UTF-8" /> <!-- Menetapkan pengkodean karakter yang digunakan untuk halaman HTML ini -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" /> <!-- Mengatur viewport agar responsif di perangkat seluler -->
    <title>Logout</title> <!-- Menentukan judul halaman yang akan ditampilkan di tab browser -->
    <link rel="stylesheet" href="style.css" /> <!-- Menyertakan file CSS eksternal untuk styling halaman logout -->
  </head>
  <body>
    <h2>You have logged out!</h2> <!-- Menampilkan pesan yang memberi tahu pengguna bahwa mereka telah logout -->
    <a href="login.html">Login again</a> <!-- Menyediakan tautan untuk pengguna agar bisa login kembali -->
    
    <script src="script.js"></script> <!-- Menyertakan file JavaScript eksternal untuk halaman ini -->
  </body>
</html>
