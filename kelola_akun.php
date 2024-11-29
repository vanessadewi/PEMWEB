<?php 
// Memulai sesi PHP untuk mengakses data sesi
session_start();

// Mengecek apakah session 'username' tidak ada atau role pengguna bukan 'admin'
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    // Jika kondisi terpenuhi, redirect ke halaman login
    header("Location: login.php");
    exit(); // Hentikan eksekusi lebih lanjut
}

// Konfigurasi koneksi ke database MySQL
$host = 'localhost'; // Alamat host database
$user = 'root'; // Username database
$password = ''; // Password database
$dbname = 'perpuspemweb'; // Nama database yang akan digunakan

// Membuat koneksi ke database
$conn = new mysqli($host, $user, $password, $dbname);

// Mengecek apakah ada error saat koneksi ke database
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error); // Jika gagal, tampilkan pesan error dan berhenti
}

// Mengecek apakah ada parameter 'delete_id' di URL untuk menghapus akun
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id']; // Mengambil id yang akan dihapus
    $sql = "DELETE FROM users WHERE id = ?"; // Query untuk menghapus data berdasarkan id
    $stmt = $conn->prepare($sql); // Menyiapkan query
    $stmt->bind_param("i", $delete_id); // Mengikat parameter, id sebagai integer
    $stmt->execute(); // Menjalankan query
    $stmt->close(); // Menutup statement setelah eksekusi
    header("Location: kelola_akun.php"); // Redirect ke halaman kelola_akun setelah menghapus
    exit(); // Hentikan eksekusi lebih lanjut
}

// Mengecek apakah ada permintaan POST untuk mengedit data akun
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_id'])) {
    $edit_id = $_POST['edit_id']; // Mengambil id akun yang akan diedit
    $new_username = $_POST['new_username']; // Mengambil username baru
    $new_email = $_POST['new_email']; // Mengambil email baru

    // Query untuk mengupdate data akun berdasarkan id
    $sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($sql); // Menyiapkan query
    $stmt->bind_param("ssi", $new_username, $new_email, $edit_id); // Mengikat parameter
    $stmt->execute(); // Menjalankan query untuk update
    $stmt->close(); // Menutup statement setelah eksekusi
    header("Location: kelola_akun.php"); // Redirect ke halaman kelola_akun setelah edit
    exit(); // Hentikan eksekusi lebih lanjut
}

// Menyiapkan query untuk mengambil data akun pengguna
$sql = "SELECT id, username, email FROM users";
$result = $conn->query($sql); // Menjalankan query dan menyimpan hasilnya

$conn->close(); // Menutup koneksi ke database
?>

<!-- Halaman HTML untuk menampilkan form pengelolaan akun -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Akun</title>
    <style>
        /* Styling untuk seluruh halaman */
        body {
            font-family: Arial, sans-serif; /* Mengatur font */
            margin: 0;
            padding: 0;
            background-color: #ffe6f2; /* Latar belakang */
            color: #333; /* Warna teks */
        }

        /* Styling untuk container utama */
        .container {
            width: 90%; /* Lebar container */
            max-width: 1200px; /* Lebar maksimal */
            margin: 50px auto; /* Margin otomatis di kanan dan kiri */
            padding: 20px;
            background-color: #ffffff; /* Latar belakang putih */
            border-radius: 8px; /* Sudut tumpul */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Efek bayangan */
        }

        /* Styling untuk judul halaman */
        h2 {
            text-align: center; /* Rata tengah */
            margin-bottom: 20px;
            color: #fd1b7d; /* Warna teks merah muda */
        }

        /* Styling untuk tabel */
        .table {
            width: 100%;
            border-collapse: collapse; /* Menghapus jarak antar border */
            margin: 20px 0;
        }

        /* Styling untuk header tabel dan data tabel */
        .table th, .table td {
            border: 1px solid #fd1b7d; /* Border merah muda */
            padding: 12px;
            text-align: left;
        }

        .table th {
            background-color: #ffd6e6; /* Latar belakang header tabel */
            color: #fd1b7d; /* Warna teks header tabel */
        }

        /* Efek hover pada baris tabel */
        .table tr:hover {
            background-color: #ffe6f2; /* Mengubah latar belakang saat hover */
        }

        /* Styling untuk tombol utama */
        .btn-primary {
            background-color: #fd1b7d; /* Warna latar belakang tombol */
            color: #fff; /* Warna teks tombol */
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease; /* Efek transisi pada hover */
        }

        /* Efek hover pada tombol utama */
        .btn-primary:hover {
            background-color: #e10a67; /* Mengubah warna tombol saat hover */
        }

        /* Styling untuk tombol hapus */
        .btn-danger {
            background-color: #fd1b7d; /* Warna latar belakang tombol */
            color: #fff; /* Warna teks tombol */
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease; /* Efek transisi pada hover */
        }

        /* Efek hover pada tombol hapus */
        .btn-danger:hover {
            background-color: #e10a67; /* Mengubah warna tombol saat hover */
        }

        /* Styling untuk input form */
        form input[type="text"], form input[type="email"] {
            padding: 8px;
            margin: 4px 0;
            border: 1px solid #ffffff; /* Border putih */
            border-radius: 4px;
            width: auto;
            background-color: #ffe6f2; /* Latar belakang input */
        }

        /* Styling untuk tombol submit form */
        form button {
            margin-top: 4px;
        }
    </style>
</head>
<body>
    <!-- Container untuk halaman Kelola Akun -->
    <div class="container">
        <h2>Kelola Akun Pengguna</h2>

        <!-- Tabel untuk menampilkan data akun -->
        <table class="table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <!-- Loop untuk menampilkan data akun dari database -->
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <!-- Menampilkan data username dan email -->
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td>
                            <!-- Form untuk mengedit data akun -->
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="edit_id" value="<?php echo $row['id']; ?>">
                                <input type="text" name="new_username" value="<?php echo htmlspecialchars($row['username']); ?>" required>
                                <input type="email" name="new_email" value="<?php echo htmlspecialchars($row['email']); ?>" required>
                                <button type="submit" class="btn-primary">Ubah</button>
                            </form>

                            <!-- Link untuk menghapus akun -->
                            <a href="?delete_id=<?php echo $row['id']; ?>" class="btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">Hapus</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Tombol kembali ke dashboard dan logout -->
        <a href="dashboard_admin.php" class="btn-primary">Kembali ke Dashboard</a>
        <a href="logout.php" class="btn-danger">Logout</a>
    </div>
</body>
</html>
