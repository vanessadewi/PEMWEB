<?php
session_start(); // Memulai session untuk melacak status login pengguna

// Mengecek apakah session 'username' ada. Jika tidak ada, mengarahkan pengguna ke halaman login.php
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Mengarahkan pengguna yang tidak terautentikasi ke halaman login
    exit(); // Menghentikan eksekusi skrip lebih lanjut setelah pengalihan
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> <!-- Menentukan karakter encoding halaman sebagai UTF-8 untuk mendukung karakter internasional -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Membuat halaman responsif di berbagai perangkat dengan mengatur viewport -->
    <title>Tentang</title> <!-- Judul halaman, ditampilkan pada tab browser -->
    
    <style>
        /* Mengimpor font Poppins dari Google Fonts dan ikon dari Font Awesome */
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800&display=swap");
        @import url("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css");

        * {
            margin: 0; /* Menghilangkan margin default dari semua elemen */
            padding: 0; /* Menghilangkan padding default dari semua elemen */
            box-sizing: border-box; /* Mengatur box-sizing untuk semua elemen agar padding dan border dihitung dalam ukuran elemen */
        }

        body {
            font-family: "Poppins", sans-serif; /* Menggunakan font Poppins untuk seluruh halaman */
        }

        /* Mengatur gaya untuk background halaman */
        body {
            background-image: url('images/background.jpg'); /* Menggunakan gambar sebagai latar belakang */
            background-size: cover; /* Mengatur gambar agar menutupi seluruh area halaman */
            background-position: center; /* Mengatur posisi gambar agar berada di tengah */
            background-repeat: no-repeat; /* Mencegah gambar latar belakang diulang */
            height: 100vh; /* Mengatur tinggi halaman agar memenuhi tinggi viewport */
            display: flex; /* Menggunakan flexbox untuk mempermudah penataan elemen */
            justify-content: center; /* Menempatkan konten di tengah secara horizontal */
            align-items: center; /* Menempatkan konten di tengah secara vertikal */
            margin: 0; /* Menghapus margin default dari body */
        }

        /* Menata kontainer form untuk informasi tentang */
        .form-container {
            background-color: rgba(255, 255, 255, 0.8); /* Latar belakang putih transparan */
            padding: 2rem; /* Memberikan ruang sekitar konten form */
            border-radius: 10px; /* Membuat sudut form melengkung */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Memberikan bayangan halus di sekitar form */
            width: 100%; /* Mengatur lebar form agar responsif */
            max-width: 400px; /* Membatasi lebar form agar tidak lebih dari 400px */
            text-align: center; /* Menata teks di dalam form agar terpusat */
        }

        /* Menata navbar */
        .navbar {
            background-color: rgba(255, 255, 255, 0.8); /* Latar belakang navbar dengan transparansi */
            padding: 10px; /* Memberikan ruang di dalam navbar */
            border-radius: 0; /* Tidak ada border radius di navbar */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Memberikan bayangan lembut di bawah navbar */
            display: flex; /* Menggunakan flexbox untuk penataan navbar */
            justify-content: center; /* Menempatkan konten navbar di tengah */
            align-items: center; /* Menempatkan konten navbar secara vertikal di tengah */
            position: fixed; /* Navbar tetap di posisi atas meskipun halaman digulir */
            top: 0; /* Menempatkan navbar di bagian atas halaman */
            left: 16rem; /* Menjauhkan navbar dari sisi kiri untuk memberi ruang sidebar */
            right: 0; /* Menjaga navbar tetap memenuhi seluruh lebar halaman */
        }

        /* Menata judul di navbar */
        .navbar h2 {
            color: #fd1b7d; /* Warna teks judul navbar */
            margin: 0; /* Menghapus margin dari h2 */
            font-size: 2rem; /* Ukuran font besar untuk judul */
            font-weight: 600; /* Menambah ketebalan teks */
        }

        /* Menata gaya tombol */
        .btn {
            padding: 10px 20px; /* Memberikan padding pada tombol */
            font-size: 0.9rem; /* Ukuran font lebih kecil pada tombol */
            background-color: #fd1b7d; /* Warna latar belakang tombol */
            color: white; /* Warna teks tombol */
            border: none; /* Menghilangkan border pada tombol */
            border-radius: 3px; /* Memberikan sudut melengkung pada tombol */
            cursor: pointer; /* Menunjukkan kursor pointer saat berada di atas tombol */
            text-transform: uppercase; /* Membuat teks tombol menjadi huruf kapital */
            text-decoration: none; /* Menghilangkan dekorasi teks (garis bawah) pada tautan tombol */
            transition: background-color 0.3s ease; /* Transisi lembut saat warna tombol berubah */
            font-family: 'Poppins', sans-serif; /* Menggunakan font Poppins pada tombol */
            font-weight: 500; /* Ketebalan font tombol */
        }

        /* Menambahkan efek saat hover pada tombol */
        .btn:hover {
            background-color: hotpink; /* Mengubah warna tombol saat hover */
        }

        /* Menata sidebar */
        .sidebar {
            width: 20%; /* Lebar sidebar adalah 20% dari lebar layar */
            background-color: rgba(255, 255, 255, 0.8); /* Latar belakang sidebar dengan transparansi */
            padding: 2rem 1rem; /* Memberikan ruang pada sidebar */
            text-align: center; /* Menyusun isi sidebar secara terpusat */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Bayangan halus di sidebar */
            position: fixed; /* Sidebar berada di posisi tetap */
            top: 0; /* Posisi sidebar dimulai dari bagian atas */
            bottom: 0; /* Sidebar mengisi seluruh tinggi layar */
            left: 0; /* Menempatkan sidebar di sisi kiri halaman */
        }

        /* Menata judul sidebar */
        .sidebar h2 {
            font-size: 1.8rem; /* Ukuran font besar untuk judul sidebar */
            color: #fd1b7d; /* Warna teks judul sidebar */
            margin-bottom: 1rem; /* Memberikan jarak di bawah judul */
        }

        /* Menata tautan di sidebar */
        .sidebar a {
            display: block; /* Menyusun tautan sebagai blok agar memenuhi lebar sidebar */
            color: #615e5e; /* Warna teks tautan */
            text-decoration: none; /* Menghilangkan dekorasi teks (garis bawah) pada tautan */
            font-size: 1rem; /* Ukuran font untuk tautan */
            margin: 10px 0; /* Memberikan jarak antar tautan */
            padding: 10px; /* Memberikan ruang di dalam tautan */
            background-color: rgba(233, 225, 225, 0.5); /* Latar belakang dengan transparansi */
            border-radius: 10px; /* Memberikan sudut melengkung pada tautan */
            transition: background-color 0.3s; /* Transisi warna latar belakang saat hover */
        }

        /* Menambahkan efek saat hover pada tautan di sidebar */
        .sidebar a:hover {
            background-color: #fd1b7d; /* Mengubah warna latar belakang tautan saat hover */
            color: white; /* Mengubah warna teks menjadi putih saat hover */
        }

        /* Menambahkan margin kanan pada ikon di sidebar */
        .sidebar i {
            margin-right: 10px; /* Memberikan jarak antara ikon dan teks di sidebar */
        }
    </style>
