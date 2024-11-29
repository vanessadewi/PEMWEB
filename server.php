<?php
session_start();

$host = 'localhost';
$user = 'user'; 
$password = '@dunanes_123xxy';
$dbname = 'perpuspemweb';
$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    die("Anda harus login terlebih dahulu.");
}

if (isset($_GET['add_to_cart'])) {
    $book_id = intval($_GET['add_to_cart']);
    $user_id = intval($_SESSION['user_id']); 

    $sql_check = "SELECT * FROM keranjang WHERE user_id = ? AND book_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ii", $user_id, $book_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows == 0) {
        $sql_insert = "INSERT INTO keranjang (user_id, book_id) VALUES (?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ii", $user_id, $book_id);

        if ($stmt_insert->execute()) {
            echo "Buku berhasil ditambahkan ke keranjang!";
        } else {
            echo "Error: " . $stmt_insert->error;
        }
    } else {
        echo "Buku sudah ada di keranjang!";
    }
}

if (isset($_GET['hapus'])) {
    $keranjang_id = intval($_GET['hapus']);
    $user_id = intval($_SESSION['user_id']);
    $sql_delete = "DELETE FROM keranjang WHERE id = ? AND user_id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("ii", $keranjang_id, $user_id);

    if ($stmt_delete->execute()) {
        echo "Buku berhasil dihapus dari keranjang!";
    } else {
        echo "Error: " . $stmt_delete->error;
    }
}

$user_id = intval($_SESSION['user_id']);
$sql_display = "SELECT b.id, b.judul, b.penulis, k.id as keranjang_id 
                FROM keranjang k 
                JOIN buku b ON k.book_id = b.id
                WHERE k.user_id = ?";
$stmt_display = $conn->prepare($sql_display);
$stmt_display->bind_param("i", $user_id);
$stmt_display->execute();
$result_display = $stmt_display->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Keranjang Buku</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #fd1b7d;
            color: white;
        }
        .btn {
            padding: 5px 10px;
            background-color: #fd1b7d;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn:hover {
            background-color: hotpink;
        }
    </style>
</head>
<body>

<div class="navbar">
    <h2>Keranjang Buku</h2>
    <nav>
        <a href="dashboard.php" class="btn">Home</a>
        <a href="logout.php" class="btn logout-btn">Logout</a>
    </nav>
</div>

<div class="form-container">
    <h3>Keranjang Buku Anda</h3>

    <?php if ($result_display->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Judul Buku</th>
                    <th>Penulis</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_display->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['keranjang_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['judul']); ?></td>
                        <td><?php echo htmlspecialchars($row['penulis']); ?></td>
                        <td>
                            <a href="keranjang.php?hapus=<?php echo $row['keranjang_id']; ?>" class="btn">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Keranjang Anda kosong.</p>
    <?php endif; ?>

    <br>
    <a href="tambah_buku.php" class="btn">Tambah Buku Lagi</a>
</div>

</body>
</html>

<?php
$conn->close();
?>
