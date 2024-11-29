<?php
// Memulai sesi untuk melacak data pengguna yang sedang login
session_start();

// Memeriksa apakah variabel sesi 'username' sudah diatur
// Jika tidak ada, pengguna belum login, maka diarahkan ke halaman login
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Mengarahkan ke login.php
    exit(); // Menghentikan eksekusi kode berikutnya
}

// Memeriksa apakah pengguna memiliki peran yang sesuai ('user')
// Jika peran tidak sesuai, maka pengguna diarahkan kembali ke login
if ($_SESSION['role'] !== 'user') {
    header("Location: login.php"); // Mengarahkan ke login.php
    exit(); // Menghentikan eksekusi kode berikutnya
}
?>

<!DOCTYPE html>
<html lang="en"> <!-- Deklarasi dokumen HTML5 dengan bahasa 'English' -->
<head>
    <meta charset="UTF-8"> <!-- Pengaturan karakter encoding UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Agar halaman responsif pada semua perangkat -->
    <title>Dashboard</title> <!-- Judul halaman -->

    <!-- Memuat font "Poppins" dari Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Memuat ikon dari Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>
        /* Reset margin, padding, dan box-sizing untuk semua elemen */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Mengatur font default untuk body dan input */
        body, input {
            font-family: "Poppins", sans-serif;
        }

        /* Mengatur gaya umum body */
        body {
            display: flex; /* Menggunakan flexbox untuk tata letak */
            min-height: 100vh; /* Tinggi minimum sesuai tinggi viewport */
            background-color: #e95ba7; /* Warna latar belakang */
            flex-direction: column; /* Susunan elemen secara vertikal */
        }

        /* Gaya untuk sidebar */
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

        .sidebar h2 {
            font-size: 1.8rem; /* Ukuran font besar untuk judul */
            color: #fd1b7d; /* Warna pink terang */
            margin-bottom: 1rem; /* Jarak bawah */
        }

        /* Gaya untuk tautan di sidebar */
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
            color: white; /* Warna teks menjadi putih */
        }

        /* Gaya untuk konten utama */
        .main-content {
            flex: 1; /* Memenuhi ruang yang tersisa */
            margin-left: 20%; /* Memberi jarak dari sidebar */
            display: flex; /* Menggunakan flexbox untuk tata letak */
            justify-content: center; /* Elemen di tengah secara horizontal */
            align-items: center; /* Elemen di tengah secara vertikal */
            background-image: url('images/background.jpg'); /* Gambar latar */
            background-size: cover; /* Gambar menyesuaikan ukuran layar */
            background-position: center; /* Gambar di posisi tengah */
            background-repeat: no-repeat; /* Tidak mengulang gambar */
        }

        /* Gaya untuk kotak ucapan selamat datang */
        .welcome-box {
            background-color: white; /* Warna putih */
            padding: 2rem; /* Padding di dalam kotak */
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
        <a href="dashboard.php"><i class="fas fa-home"></i> Home</a>
        <a href="tentang.php"><i class="fas fa-info-circle"></i> Tentang</a>
        <a href="buku.php"><i class="fas fa-search"></i> Cari Buku</a>
        <a href="keranjang.php"><i class="fas fa-shopping-cart"></i> Keranjang</a>
        <a href="profile.php"><i class="fas fa-user"></i> Profil</a>
        <a href="logout.php"><i class="fas fa-close"></i> Logout</a>
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
