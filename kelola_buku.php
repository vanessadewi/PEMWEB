<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$host = 'localhost';
$user = 'user';
$password = '@dunanes_123xxy';
$dbname = 'perpuspemweb';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_book'])) {

    $judul = htmlspecialchars($_POST['judul'], ENT_QUOTES, 'UTF-8');
    $penulis = htmlspecialchars($_POST['penulis'], ENT_QUOTES, 'UTF-8');
    $tahun = intval($_POST['tahun']);
    $genre = htmlspecialchars($_POST['genre'], ENT_QUOTES, 'UTF-8');
    $stok = intval($_POST['stok']);

    $sql = "INSERT INTO buku (judul, penulis, tahun_terbit, genre, stok) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisi", $judul, $penulis, $tahun, $genre, $stok);

    if ($stmt->execute()) {
        header("Location: kelola_buku.php"); 
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']); 
    $sql = "DELETE FROM buku WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        header("Location: kelola_buku.php"); 
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$sql = "SELECT id, judul, penulis, tahun_terbit, genre, stok FROM buku";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Data Buku</title>
    <style>
    body {
    font-family: Arial, sans-serif;
    margin: 20px;
    background-color: #ffe6f2; 
    color: #333; 
}

h2, h3 {
    text-align: center;
    color: #fd1b7d; 
}

.container {
    max-width: 900px;
    margin: 0 auto;
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

form {
    margin-top: 20px;
    padding: 15px;
    background: #ffd6e7;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

form input, form select {
    margin-bottom: 10px;
    padding: 10px;
    width: calc(100% - 22px);
    border: 1px solid #ffe6f2;
    border-radius: 5px;
}

form input[type="submit"] {
    background-color: #fd1b7d;
    color: white;
    border: none;
    cursor: pointer;
    text-transform: uppercase;
    font-weight: bold;
    transition: background-color 0.3s ease;
}

form input[type="submit"]:hover {
    background-color: hotpink;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

table, th, td {
    border: 1px solid #fd1b7d;
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
    background-color: #ffe6f2; 
}

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
    background-color: hotpink;
}

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
    <div class="container">
        <h2>Kelola Data Buku</h2>

        <form action="" method="POST">
            <h3>Tambah Buku Baru</h3>
            <input type="text" name="judul" placeholder="Judul Buku" required>
            <input type="text" name="penulis" placeholder="Penulis" required>
            <input type="number" name="tahun" placeholder="Tahun Terbit" required>
            <input type="text" name="genre" placeholder="Genre" required>
            <input type="number" name="stok" placeholder="Stok Buku" required>
            <input type="submit" name="add_book" value="Tambah Buku">
        </form>

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
                <?php if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['judul'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['penulis'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['tahun_terbit'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['genre'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['stok'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="action-buttons">
                                <a href="edit_buku.php?id=<?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?>">Edit</a>
                                <a href="kelola_buku.php?delete_id=<?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus buku ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php }
                } else { ?>
                    <tr>
                        <td colspan="7">Tidak ada buku dalam database.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <a href="dashboard_admin.php">Kembali ke Dashboard</a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form');

            form.addEventListener('submit', function (event) {
                const inputs = form.querySelectorAll('input[type="text"], input[type="number"]');
                let isValid = true;

                inputs.forEach(input => {
                    const value = input.value.trim();

                    if (/[\<\>\"\'\`]/.test(value)) {
                        alert(`Input "${input.name}" mengandung karakter tidak diperbolehkan.`);
                        isValid = false;
                    }
                });

                if (!isValid) {
                    event.preventDefault();
                }
            });
        });
    </script>
</body>
</html>
