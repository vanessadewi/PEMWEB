<?php
// Memulai sesi untuk mengakses data session
session_start();

// Mengecek apakah session 'username' tidak ada atau role pengguna bukan 'admin'
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    // Jika tidak ada session 'username' atau peran bukan admin, arahkan ke halaman login
    header("Location: login.php");
    exit(); // Menghentikan eksekusi lebih lanjut
}

// Menyimpan data untuk koneksi ke database
$host = 'localhost'; // Alamat host database
$user = 'user'; // Username database
$password = '@dunanes_123xxy'; // Password untuk database
$dbname = 'perpuspemweb'; // Nama database yang digunakan

// Membuat koneksi ke database MySQL
$conn = new mysqli($host, $user, $password, $dbname);

// Mengecek apakah ada error saat koneksi ke database
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // Menampilkan pesan error jika koneksi gagal
}

// Mengecek apakah form untuk menambah buku telah dikirimkan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_book'])) {

    // Mengambil dan membersihkan data inputan dari form
    $judul = htmlspecialchars($_POST['judul'], ENT_QUOTES, 'UTF-8'); // Menjaga karakter khusus agar aman dari XSS
    $penulis = htmlspecialchars($_POST['penulis'], ENT_QUOTES, 'UTF-8');
    $tahun = intval($_POST['tahun']); // Mengkonversi tahun menjadi integer
    $genre = htmlspecialchars($_POST['genre'], ENT_QUOTES, 'UTF-8');
    $stok = intval($_POST['stok']); // Mengkonversi stok menjadi integer

    // Menyiapkan query untuk menyisipkan data buku baru ke dalam database
    $sql = "INSERT INTO buku (judul, penulis, tahun_terbit, genre, stok) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql); // Menyiapkan statement
    $stmt->bind_param("ssisi", $judul, $penulis, $tahun, $genre, $stok); // Mengikat parameter dengan tipe data yang sesuai

    // Menjalankan query untuk memasukkan data buku
    if ($stmt->execute()) {
        // Jika berhasil, redirect ke halaman kelola buku
        header("Location: kelola_buku.php");
        exit(); // Hentikan eksekusi lebih lanjut
    } else {
        // Jika gagal, tampilkan error
        echo "Error: " . $stmt->error;
    }

    // Menutup statement setelah eksekusi
    $stmt->close();
}

// Mengecek apakah ada permintaan untuk menghapus buku berdasarkan parameter 'delete_id' di URL
if (isset($_GET['delete_id'])) {
    // Mengambil id buku yang akan dihapus
    $delete_id = intval($_GET['delete_id']); // Mengkonversi id ke integer
    $sql = "DELETE FROM buku WHERE id = ?"; // Query untuk menghapus buku berdasarkan id
    $stmt = $conn->prepare($sql); // Menyiapkan query
    $stmt->bind_param("i", $delete_id); // Mengikat parameter id dengan tipe integer

    // Menjalankan query untuk menghapus buku
    if ($stmt->execute()) {
        // Jika berhasil, redirect ke halaman kelola buku
        header("Location: kelola_buku.php");
        exit(); // Hentikan eksekusi lebih lanjut
    } else {
        // Jika gagal, tampilkan error
        echo "Error: " . $stmt->error;
    }

    // Menutup statement setelah eksekusi
    $stmt->close();
}

// Query untuk mengambil data buku dari database
$sql = "SELECT id, judul, penulis, tahun_terbit, genre, stok FROM buku";
$result = $conn->query($sql); // Menjalankan query untuk mengambil data buku

// Menutup koneksi ke database
$conn->close();
?>

