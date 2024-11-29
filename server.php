<?php
// Memulai session agar dapat mengakses variabel session
session_start();

// Mendefinisikan detail koneksi ke database
$host = 'localhost';
$user = 'user'; 
$password = '@dunanes_123xxy';
$dbname = 'perpuspemweb';

// Membuat koneksi ke database menggunakan objek mysqli
$conn = new mysqli($host, $user, $password, $dbname);

// Mengecek apakah koneksi berhasil, jika gagal, tampilkan pesan error
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Mengecek apakah user sudah login dengan memeriksa session 'user_id'
if (!isset($_SESSION['user_id'])) {
    die("Anda harus login terlebih dahulu.");
}

// Jika ada parameter 'add_to_cart' di URL, proses untuk menambahkan buku ke keranjang
if (isset($_GET['add_to_cart'])) {
    // Mendapatkan ID buku yang ingin ditambahkan ke keranjang dan ID user dari session
    $book_id = intval($_GET['add_to_cart']);
    $user_id = intval($_SESSION['user_id']); 

    // Mengecek apakah buku sudah ada di keranjang user
    $sql_check = "SELECT * FROM keranjang WHERE user_id = ? AND book_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ii", $user_id, $book_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    // Jika buku belum ada di keranjang, tambahkan ke keranjang
    if ($result_check->num_rows == 0) {
        $sql_insert = "INSERT INTO keranjang (user_id, book_id) VALUES (?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ii", $user_id, $book_id);

        // Jika query berhasil, tampilkan pesan sukses
        if ($stmt_insert->execute()) {
            echo "Buku berhasil ditambahkan ke keranjang!";
        } else {
            // Jika terjadi error pada query, tampilkan pesan error
            echo "Error: " . $stmt_insert->error;
        }
    } else {
        // Jika buku sudah ada di keranjang, tampilkan pesan error
        echo "Buku sudah ada di keranjang!";
    }
}

// Jika ada parameter 'hapus' di URL, proses untuk menghapus buku dari keranjang
if (isset($_GET['hapus'])) {
    // Mendapatkan ID keranjang dan ID user dari session
    $keranjang_id = intval($_GET['hapus']);
    $user_id = intval($_SESSION['user_id']);

    // Query untuk menghapus buku dari keranjang berdasarkan ID keranjang dan user ID
    $sql_delete = "DELETE FROM keranjang WHERE id = ? AND user_id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("ii", $keranjang_id, $user_id);

    // Jika query berhasil, tampilkan pesan sukses
    if ($stmt_delete->execute()) {
        echo "Buku berhasil dihapus dari keranjang!";
    } else {
        // Jika terjadi error pada query, tampilkan pesan error
        echo "Error: " . $stmt_delete->error;
    }
}

// Mendapatkan ID user dari session
$user_id = intval($_SESSION['user_id']);

// Query untuk menampilkan daftar buku yang ada di keranjang user
$sql_display = "SELECT b.id, b.judul, b.penulis, k.id as keranjang_id 
                FROM keranjang k 
                JOIN buku b ON k.book_id = b.id
                WHERE k.user_id = ?";
$stmt_display = $conn->prepare($sql_display);
$stmt_display->bind_param("i", $user_id);
$stmt_display->execute();
$result_display = $stmt_display->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Keranjang Buku</title>
    <style>
        /* Styling untuk tabel keranjang */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #fd1b7d;
            color: white;
        }
        /* Styling untuk tombol */
        .btn {
            padding: 5px 10px;
            background-color: #fd1b7d;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn:hover {
            background-color: hotpink;
        }
    </style>
</head>
<body>

<!-- Navbar untuk menampilkan nama halaman dan tombol logout -->
<div class="navbar">
    <h2>Keranjang Buku</h2>
    <nav>
        <a href="dashboard.php" class="btn">Home</a>
        <a href="logout.php" class="btn logout-btn">Logout</a>
    </nav>
</div>

<!-- Container untuk menampilkan keranjang buku -->
<div class="form-container">
    <h3>Keranjang Buku Anda</h3>

    <?php if ($result_display->num_rows > 0): ?>
        <!-- Jika ada buku dalam keranjang, tampilkan dalam tabel -->
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Judul Buku</th>
                    <th>Penulis</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_display->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['keranjang_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['judul']); ?></td>
                        <td><?php echo htmlspecialchars($row['penulis']); ?></td>
                        <td>
                            <!-- Tombol untuk menghapus buku dari keranjang -->
                            <a href="keranjang.php?hapus=<?php echo $row['keranjang_id']; ?>" class="btn">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <!-- Jika keranjang kosong, tampilkan pesan -->
        <p>Keranjang Anda kosong.</p>
    <?php endif; ?>

    <br>
    <!-- Tombol untuk menambah buku lagi -->
    <a href="tambah_buku.php" class="btn">Tambah Buku Lagi</a>
</div>

</body>
</html>

<?php
// Menutup koneksi ke database setelah selesai
$conn->close();
?>
