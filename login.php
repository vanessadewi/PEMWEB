<?php
session_start(); // Memulai session untuk melacak data pengguna yang sudah login.

$host = 'localhost'; // Alamat server database
$user = 'user'; // Username untuk mengakses database
$password = '@dunanes_123xxy'; // Password untuk mengakses database
$dbname = 'perpuspemweb'; // Nama database yang digunakan

// Membuat koneksi ke database MySQL dengan kredensial yang telah diberikan
$conn = new mysqli($host, $user, $password, $dbname);

// Mengecek apakah ada kesalahan dalam koneksi database
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // Menampilkan pesan error dan menghentikan eksekusi jika gagal terhubung
}

// Mengecek apakah session 'username' sudah ada (menandakan pengguna sudah login)
if (isset($_SESSION['username'])) {
    // Jika pengguna sudah login dan memiliki role 'admin', arahkan ke halaman dashboard admin
    if ($_SESSION['role'] === 'admin') {
        header("Location: dashboard_admin.php");
    } else {
        // Jika pengguna memiliki role selain 'admin', arahkan ke halaman dashboard biasa
        header("Location: dashboard.php");
    }
    exit(); // Menghentikan eksekusi kode lebih lanjut setelah pengalihan
}

// Mengecek apakah metode request adalah POST (artinya form login telah dikirim)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mengambil input username dan password dari form dan menyanitasi input username untuk mencegah XSS
    $username = htmlspecialchars(trim($_POST['username']), ENT_QUOTES, 'UTF-8'); // Sanitasi input username
    $password = $_POST['password']; // Password tidak perlu disanitasi, karena hanya akan dicocokkan di database

    // Query untuk mengambil data pengguna berdasarkan username
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql); // Menyiapkan query untuk eksekusi
    $stmt->bind_param("s", $username); // Mengikat parameter username ke query
    $stmt->execute(); // Menjalankan query
    $result = $stmt->get_result(); // Mendapatkan hasil eksekusi query

    // Mengecek apakah ada pengguna yang ditemukan dengan username tersebut
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc(); // Mengambil data pengguna sebagai array
        // Memverifikasi apakah password yang dimasukkan cocok dengan password yang disimpan di database
        if (password_verify($password, $user['password'])) {
            // Jika password benar, set session untuk username dan role pengguna
            $_SESSION['username'] = $username; // Menyimpan username ke dalam session
            $_SESSION['role'] = $user['role']; // Menyimpan role pengguna (admin atau user) ke dalam session

            // Mengarahkan pengguna ke halaman yang sesuai dengan role-nya
            if ($user['role'] === 'admin') {
                header("Location: dashboard_admin.php"); // Halaman dashboard untuk admin
            } else {
                header("Location: dashboard.php"); // Halaman dashboard untuk pengguna biasa
            }
            exit(); // Menghentikan eksekusi lebih lanjut setelah pengalihan
        } else {
            // Jika password tidak cocok, beri pesan error
            $error = "Password salah!"; // Pesan error jika password yang dimasukkan salah
        }
    } else {
        // Jika username tidak ditemukan, beri pesan error
        $error = "Username tidak ditemukan!"; // Pesan error jika username tidak ada dalam database
    }

    $stmt->close(); // Menutup statement query setelah eksekusi
}

// Menutup koneksi ke database
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css"> <!-- Menyertakan file CSS untuk styling -->
    <title>Login</title> <!-- Judul halaman login -->
</head>
<body>
    <!-- Form untuk login pengguna -->
    <div class="form-container">
        <form action="login.php" method="POST" class="form">
            <h2 class="title">Login</h2>
            <!-- Menampilkan pesan error jika ada -->
            <?php if (isset($error)): ?>
                <p style="color: red;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p> <!-- Menampilkan pesan error dalam teks merah -->
            <?php endif; ?>
            <!-- Input untuk username -->
            <div class="input-field">
                <i class="fas fa-user"></i> <!-- Ikon untuk input username -->
                <input type="text" name="username" placeholder="Username" required> <!-- Field untuk input username -->
            </div>
            <!-- Input untuk password -->
            <div class="input-field">
                <i class="fas fa-lock"></i> <!-- Ikon untuk input password -->
                <input type="password" name="password" placeholder="Password" required> <!-- Field untuk input password -->
            </div>
            <!-- Tombol submit untuk login -->
            <input type="submit" value="Login" class="btn solid">
            <!-- Link untuk mengarahkan pengguna ke halaman registrasi jika belum punya akun -->
            <p class="toggle-form">
                Belum punya akun? <a href="register.php">Daftar di sini</a> <!-- Link menuju halaman register -->
            </p>
        </form>
    </div>
    <!-- Menyertakan script untuk font-awesome icons -->
    <script src="https://kit.fontawesome.com/64d58efce2.js"></script>
</body>
</html>
