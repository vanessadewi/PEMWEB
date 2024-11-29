<?php
// Memulai sesi untuk menangani session variabel (seperti login user)
session_start();

// Menentukan detail koneksi ke database
$host = 'localhost';  // Host tempat database berada
$user = 'user';  // Username untuk mengakses database
$password = '@dunanes_123xxy';  // Password untuk mengakses database
$dbname = 'perpuspemweb';  // Nama database yang digunakan

// Membuat koneksi ke database menggunakan MySQLi
$conn = new mysqli($host, $user, $password, $dbname);

// Memeriksa apakah koneksi ke database berhasil
if ($conn->connect_error) {
    // Jika gagal koneksi, tampilkan pesan error dan hentikan eksekusi program
    die("Connection failed: " . $conn->connect_error);
}

// Memeriksa apakah permintaan yang diterima adalah metode POST (form submit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Menangani data yang dikirimkan melalui form, membersihkan dan mengamankan input
    $username = htmlspecialchars(trim($_POST['username']), ENT_QUOTES, 'UTF-8');  // Mengamankan dan membersihkan input username
    $email = htmlspecialchars(trim($_POST['email']), ENT_QUOTES, 'UTF-8');  // Mengamankan dan membersihkan input email
    $password = $_POST['password'];  // Password tidak perlu di-trim karena akan di-hash
    $role = htmlspecialchars(trim($_POST['role']), ENT_QUOTES, 'UTF-8');  // Mengamankan dan membersihkan input role

    // Memeriksa apakah ada kolom yang kosong
    if (empty($username) || empty($email) || empty($password) || empty($role)) {
        // Jika ada kolom kosong, set error message
        $error = "All fields are required!";
    } else {

        // Memeriksa apakah format email valid
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Jika format email tidak valid, set error message
            $error = "Invalid email format!";
        } else {

            // Query untuk mengecek apakah username sudah ada di database
            $sql_check_username = "SELECT id FROM users WHERE username = ?";
            $stmt_check_username = $conn->prepare($sql_check_username);  // Menyiapkan statement untuk query
            $stmt_check_username->bind_param("s", $username);  // Mengikat parameter untuk username
            $stmt_check_username->execute();  // Menjalankan query
            $stmt_check_username->store_result();  // Menyimpan hasil query

            // Jika ada username yang sama, tampilkan pesan error
            if ($stmt_check_username->num_rows > 0) {
                $error = "Username sudah terpakai! Coba yang lain.";
            } else {

                // Query untuk mengecek apakah email sudah ada di database
                $sql_check_email = "SELECT id FROM users WHERE email = ?";
                $stmt_check_email = $conn->prepare($sql_check_email);  // Menyiapkan statement untuk query
                $stmt_check_email->bind_param("s", $email);  // Mengikat parameter untuk email
                $stmt_check_email->execute();  // Menjalankan query
                $stmt_check_email->store_result();  // Menyimpan hasil query

                // Jika ada email yang sama, tampilkan pesan error
                if ($stmt_check_email->num_rows > 0) {
                    $error = "Email sudah terpakai!";
                } else {

                    // Jika username dan email unik, enkripsi password pengguna
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);  // Menggunakan hashing algoritma PASSWORD_DEFAULT untuk password

                    // Query untuk memasukkan data pengguna baru ke dalam tabel 'users'
                    $sql_insert = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
                    $stmt_insert = $conn->prepare($sql_insert);  // Menyiapkan statement untuk query insert
                    $stmt_insert->bind_param("ssss", $username, $email, $hashed_password, $role);  // Mengikat parameter

                    // Menjalankan query untuk menambahkan data pengguna
                    if ($stmt_insert->execute()) {
                        // Jika berhasil, arahkan pengguna ke halaman login
                        header("Location: login.php");
                        exit();  // Menghentikan eksekusi lebih lanjut
                    } else {
                        // Jika terjadi error saat insert data, tampilkan pesan error
                        $error = "Error: " . $stmt_insert->error;
                    }
                }
                $stmt_check_email->close();  // Menutup statement setelah digunakan
            }
            $stmt_check_username->close();  // Menutup statement setelah digunakan
        }
    }
}

// Menutup koneksi ke database
$conn->close();
?>

<!-- HTML Form untuk registrasi -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Register</title>
</head>
<body>
    <!-- Container untuk form registrasi -->
    <div class="form-container">
        <!-- Form untuk mengirimkan data registrasi -->
        <form action="register.php" method="POST" class="form">
            <h2 class="title">Register</h2>
            <!-- Menampilkan error jika ada -->
            <?php if (isset($error)): ?>
                <!-- Menampilkan pesan error dengan warna merah -->
                <p style="color: red;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            
            <!-- Input field untuk username -->
            <div class="input-field">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="Username" required />
            </div>
            
            <!-- Input field untuk email -->
            <div class="input-field">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email" required />
            </div>
            
            <!-- Input field untuk password -->
            <div class="input-field">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required />
            </div>

            <!-- Dropdown menu untuk memilih role pengguna (user atau admin) -->
            <div class="input-field">
                <label for="role">Role:</label>
                <select name="role" required>
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <!-- Tombol untuk submit form registrasi -->
            <input type="submit" class="btn" value="Register" />

            <!-- Link ke halaman login jika sudah punya akun -->
            <p class="toggle-form">
                Already have an account? <a href="login.php">Login disini</a>
            </p>
        </form>
    </div>
    <!-- Menambahkan ikon fontawesome untuk input ikon di form -->
    <script src="https://kit.fontawesome.com/64d58efce2.js"></script>
</body>
</html>
