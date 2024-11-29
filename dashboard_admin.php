<?php
// Memulai sesi PHP untuk mengelola data pengguna yang sedang login
session_start();

// Memeriksa apakah variabel sesi 'username' telah diatur
// Jika belum diatur, pengguna dianggap belum login dan diarahkan ke halaman login
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Mengarahkan pengguna ke login.php
    exit(); // Menghentikan eksekusi kode selanjutnya
}

// Memeriksa apakah peran pengguna adalah 'admin'
// Jika bukan admin, pengguna diarahkan kembali ke login
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php"); // Mengarahkan pengguna ke login.php
    exit(); // Menghentikan eksekusi kode selanjutnya
}
?>

<!DOCTYPE html>
<html lang="en"> <!-- Deklarasi dokumen HTML5 dengan bahasa Inggris -->
<head>
    <meta charset="UTF-8"> <!-- Mengatur encoding karakter ke UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Agar halaman responsif -->
    <title>Dashboard</title> <!-- Judul halaman -->
    
    <!-- Menyisipkan font 'Poppins' dari Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Menyisipkan ikon dari Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- Bagian gaya CSS -->
    <style>
        /* Reset margin dan padding untuk semua elemen */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Gaya umum untuk body dan input */
        body, input {
            font-family: "Poppins", sans-serif; /* Menggunakan font Poppins */
        }

        /* Gaya untuk body */
        body {
            display: flex; /* Mengatur tata letak menggunakan flexbox */
            min-height: 100vh; /* Tinggi minimum sesuai tinggi layar */
            background-color: #e95ba7; /* Warna latar pink terang */
            flex-direction: column; /* Elemen disusun secara vertikal */
        }

        /* Sidebar navigasi */
        .sidebar {
            width: 20%; /* Lebar sidebar 20% dari lebar layar */
            background-color: rgba(255, 255, 255, 0.8); /* Warna putih transparan */
            padding: 2rem 1rem; /* Padding di dalam sidebar */
            text-align: center; /* Teks rata tengah */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Efek bayangan */
            position: fixed; /* Tetap di sisi kiri layar */
            top: 0; /* Dari atas layar */
            bottom: 0; /* Hingga bawah layar */
            left: 0; /* Dari kiri layar */
        }

        /* Judul sidebar */
        .sidebar h2 {
            font-size: 1.8rem; /* Ukuran font besar */
            color: #fd1b7d; /* Warna pink terang */
            margin-bottom: 1rem; /* Jarak bawah */
        }

        /* Gaya tautan dalam sidebar */
        .sidebar a {
            display: block; /* Setiap tautan di baris baru */
            color: #615e5e; /* Warna teks abu-abu gelap */
            text-decoration: none; /* Hilangkan garis bawah */
            font-size: 1rem; /* Ukuran font */
            margin: 10px 0; /* Jarak antar tautan */
            padding: 10px; /* Spasi dalam tautan */
            background-color: rgba(233, 225, 225, 0.5); /* Warna latar transparan */
            border-radius: 10px; /* Sudut membulat */
            transition: background-color 0.3s; /* Animasi perubahan warna */
        }

        .sidebar a:hover {
            background-color: #fd1b7d; /* Warna pink terang saat hover */
            color: white; /* Teks berubah menjadi putih */
        }

        /* Gaya konten utama */
        .main-content {
            flex: 1; /* Mengisi ruang yang tersisa */
            margin-left: 20%; /* Memberi jarak dari sidebar */
            display: flex; /* Menggunakan flexbox untuk tata letak */
            justify-content: center; /* Elemen di tengah horizontal */
            align-items: center; /* Elemen di tengah vertikal */
            background-image: url('images/background.jpg'); /* Gambar latar */
            background-size: cover; /* Menyesuaikan ukuran gambar dengan layar */
            background-position: center; /* Gambar di posisi tengah */
            background-repeat: no-repeat; /* Tidak mengulang gambar */
        }

        /* Kotak ucapan selamat datang */
        .welcome-box {
            background-color: white; /* Warna latar putih */
            padding: 2rem; /* Padding dalam kotak */
            border-radius: 10px; /* Sudut membulat */
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1); /* Efek bayangan */
            text-align: center; /* Teks rata tengah */
            max-width: 400px; /* Lebar maksimum kotak */
        }

        .welcome-box h3 {
            color: #fd1b7d; /* Warna pink terang */
            margin-bottom: 1rem; /* Jarak bawah */
        }

        .welcome-box p {
            color: #615e5e; /* Warna teks abu-abu gelap */
            font-size: 1rem; /* Ukuran font */
        }
    </style>
</head>
<body>
    <!-- Sidebar untuk navigasi -->
    <div class="sidebar">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h2> <!-- Menampilkan nama pengguna -->

        <!-- Menu navigasi -->
        <a href="dashboard.php"><i class="fas fa-home"></i> Home</a>
        <a href="tentang.php"><i class="fas fa-info-circle"></i> Tentang</a>
        <a href="buku.php"><i class="fas fa-search"></i> Cari Buku</a>
        <a href="keranjang.php"><i class="fas fa-shopping-cart"></i> Keranjang</a>
        <a href="profile.php"><i class="fas fa-user"></i> Profil</a>
        <a href="logout.php"><i class="fas fa-close"></i> Logout</a>

        <!-- Menu tambahan jika pengguna adalah admin -->
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') { ?>
            <hr> <!-- Garis pemisah -->
            <h3 style="color: #fd1b7d; margin-top: 10px;">Admin Menu</h3> <!-- Judul menu admin -->
            <a href="kelola_akun.php"><i class="fas fa-users-cog"></i> Kelola Akun</a> <!-- Tautan kelola akun -->
            <a href="kelola_buku.php"><i class="fas fa-book"></i> Kelola Buku</a> <!-- Tautan kelola buku -->
        <?php } ?>
    </div>

    <!-- Konten utama -->
    <div class="main-content">
        <div class="welcome-box">
            <h3>Dashboard</h3>
            <p>Selamat datang di dashboard Anda, <?php echo htmlspecialchars($_SESSION['username']); ?>! Di sini Anda dapat mengelola pengaturan, melihat profil, dan banyak lagi.</p>
        </div>
    </div>
</body>
</html>

