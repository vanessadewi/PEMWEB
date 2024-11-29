<?php
session_start(); // Memulai sesi PHP untuk melacak informasi pengguna.

if (!isset($_SESSION['username'])) { // Mengecek apakah sesi 'username' tersedia.
    header("Location: login.php"); // Jika tidak, alihkan ke halaman login.
    exit(); // Hentikan eksekusi skrip.
}

// Konfigurasi koneksi database.
$host = 'localhost'; // Nama host.
$user = 'user'; // Nama pengguna database.
$password = '@dunanes_123xxy'; // Kata sandi pengguna database.
$dbname = 'perpuspemweb'; // Nama database.

$conn = new mysqli($host, $user, $password, $dbname); // Membuat koneksi ke database MySQL.

if ($conn->connect_error) { // Memeriksa jika koneksi gagal.
    die("Connection failed: " . $conn->connect_error); // Tampilkan pesan error dan hentikan skrip.
}

$username = $_SESSION['username']; // Ambil nama pengguna dari sesi login.

$searchResults = []; // Inisialisasi array kosong untuk menyimpan hasil pencarian.

if (isset($_POST['search'])) { // Memeriksa apakah tombol pencarian diklik.
    $keyword = $_POST['keyword']; // Mengambil input kata kunci pencarian.
    $keyword = $conn->real_escape_string($keyword); // Membersihkan input untuk mencegah SQL Injection.

    // Query SQL untuk mencari buku berdasarkan judul atau penulis yang sesuai dengan kata kunci.
    $sql = "SELECT id, judul, penulis, tahun_terbit, genre, stok 
            FROM buku 
            WHERE judul LIKE '%$keyword%' OR penulis LIKE '%$keyword%'";
    $result = $conn->query($sql); // Menjalankan query.

    if ($result->num_rows > 0) { // Jika ada hasil pencarian.
        while ($row = $result->fetch_assoc()) { // Loop untuk setiap baris hasil pencarian.
            $searchResults[] = $row; // Menambahkan baris ke array `$searchResults`.
        }
    }
}

if (isset($_POST['add_to_cart'])) { // Memeriksa apakah tombol "Tambah ke Keranjang" ditekan.
    $bookId = $_POST['book_id']; // Mendapatkan ID buku yang ingin ditambahkan ke keranjang.
    $quantity = 1; // Jumlah buku default yang ditambahkan adalah 1.

    // Query untuk mendapatkan ID pengguna berdasarkan username.
    $sql_user = "SELECT id FROM users WHERE username = ?";
    $stmt_user = $conn->prepare($sql_user); // Siapkan statement SQL.
    $stmt_user->bind_param("s", $username); // Masukkan parameter username.
    $stmt_user->execute(); // Jalankan query.
    $user_result = $stmt_user->get_result(); // Ambil hasil query.
    $user = $user_result->fetch_assoc(); // Ambil data pengguna sebagai array asosiatif.
    $user_id = $user['id']; // Simpan ID pengguna.

    // Periksa apakah buku sudah ada di keranjang.
    $check_sql = "SELECT * FROM keranjang WHERE user_id = ? AND book_id = ?";
    $stmt_check = $conn->prepare($check_sql); // Siapkan statement SQL.
    $stmt_check->bind_param("ii", $user_id, $bookId); // Masukkan parameter user_id dan book_id.
    $stmt_check->execute(); // Jalankan query.
    $check_result = $stmt_check->get_result(); // Ambil hasil query.

    if ($check_result->num_rows > 0) { // Jika buku sudah ada di keranjang.
        // Perbarui jumlah buku di keranjang.
        $update_sql = "UPDATE keranjang SET quantity = quantity + 1 WHERE user_id = ? AND book_id = ?";
        $stmt_update = $conn->prepare($update_sql); // Siapkan query update.
        $stmt_update->bind_param("ii", $user_id, $bookId); // Masukkan parameter.
        $stmt_update->execute(); // Jalankan query.
        $stmt_update->close(); // Tutup statement.
    } else { // Jika buku belum ada di keranjang.
        // Tambahkan buku ke keranjang.
        $insert_sql = "INSERT INTO keranjang (user_id, book_id, quantity) VALUES (?, ?, ?)";
        $stmt_insert = $conn->prepare($insert_sql); // Siapkan query insert.
        $stmt_insert->bind_param("iii", $user_id, $bookId, $quantity); // Masukkan parameter.
        $stmt_insert->execute(); // Jalankan query.
        $stmt_insert->close(); // Tutup statement.
    }
}