<!-- HTML untuk tampilan halaman pengelolaan buku -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Data Buku</title>
    <style>
    /* Styling untuk seluruh halaman */
    body {
        font-family: Arial, sans-serif; /* Mengatur font */
        margin: 20px;
        background-color: #ffe6f2; /* Warna latar belakang */
        color: #333; /* Warna teks */
    }

    /* Styling untuk judul dan subjudul */
    h2, h3 {
        text-align: center;
        color: #fd1b7d; /* Warna merah muda */
    }

    /* Container utama */
    .container {
        max-width: 900px;
        margin: 0 auto;
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Efek bayangan */
    }

    /* Styling untuk form input */
    form {
        margin-top: 20px;
        padding: 15px;
        background: #ffd6e7;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Efek bayangan */
    }

    form input, form select {
        margin-bottom: 10px;
        padding: 10px;
        width: calc(100% - 22px); /* Menyesuaikan lebar input */
        border: 1px solid #ffe6f2;
        border-radius: 5px;
    }

    /* Styling untuk tombol submit form */
    form input[type="submit"] {
        background-color: #fd1b7d;
        color: white;
        border: none;
        cursor: pointer;
        text-transform: uppercase;
        font-weight: bold;
        transition: background-color 0.3s ease; /* Efek transisi saat hover */
    }

    form input[type="submit"]:hover {
        background-color: hotpink; /* Mengubah warna saat hover */
    }

    /* Styling untuk tabel data buku */
    table {
        width: 100%;
        border-collapse: collapse; /* Menghapus jarak antar border */
        margin-top: 20px;
    }

    table, th, td {
        border: 1px solid #fd1b7d; /* Border merah muda */
    }

    th, td {
        padding: 10px;
        text-align: left;
    }

    th {
        background-color: #fd1b7d;
        color: white;
        text-transform: uppercase;
    }

    tr:nth-child(even) {
        background-color: #ffe6f2; /* Warna latar belakang baris genap */
    }

    /* Styling untuk tombol aksi (edit, hapus) */
    .action-buttons a {
        text-decoration: none;
        padding: 5px 10px;
        color: white;
        background-color: #fd1b7d;
        border-radius: 3px;
        margin-right: 5px;
        transition: background-color 0.3s ease;
    }

    .action-buttons a:hover {
        background-color: hotpink; /* Mengubah warna tombol saat hover */
    }

    /* Styling untuk link kembali ke dashboard */
    a {
        text-decoration: none;
        color: #fd1b7d;
        font-weight: bold;
    }

    a:hover {
        color: hotpink;
    }

    </style>
</head>
<body>
    <!-- Container utama untuk halaman kelola buku -->
    <div class="container">
        <h2>Kelola Data Buku</h2>

        <!-- Form untuk menambahkan buku baru -->
        <form action="" method="POST">
            <h3>Tambah Buku Baru</h3>
            <input type="text" name="judul" placeholder="Judul Buku" required>
            <input type="text" name="penulis" placeholder="Penulis" required>
            <input type="number" name="tahun" placeholder="Tahun Terbit" required>
            <input type="text" name="genre" placeholder="Genre" required>
            <input type="number" name="stok" placeholder="Stok Buku" required>
            <input type="submit" name="add_book" value="Tambah Buku">
        </form>

        <!-- Tabel untuk menampilkan data buku -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Judul</th>
                    <th>Penulis</th>
                    <th>Tahun Terbit</th>
                    <th>Genre</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Menampilkan data buku yang diambil dari database
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['judul'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['penulis'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['tahun_terbit'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['genre'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['stok'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="action-buttons">
                                <!-- Link untuk mengedit dan menghapus buku -->
                                <a href="edit_buku.php?id=<?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?>">Edit</a>
                                <a href="kelola_buku.php?delete_id=<?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus buku ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php }
                } else { ?>
                    <!-- Menampilkan pesan jika tidak ada buku di database -->
                    <tr>
                        <td colspan="7">Tidak ada buku dalam database.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Link kembali ke dashboard admin -->
        <a href="dashboard_admin.php">Kembali ke Dashboard</a>
    </div>

    <!-- Script untuk validasi input sebelum form disubmit -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form');

            form.addEventListener('submit', function (event) {
                const inputs = form.querySelectorAll('input[type="text"], input[type="number"]');
                let isValid = true;

                inputs.forEach(input => {
                    const value = input.value.trim();

                    // Validasi karakter yang tidak diperbolehkan
                    if (/[\<\>\"\'\`]/.test(value)) {
                        alert(`Input "${input.name}" mengandung karakter tidak diperbolehkan.`);
                        isValid = false;
                    }
                });

                // Jika input tidak valid, hentikan submit form
                if (!isValid) {
                    event.preventDefault();
                }
            });
        });
    </script>
</body>
</html>
