<?php 
session_start(); // Memulai sesi untuk mengakses data session, seperti username dan status login.

// Mengecek apakah session 'username' ada, jika tidak, arahkan pengguna ke halaman login
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Arahkan ke halaman login jika session username tidak ada
    exit(); // Menghentikan eksekusi lebih lanjut setelah pengalihan
}

// Mengatur kredensial untuk koneksi ke database
$host = 'localhost'; // Alamat server database
$user = 'user'; // Username untuk login ke database
$password = '@dunanes_123xxy'; // Password untuk login ke database
$dbname = 'perpuspemweb'; // Nama database yang digunakan

// Membuat koneksi ke database menggunakan MySQLi
$conn = new mysqli($host, $user, $password, $dbname);

// Mengecek apakah ada kesalahan saat melakukan koneksi ke database
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // Menampilkan pesan error jika koneksi gagal
}

// Mendapatkan username yang sudah login dari session
$username = $_SESSION['username'];

// Query untuk mencari ID pengguna berdasarkan username
$sql_user = "SELECT id FROM users WHERE username = ?";
$stmt_user = $conn->prepare($sql_user); // Menyiapkan statement query
$stmt_user->bind_param("s", $username); // Mengikat parameter username ke query
$stmt_user->execute(); // Menjalankan query
$user_result = $stmt_user->get_result(); // Mendapatkan hasil query
$user = $user_result->fetch_assoc(); // Mengambil data hasil query sebagai array
$user_id = $user['id']; // Menyimpan id pengguna ke dalam variabel $user_id

// Mengecek apakah ada parameter 'hapus' di URL (untuk menghapus item dari keranjang)
if (isset($_GET['hapus'])) {
    $keranjang_id = intval($_GET['hapus']); // Mengambil ID keranjang yang akan dihapus dan mengkonversinya ke integer
    if ($keranjang_id > 0) { // Mengecek apakah ID valid
        // Query untuk menghapus data keranjang berdasarkan ID keranjang dan ID pengguna
        $sql = "DELETE FROM keranjang WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql); // Menyiapkan statement query
        $stmt->bind_param("ii", $keranjang_id, $user_id); // Mengikat parameter ID keranjang dan ID pengguna
        $stmt->execute(); // Menjalankan query untuk menghapus data
        $stmt->close(); // Menutup statement setelah eksekusi
    }
}

// Query untuk mengambil data keranjang buku yang dimiliki oleh pengguna berdasarkan user_id
$sql = "SELECT k.id, b.judul, b.penulis, k.quantity
        FROM keranjang k
        JOIN buku b ON k.book_id = b.id
        WHERE k.user_id = ?";
$stmt = $conn->prepare($sql); // Menyiapkan statement query
$stmt->bind_param("i", $user_id); // Mengikat parameter user_id ke query
$stmt->execute(); // Menjalankan query
$result = $stmt->get_result(); // Mendapatkan hasil query
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Buku</title> <!-- Judul halaman -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Styling untuk halaman dan elemen di dalamnya */
        body {
            font-family: "Poppins", sans-serif; 
            background-image: url('images/background.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        /* Navbar styling */
        .navbar {
            background-color: rgba(255, 255, 255, 0.9); 
            padding: 20px 40px;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .navbar h2 {
            color: #fd1b7d;
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
        }

        .navbar nav {
            display: flex;
            gap: 20px;
        }

        /* Styling untuk tabel keranjang buku */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 60px; 
            background-color: #fff;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px; 
            text-align: left;
        }

        th {
            background-color: #fd1b7d;
            color: white;
        }

        .btn {
            padding: 8px 16px;
            font-size: 0.85rem;
            background-color: #fd1b7d;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            text-transform: uppercase;
            text-decoration: none;
            transition: background-color 0.3s ease;
            font-weight: 500;
        }

        .btn:hover {
            background-color: hotpink;
        }

        /* Styling untuk form */
        .form-container {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
            margin: 100px auto;
        }
    </style>
</head>
<body>

<!-- Navbar untuk navigasi antar halaman -->
<div class="navbar">
    <h2>Perpus Pemweb</h2>
    <nav>
        <a href="dashboard.php" class="btn">Home</a>
        <a href="buku.php" class="btn">Cari Buku</a>
        <a href="logout.php" class="btn">Logout</a>
    </nav>
</div>

<!-- Form untuk menampilkan keranjang buku pengguna -->
<div class="form">
    <h3>Keranjang Buku Anda</h3>

    <!-- Mengecek apakah hasil query keranjang kosong atau tidak -->
    <?php if ($result->num_rows == 0): ?>
        <p>Keranjang Anda kosong.</p> <!-- Menampilkan pesan jika keranjang kosong -->
    <?php else: ?>
        <!-- Tabel untuk menampilkan daftar buku di keranjang -->
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Judul Buku</th>
                    <th>Penulis</th>
                    <th>Jumlah</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                // Menampilkan setiap item di keranjang pengguna
                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $no++; ?></td> <!-- Menampilkan nomor urut -->
                        <td><?php echo htmlspecialchars($row['judul']); ?></td> <!-- Menampilkan judul buku -->
                        <td><?php echo htmlspecialchars($row['penulis']); ?></td> <!-- Menampilkan penulis buku -->
                        <td><?php echo $row['quantity']; ?></td> <!-- Menampilkan jumlah buku -->
                        <td>
                            <!-- Link untuk menghapus item dari keranjang -->
                            <a href="keranjang.php?hapus=<?php echo $row['id']; ?>" class="btn">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>

</body>
</html>

<?php
$stmt->close(); // Menutup statement query
$conn->close(); // Menutup koneksi ke database
?>
