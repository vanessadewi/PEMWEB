<?php 
session_start();

if (!isset($_SESSION['username'])) {
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

$username = $_SESSION['username'];

$sql_user = "SELECT id FROM users WHERE username = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("s", $username);
$stmt_user->execute();
$user_result = $stmt_user->get_result();
$user = $user_result->fetch_assoc();
$user_id = $user['id'];

if (isset($_GET['hapus'])) {
    $keranjang_id = intval($_GET['hapus']);
    if ($keranjang_id > 0) {
        $sql = "DELETE FROM keranjang WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $keranjang_id, $user_id);
        $stmt->execute();
        $stmt->close();
    }
}

$sql = "SELECT k.id, b.judul, b.penulis, k.quantity
        FROM keranjang k
        JOIN buku b ON k.book_id = b.id
        WHERE k.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Buku</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Poppins", sans-serif;
            background-image: url('images/background.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        .navbar {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px 40px;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .navbar h2 {
            color: #fd1b7d;
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
        }

        .navbar nav {
            display: flex;
            gap: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 60px; 
            background-color: #fff;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px; 
            text-align: left;
        }

        th {
            background-color: #fd1b7d;
            color: white;
        }

        .btn {
            padding: 8px 16px;
            font-size: 0.85rem;
            background-color: #fd1b7d;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            text-transform: uppercase;
            text-decoration: none;
            transition: background-color 0.3s ease;
            font-weight: 500;
        }

        .btn:hover {
            background-color: hotpink;
        }

        .form-container {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
            margin: 100px auto;
        }
    </style>
</head>
<body>

<div class="navbar">
    <h2>Perpus Pemweb</h2>
    <nav>
        <a href="dashboard.php" class="btn">Home</a>
        <a href="buku.php" class="btn">Cari Buku</a>
        <a href="logout.php" class="btn">Logout</a>
    </nav>
</div>

<div class="form">
    <h3>Keranjang Buku Anda</h3>

    <?php if ($result->num_rows == 0): ?>
        <p>Keranjang Anda kosong.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Judul Buku</th>
                    <th>Penulis</th>
                    <th>Jumlah</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while ($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($row['judul']); ?></td>
                    <td><?php echo htmlspecialchars($row['penulis']); ?></td>
                    <td><?php echo $row['quantity']; ?></td>
                    <td>
                        <a href="keranjang.php?hapus=<?php echo $row['id']; ?>" class="btn">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
