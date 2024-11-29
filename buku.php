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

$searchResults = [];
if (isset($_POST['search'])) {
    $keyword = $_POST['keyword'];
    $keyword = $conn->real_escape_string($keyword);

    $sql = "SELECT id, judul, penulis, tahun_terbit, genre, stok FROM buku WHERE judul LIKE '%$keyword%' OR penulis LIKE '%$keyword%'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $searchResults[] = $row;
        }
    }
}

if (isset($_POST['add_to_cart'])) {
    $bookId = $_POST['book_id'];
    $quantity = 1; 

    $sql_user = "SELECT id FROM users WHERE username = ?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("s", $username);
    $stmt_user->execute();
    $user_result = $stmt_user->get_result();
    $user = $user_result->fetch_assoc();
    $user_id = $user['id'];

    $check_sql = "SELECT * FROM keranjang WHERE user_id = ? AND book_id = ?";
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param("ii", $user_id, $bookId);
    $stmt_check->execute();
    $check_result = $stmt_check->get_result();

    if ($check_result->num_rows > 0) {
        $update_sql = "UPDATE keranjang SET quantity = quantity + 1 WHERE user_id = ? AND book_id = ?";
        $stmt_update = $conn->prepare($update_sql);
        $stmt_update->bind_param("ii", $user_id, $bookId);
        $stmt_update->execute();
        $stmt_update->close();
    } else {
        $insert_sql = "INSERT INTO keranjang (user_id, book_id, quantity) VALUES (?, ?, ?)";
        $stmt_insert = $conn->prepare($insert_sql);
        $stmt_insert->bind_param("iii", $user_id, $bookId, $quantity);
        $stmt_insert->execute();
        $stmt_insert->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencarian Buku</title>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800&display=swap");
        @import url("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css");

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
            background-color: rgba(255, 255, 255, 0.8);
            padding: 15px;
            border-radius: 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: center;
            align-items: center;
            position: fixed;
            top: 0;
            left: 15.6rem;
            right: 0;
            z-index: 999;
        }

        .navbar h2 {
            color: #fd1b7d;
            margin: 0;
            font-size: 2rem;
            font-weight: 600;
        }

        .sidebar {
            width: 250px;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 2rem 1rem;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1000;
        }

        .sidebar h2 {
            font-size: 1.8rem;
            color: #fd1b7d;
            margin-bottom: 1rem;
        }

        .sidebar a {
            display: block;
            color: #615e5e;
            text-decoration: none;
            font-size: 1rem;
            margin: 10px 0;
            padding: 10px;
            background-color: rgba(233, 225, 225, 0.5);
            border-radius: 10px;
            transition: background-color 0.3s;
        }

        .sidebar a:hover {
            background-color: #fd1b7d;
            color: white;
        }

        .sidebar i {
            margin-right: 10px;
        }

        .form-container {
            margin: 100px auto;
            max-width: 600px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 10px;
            margin: 15px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

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

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        @media (max-width: 768px) {
            .navbar h2 {
                font-size: 1.5rem;
            }

            .btn {
                font-size: 0.8rem;
            }

            .form-container {
                width: 90%;
                margin-top: 20px;
            }

            .sidebar {
                width: 220px; 
                padding: 1rem; 
        }
    }

        button[type="submit"] {
    padding: 10px 20px;
    font-size: 0.9rem;
    background-color: #fd1b7d;  
    color: white;
    border: none; 
    border-radius: 5px; 
    cursor: pointer;
    text-transform: uppercase; 
    transition: background-color 0.3s ease; 
    font-family: 'Poppins', sans-serif;
    font-weight: 500;
}

button[type="submit"]:hover {
    background-color: hotpink; 
}

    </style>
</head>
<body>
    <div class="navbar">
        <h2>Pencarian Buku</h2>
    </div>

    <div class="sidebar">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
        <a href="dashboard.php"><i class="fas fa-home"></i> Home</a>
        <a href="tentang.php"><i class="fas fa-info-circle"></i> Tentang</a>
        <a href="buku.php"><i class="fas fa-search"></i> Cari Buku</a>
        <a href="keranjang.php"><i class="fas fa-shopping-cart"></i> Keranjang</a>
        <a href="profile.php"><i class="fas fa-user"></i> Profil</a>
        <a href="logout.php"><i class="fas fa-close"></i> Logout</a>
    </div>

    <div class="form-container">
        <h3>Cari Buku</h3>
        <form action="buku.php" method="POST">
            <input type="text" name="keyword" placeholder="Masukkan judul atau penulis" required>
            <button type="submit" name="search" class="btn">Cari</button>
        </form>

        <?php if (isset($_POST['search'])): ?>
            <h3>Hasil Pencarian:</h3>
            <table>
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Penulis</th>
                        <th>Tahun Terbit</th>
                        <th>Stok</th>
                        <th>Tambah ke Keranjang</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($searchResults as $book): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($book['judul']); ?></td>
                            <td><?php echo htmlspecialchars($book['penulis']); ?></td>
                            <td><?php echo htmlspecialchars($book['tahun_terbit']); ?></td>
                            <td><?php echo htmlspecialchars($book['stok']); ?></td>
                            <td>
                                <form action="buku.php" method="POST">
                                    <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                    <button type="submit" name="add_to_cart">Tambah</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
