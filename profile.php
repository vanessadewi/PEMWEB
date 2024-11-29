<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$host = 'localhost';
$user = 'root';
$password = ''; 
$dbname = 'perpuspemweb'; 
$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$username = $_SESSION['username'];
$profilePicPath = "uploads/$username.png"; 

$sql = "SELECT email, role, password FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($email, $role, $stored_password);
$stmt->fetch();
$stmt->close();

$output_message = ""; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        $targetFile = $targetDir . $username . ".png";  
        $uploadOk = 1;

        $imageFileType = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));

        $check = getimagesize($_FILES['profile_pic']['tmp_name']);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $output_message = "File yang diunggah bukan gambar.";
            $uploadOk = 0;
        }

        if ($uploadOk && move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetFile)) {
            $output_message = "Foto profil berhasil diunggah!";
        } else {
            $output_message = "Ada masalah dalam mengunggah gambar.";
        }
    } else {
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] != UPLOAD_ERR_NO_FILE) {
            $output_message = "Gagal mengunggah file. Coba lagi.";
        }
    }

    if (isset($_POST['delete_pic'])) {
        if (file_exists($profilePicPath)) {
            unlink($profilePicPath);  
            $output_message = "Foto profil berhasil dihapus!";
        } else {
            $output_message = "Foto profil tidak ditemukan!";
        }
    }

    if (isset($_POST['new_username']) && !empty($_POST['new_username'])) {
        $new_username = htmlspecialchars($_POST['new_username'], ENT_QUOTES, 'UTF-8');
        $sql = "UPDATE users SET username = ? WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $new_username, $username);
        $stmt->execute();
        $_SESSION['username'] = $new_username; 
        $stmt->close();
        $output_message = "Username berhasil diperbarui!";
        $username = $new_username; 
    }

    if (isset($_POST['new_email']) && !empty($_POST['new_email'])) {
        $new_email = htmlspecialchars($_POST['new_email'], ENT_QUOTES, 'UTF-8');
        $sql = "UPDATE users SET email = ? WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $new_email, $username);
        $stmt->execute();
        $_SESSION['email'] = $new_email; 
        $stmt->close();
        $output_message = "Email berhasil diperbarui!";
        $email = $new_email;
    }


    if (isset($_POST['old_password']) && isset($_POST['new_password']) && !empty($_POST['old_password']) && !empty($_POST['new_password'])) {
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];

        if (password_verify($old_password, $stored_password)) {
        
            $new_password_hashed = password_hash($new_password, PASSWORD_BCRYPT);

            $sql = "UPDATE users SET password = ? WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $new_password_hashed, $username);
            $stmt->execute();
            $stmt->close();
            $output_message = "Password berhasil diperbarui!";
        } else {
            $output_message = "Password lama salah!";
        }
    }

    if (isset($_POST['new_role']) && !empty($_POST['new_role']) && $_SESSION['role'] == 'admin') {
        $new_role = htmlspecialchars($_POST['new_role'], ENT_QUOTES, 'UTF-8');
        $sql = "UPDATE users SET role = ? WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $new_role, $username);
        $stmt->execute();
        $stmt->close();
        $output_message = "Role berhasil diperbarui!";
    }
}

if (!file_exists($profilePicPath)) {
    $profilePicPath = "images/default.jpg";  
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link rel="stylesheet" href="style.css">
    <style>body {
    background-color: #f8f8f8; 
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center; 
    align-items: center; 
    min-height: 100vh; 
    font-family: Arial, sans-serif; 
}
.profile-container {
    background: rgba(255, 255, 255, 0.9);
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: row; 
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
    width: 100%;
    max-width: 900px; 
    margin: 0 auto;
}

.profile-left {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 20px;
}

.profile-left img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 15px;
}

.profile-left h2 {
    color: #fd1b7d;
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 5px;
}

.profile-left p {
    color: #333;
    font-size: 0.9rem;
    margin: 5px 0;
}

.profile-right {
    flex: 2;
    display: flex;
    flex-wrap: wrap; 
    gap: 20px;
    justify-content: flex-start;
}

.profile-form {
    flex: 1 1 calc(50% - 20px);
    display: flex;
    flex-direction: column;
    gap: 10px;
    background-color: #f9f9f9;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.profile-form label {
    font-weight: bold;
    color: #333;
}

.profile-form input[type="text"],
.profile-form input[type="email"],
.profile-form input[type="password"],
.profile-form input[type="file"],
.profile-form select {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 0.9rem;
}

.profile-form input[type="submit"] {
    padding: 10px;
    background-color: #fd1b7d;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1rem;
}

.profile-form input[type="submit"]:hover {
    background-color: hotpink;
}

.profile-right button {
    padding: 10px;
    background-color: #fd1b7d;
    color: #f8f8f8;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1rem;
}

.profile-right button:hover {
    background-color: #bbb;
}

/* Responsif */
@media (max-width: 768px) {
    .profile-container {
        flex-direction: column;
    }

    .profile-left img {
        width: 80px;
        height: 80px;
    }

    .profile-right {
        flex-wrap: wrap;
    }

    .profile-form {
        flex: 1 1 100%; 
    }
}

.profile-form-row {
    display: flex;              
    justify-content: space-between; 
    align-items: center;      
    gap: 20px;                 
}

.profile-form-row .form-group {
    flex: 1;                 
    display: flex;
    flex-direction: column;   
    gap: 10px;
}

.profile-form-row input[type="file"],
.profile-form-row input[type="submit"] {
    width: 100%;             
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 0.9rem;
}

.profile-form-row input[type="submit"] {
    background-color: #fd1b7d;
    color: white;
    cursor: pointer;
}

.profile-form-row input[type="submit"]:hover {
    background-color: hotpink;
}</style>
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
                <form class="profile-form profile-form-row" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Unggah Foto Profil:</label>
                <input type="file" name="profile_pic">
                <input type="submit" value="Unggah Foto">
            </div>
            <div class="form-group">
                <input type="submit" name="delete_pic" value="Hapus Foto Profil">
            </div>
        </form>
            <form class="profile-form" method="POST">
                <label>Ganti Username:</label>
                <input type="text" name="new_username" value="<?php echo htmlspecialchars($username); ?>" required>
                <input type="submit" value="Ganti Username">
            </form>

            <form class="profile-form" method="POST">
                <label>Ganti Email:</label>
                <input type="email" name="new_email" value="<?php echo htmlspecialchars($email); ?>" required>
                <input type="submit" value="Ganti Email">
            </form>

            <form class="profile-form" method="POST">
                <label>Password Lama:</label>
                <input type="password" name="old_password" required>
                <label>Password Baru:</label>
                <input type="password" name="new_password" required>
                <input type="submit" value="Ganti Password">
            </form>

            <a href="dashboard.php">
                <button>Kembali ke Dashboard</button>
            </a>
        </div>
    </div>
</div>

</body>
</html>
