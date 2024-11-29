<?php
session_start();

$host = 'localhost';
$user = 'user'; 
$password = '@dunanes_123xxy';
$dbname = 'perpuspemweb';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = htmlspecialchars(trim($_POST['username']), ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars(trim($_POST['email']), ENT_QUOTES, 'UTF-8');
    $password = $_POST['password'];
    $role = htmlspecialchars(trim($_POST['role']), ENT_QUOTES, 'UTF-8');  

    if (empty($username) || empty($email) || empty($password) || empty($role)) {
        $error = "All fields are required!";
    } else {

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format!";
        } else {

            $sql_check_username = "SELECT id FROM users WHERE username = ?";
            $stmt_check_username = $conn->prepare($sql_check_username);
            $stmt_check_username->bind_param("s", $username);
            $stmt_check_username->execute();
            $stmt_check_username->store_result();

            if ($stmt_check_username->num_rows > 0) {
                $error = "Username sudah terpakai! Coba yang lain.";
            } else {

                $sql_check_email = "SELECT id FROM users WHERE email = ?";
                $stmt_check_email = $conn->prepare($sql_check_email);
                $stmt_check_email->bind_param("s", $email);
                $stmt_check_email->execute();
                $stmt_check_email->store_result();

                if ($stmt_check_email->num_rows > 0) {
                    $error = "Email sudah terpakai!";
                } else {

                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    $sql_insert = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
                    $stmt_insert = $conn->prepare($sql_insert);
                    $stmt_insert->bind_param("ssss", $username, $email, $hashed_password, $role);

                    if ($stmt_insert->execute()) {
                        header("Location: login.php");
                        exit();
                    } else {
                        $error = "Error: " . $stmt_insert->error;
                    }
                }
                $stmt_check_email->close();
            }
            $stmt_check_username->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Register</title>
</head>
<body>
    <div class="form-container">
        <form action="register.php" method="POST" class="form">
            <h2 class="title">Register</h2>
            <?php if (isset($error)): ?>

                <p style="color: red;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <div class="input-field">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="Username" required />
            </div>
            <div class="input-field">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email" required />
            </div>
            <div class="input-field">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required />
            </div>

            <div class="input-field">
                <label for="role">Role:</label>
                <select name="role" required>
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <input type="submit" class="btn" value="Register" />
            <p class="toggle-form">
                Already have an account? <a href="login.php">Login disini</a>
            </p>
        </form>
    </div>
    <script src="https://kit.fontawesome.com/64d58efce2.js"></script>
</body>
</html>