</head>
<body>
    <!-- Navbar bagian atas -->
    <div class="navbar">
        <h2>About Me</h2> <!-- Judul navbar -->
    </div>

    <!-- Sidebar kiri dengan menu navigasi -->
    <div class="sidebar">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
        <!-- Menampilkan nama pengguna yang login dengan aman menggunakan htmlspecialchars -->
        <a href="dashboard.php"><i class="fas fa-home"></i> Home</a> <!-- Menu Home -->
        <a href="tentang.php"><i class="fas fa-info-circle"></i> Tentang</a> <!-- Menu Tentang -->
        <a href="buku.php"><i class="fas fa-search"></i> Cari Buku</a> <!-- Menu Cari Buku -->
        <a href="keranjang.php"><i class="fas fa-shopping-cart"></i> Keranjang</a> <!-- Menu Keranjang -->
        <a href="profile.php"><i class="fas fa-user"></i> Profil</a> <!-- Menu Profil -->
        <a href="logout.php"><i class="fas fa-close"></i> Logout</a> <!-- Menu Logout -->
    </div>

    <!-- Kontainer form untuk informasi tentang -->
    <div class="form-container">
        <main>
            <h3>Tentang Kami</h3> <!-- Judul bagian tentang -->
            <p>Selamat datang di Perpustakaan, pusat informasi dan literasi yang berkomitmen untuk meningkatkan wawasan dan pengetahuan masyarakat. Kami hadir sebagai tempat yang menyediakan beragam koleksi buku, jurnal, majalah, dan sumber digital yang mencakup berbagai bidang ilmu, baik akademis, literatur, maupun hiburan.</p>
        </main>
    </div>
</body>
</html>
