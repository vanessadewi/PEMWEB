<?php 
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
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

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: kelola_akun.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_id'])) {
    $edit_id = $_POST['edit_id'];
    $new_username = $_POST['new_username'];
    $new_email = $_POST['new_email'];

    $sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $new_username, $new_email, $edit_id);
    $stmt->execute();
    $stmt->close();
    header("Location: kelola_akun.php");
    exit();
}

$sql = "SELECT id, username, email FROM users";
$result = $conn->query($sql);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Akun</title>
    <style>

body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #ffe6f2; 
    color: #333;
}

.container {
    width: 90%;
    max-width: 1200px;
    margin: 50px auto;
    padding: 20px;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #fd1b7d; 
}

.table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
}

.table th, .table td {
    border: 1px solid #fd1b7d;
    padding: 12px;
    text-align: left;
}

.table th {
    background-color: #ffd6e6; 
    color: #fd1b7d; 
}

.table tr:hover {
    background-color: #ffe6f2; 
}

.btn-primary {
    background-color: #fd1b7d; 
    color: #fff;
    padding: 8px 12px;
    border: none;
    border-radius: 4px;
    text-decoration: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-primary:hover {
    background-color: #e10a67; 
}

.btn-danger {
    background-color: #fd1b7d; 
    color: #fff;
    padding: 8px 12px;
    border: none;
    border-radius: 4px;
    text-decoration: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-danger:hover {
    background-color: #e10a67; 
}

form input[type="text"], form input[type="email"] {
    padding: 8px;
    margin: 4px 0;
    border: 1px solid #ffffff; 
    border-radius: 4px;
    width: auto;
    background-color: #ffe6f2; 
}

form button {
    margin-top: 4px;
}

    </style>
</head>
<body>
    <div class="container">
        <h2>Kelola Akun Pengguna</h2>

        <table class="table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="edit_id" value="<?php echo $row['id']; ?>">
                                <input type="text" name="new_username" value="<?php echo htmlspecialchars($row['username']); ?>" required>
                                <input type="email" name="new_email" value="<?php echo htmlspecialchars($row['email']); ?>" required>
                                <button type="submit" class="btn-primary">Ubah</button>
                            </form>

                            <a href="?delete_id=<?php echo $row['id']; ?>" class="btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">Hapus</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <a href="dashboard_admin.php" class="btn-primary">Kembali ke Dashboard</a>
        <a href="logout.php" class="btn-danger">Logout</a>
    </div>
</body>
</html>