$conn->close(); // Tutup koneksi database.
?>


<!DOCTYPE html>
<html lang="en"> <!-- Deklarasi dokumen HTML5 dan bahasa dokumen adalah Inggris -->
<head>
    <meta charset="UTF-8"> <!-- Menentukan encoding karakter menggunakan UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Membuat halaman responsif untuk perangkat -->
    <title>Pencarian Buku</title> <!-- Judul halaman yang muncul di tab browser -->
    <style>
        ...
    </style> <!-- Gaya CSS internal -->
</head>
<div class="navbar"> <!-- Elemen navbar untuk bagian atas halaman -->
    <h2>Pencarian Buku</h2> <!-- Judul navbar -->
</div>
<div class="sidebar"> <!-- Elemen sidebar untuk navigasi -->
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h2> <!-- Menampilkan nama pengguna yang sedang login -->
    <a href="dashboard.php"><i class="fas fa-home"></i> Home</a> <!-- Link menuju dashboard -->
    <a href="tentang.php"><i class="fas fa-info-circle"></i> Tentang</a> <!-- Link menuju halaman Tentang -->
    <a href="buku.php"><i class="fas fa-search"></i> Cari Buku</a> <!-- Link menuju halaman pencarian buku -->
    <a href="keranjang.php"><i class="fas fa-shopping-cart"></i> Keranjang</a> <!-- Link menuju keranjang pengguna -->
    <a href="profile.php"><i class="fas fa-user"></i> Profil</a> <!-- Link menuju profil pengguna -->
    <a href="logout.php"><i class="fas fa-close"></i> Logout</a> <!-- Link untuk logout -->
</div>
<div class="form-container"> <!-- Kontainer form pencarian buku -->
    <h3>Cari Buku</h3> <!-- Judul kecil pada form -->
    <form action="buku.php" method="POST"> <!-- Formulir dengan metode POST -->
        <input type="text" name="keyword" placeholder="Masukkan judul atau penulis" required> <!-- Input teks untuk kata kunci pencarian -->
        <button type="submit" name="search" class="btn">Cari</button> <!-- Tombol untuk submit formulir -->
    </form>
</div>
<?php if (isset($_POST['search'])): ?> <!-- Mengecek apakah form pencarian telah dikirimkan -->
    <h3>Hasil Pencarian:</h3> <!-- Judul kecil untuk hasil pencarian -->
    <table> <!-- Elemen tabel untuk hasil pencarian -->
        <thead> <!-- Bagian header tabel -->
            <tr>
                <th>Judul</th> <!-- Kolom judul -->
                <th>Penulis</th> <!-- Kolom penulis -->
                <th>Tahun Terbit</th> <!-- Kolom tahun terbit -->
                <th>Stok</th> <!-- Kolom stok buku -->
                <th>Tambah ke Keranjang</th> <!-- Kolom tombol tambah -->
            </tr>
        </thead>
        <tbody> <!-- Bagian isi tabel -->
            <?php foreach ($searchResults as $book): ?> <!-- Looping hasil pencarian -->
                <tr>
                    <td><?php echo htmlspecialchars($book['judul']); ?></td> <!-- Menampilkan judul buku -->
                    <td><?php echo htmlspecialchars($book['penulis']); ?></td> <!-- Menampilkan penulis -->
                    <td><?php echo htmlspecialchars($book['tahun_terbit']); ?></td> <!-- Menampilkan tahun terbit -->
                    <td><?php echo htmlspecialchars($book['stok']); ?></td> <!-- Menampilkan stok -->
                    <td>
                        <form action="buku.php" method="POST"> <!-- Formulir untuk menambah ke keranjang -->
                            <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>"> <!-- Input tersembunyi untuk ID buku -->
                            <button type="submit" name="add_to_cart">Tambah</button> <!-- Tombol untuk menambahkan buku ke keranjang -->
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
