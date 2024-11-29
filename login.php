<?php
session_start();

$host = 'localhost';
$user = 'user'; 
$password = '@dunanes_123xxy';
$dbname = 'perpuspemweb';

// Koneksi ke database
$conn = new mysqli($host, $user, $password, $dbname);

// Cek koneksi ke database
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Jika pengguna sudah login, arahkan ke dashboard sesuai role
if (isset($_SESSION['username'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: dashboard_admin.php");
    } else {
        header("Location: dashboard.php");
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil dan sanitasi input pengguna untuk mencegah XSS
    $username = htmlspecialchars(trim($_POST['username']), ENT_QUOTES, 'UTF-8');
    $password = $_POST['password']; // password tidak perlu disanitasi secara khusus

    // Query untuk mengambil data pengguna berdasarkan username
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verifikasi password dengan password_verify
        if (password_verify($password, $user['password'])) {
            // Set session pengguna
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $user['role']; // Menyimpan role (admin atau user) di sesi

            // Arahkan berdasarkan role
            if ($user['role'] === 'admin') {
                header("Location: dashboard_admin.php"); // Halaman khusus admin
            } else {
                header("Location: dashboard.php"); // Halaman pengguna biasa
            }
            exit();
        } else {
            $error = "Password salah!";  // Pesan error untuk password yang salah
        }
    } else {
        $error = "Username tidak ditemukan!";  // Pesan error untuk username yang tidak ditemukan
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
</head>
<body>
    <div class="form-container">
        <form action="login.php" method="POST" class="form">
            <h2 class="title">Login</h2>
            <?php if (isset($error)): ?>

                <p style="color: red;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <div class="input-field">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="input-field">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <input type="submit" value="Login" class="btn solid">
            <p class="toggle-form">
                Belum punya akun? <a href="register.php">Daftar di sini</a>
            </p>
        </form>
    </div>
    <script src="https://kit.fontawesome.com/64d58efce2.js"></script>
</body>
</html>
