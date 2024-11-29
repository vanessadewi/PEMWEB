<?php
// Memulai sesi untuk mengakses variabel sesi seperti username
session_start();

// Mengecek apakah session 'username' ada, jika tidak ada maka arahkan ke halaman login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit(); // Menghentikan eksekusi lebih lanjut jika user belum login
}

// Menyiapkan koneksi ke database
$host = 'localhost'; // Nama host database
$user = 'root'; // Username database
$password = ''; // Password database
$dbname = 'perpuspemweb'; // Nama database yang digunakan

// Membuat koneksi ke database
$conn = new mysqli($host, $user, $password, $dbname);

// Mengecek apakah koneksi ke database berhasil atau tidak
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error); // Jika gagal, tampilkan pesan error
}

// Menyimpan username yang terautentikasi dalam variabel
$username = $_SESSION['username'];
// Menentukan path file gambar profil berdasarkan username
$profilePicPath = "uploads/$username.png"; 

// Menyiapkan query untuk mengambil email, role, dan password dari database
$sql = "SELECT email, role, password FROM users WHERE username = ?";
$stmt = $conn->prepare($sql); // Menyiapkan statement query
$stmt->bind_param("s", $username); // Mengikat parameter untuk query
$stmt->execute(); // Menjalankan query
$stmt->bind_result($email, $role, $stored_password); // Mengambil hasil query ke variabel
$stmt->fetch(); // Mengambil satu hasil (baris) dari query
$stmt->close(); // Menutup statement query

$output_message = ""; // Variabel untuk menyimpan pesan output

// Memeriksa apakah ada permintaan POST (form di-submit)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Memeriksa jika ada file gambar profil yang diunggah
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == UPLOAD_ERR_OK) {
        // Menentukan direktori tempat menyimpan gambar profil
        $targetDir = "uploads/";
        $targetFile = $targetDir . $username . ".png";  
        $uploadOk = 1;

        // Mengecek apakah file yang diunggah adalah gambar
        $imageFileType = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
        $check = getimagesize($_FILES['profile_pic']['tmp_name']);
        if ($check !== false) {
            $uploadOk = 1; // Menandakan gambar valid
        } else {
            $output_message = "File yang diunggah bukan gambar."; // Pesan jika file bukan gambar
            $uploadOk = 0; // Menandakan upload gagal
        }

        // Jika gambar valid, pindahkan file ke direktori tujuan
        if ($uploadOk && move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetFile)) {
            $output_message = "Foto profil berhasil diunggah!"; // Pesan jika berhasil diunggah
        } else {
            $output_message = "Ada masalah dalam mengunggah gambar."; // Pesan jika ada kesalahan
        }
    } else {
        // Jika tidak ada file yang diunggah
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] != UPLOAD_ERR_NO_FILE) {
            $output_message = "Gagal mengunggah file. Coba lagi."; // Pesan jika gagal mengunggah
        }
    }

    // Memeriksa jika pengguna ingin menghapus foto profil
    if (isset($_POST['delete_pic'])) {
        // Mengecek apakah file gambar profil ada di server
        if (file_exists($profilePicPath)) {
            unlink($profilePicPath);  // Menghapus file gambar profil
            $output_message = "Foto profil berhasil dihapus!"; // Pesan jika berhasil dihapus
        } else {
            $output_message = "Foto profil tidak ditemukan!"; // Pesan jika file tidak ditemukan
        }
    }

    // Memeriksa jika pengguna ingin mengganti username
    if (isset($_POST['new_username']) && !empty($_POST['new_username'])) {
        $new_username = htmlspecialchars($_POST['new_username'], ENT_QUOTES, 'UTF-8'); // Melakukan sanitasi input
        $sql = "UPDATE users SET username = ? WHERE username = ?"; // Query untuk memperbarui username
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $new_username, $username); // Mengikat parameter
        $stmt->execute(); // Menjalankan query
        $_SESSION['username'] = $new_username;  // Memperbarui session username
        $stmt->close(); // Menutup statement query
        $output_message = "Username berhasil diperbarui!"; // Pesan jika berhasil diperbarui
        $username = $new_username; // Memperbarui variabel username
    }

    // Memeriksa jika pengguna ingin mengganti email
    if (isset($_POST['new_email']) && !empty($_POST['new_email'])) {
        $new_email = htmlspecialchars($_POST['new_email'], ENT_QUOTES, 'UTF-8'); // Sanitasi input email
        $sql = "UPDATE users SET email = ? WHERE username = ?"; // Query untuk memperbarui email
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $new_email, $username); // Mengikat parameter
        $stmt->execute(); // Menjalankan query
        $_SESSION['email'] = $new_email; // Memperbarui session email
        $stmt->close(); // Menutup statement query
        $output_message = "Email berhasil diperbarui!"; // Pesan jika berhasil diperbarui
        $email = $new_email; // Memperbarui variabel email
    }

    // Memeriksa jika pengguna ingin mengganti password
    if (isset($_POST['old_password']) && isset($_POST['new_password']) && !empty($_POST['old_password']) && !empty($_POST['new_password'])) {
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];

        // Memeriksa apakah password lama yang dimasukkan sesuai dengan yang ada di database
        if (password_verify($old_password, $stored_password)) {
            // Jika benar, mengenkripsi password baru
            $new_password_hashed = password_hash($new_password, PASSWORD_BCRYPT);

            // Query untuk memperbarui password
            $sql = "UPDATE users SET password = ? WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $new_password_hashed, $username); // Mengikat parameter
            $stmt->execute(); // Menjalankan query
            $stmt->close(); // Menutup statement query
            $output_message = "Password berhasil diperbarui!"; // Pesan jika berhasil diperbarui
        } else {
            $output_message = "Password lama salah!"; // Pesan jika password lama salah
        }
    }

    // Memeriksa jika admin ingin mengganti role pengguna
    if (isset($_POST['new_role']) && !empty($_POST['new_role']) && $_SESSION['role'] == 'admin') {
        $new_role = htmlspecialchars($_POST['new_role'], ENT_QUOTES, 'UTF-8'); // Sanitasi input role
        $sql = "UPDATE users SET role = ? WHERE username = ?"; // Query untuk memperbarui role
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $new_role, $username); // Mengikat parameter
        $stmt->execute(); // Menjalankan query
        $stmt->close(); // Menutup statement query
        $output_message = "Role berhasil diperbarui!"; // Pesan jika role berhasil diperbarui
    }
}

// Mengecek jika file gambar profil tidak ditemukan, maka menggunakan gambar default
if (!file_exists($profilePicPath)) {
    $profilePicPath = "images/default.jpg";  
}

// Menutup koneksi ke database
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* CSS Styling goes here */
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-container">
            <div class="profile-left">
                <img src="<?php echo htmlspecialchars($profilePicPath); ?>" alt="Foto Profil">
                <h2><?php echo htmlspecialchars($username); ?></h2>
                <p>Email: <?php echo htmlspecialchars($email); ?></p>
                <p>Role: <?php echo htmlspecialchars($role); ?></p>
            </div>
            <div class="profile-right">
                <!-- Form to change profile picture, username, email, and password -->
            </div>
        </div>
    </div>
</body>
</html>
