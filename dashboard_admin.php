<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body, input {
            font-family: "Poppins", sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
            background-color: #e95ba7;
            flex-direction: column;
        }

        .sidebar {
            width: 20%;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 2rem 1rem;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
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

        .main-content {
            flex: 1;
            margin-left: 20%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-image: url('images/background.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .welcome-box {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
        }

        .welcome-box h3 {
            color: #fd1b7d;
            margin-bottom: 1rem;
        }

        .welcome-box p {
            color: #615e5e;
            font-size: 1rem;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
        <a href="dashboard.php"><i class="fas fa-home"></i> Home</a>
        <a href="tentang.php"><i class="fas fa-info-circle"></i> Tentang</a>
        <a href="buku.php"><i class="fas fa-search"></i> Cari Buku</a>
        <a href="keranjang.php"><i class="fas fa-shopping-cart"></i> Keranjang</a>
        <a href="profile.php"><i class="fas fa-user"></i> Profil</a>
        <a href="logout.php"><i class="fas fa-close"></i> Logout</a>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') { ?>
            <hr>
            <h3 style="color: #fd1b7d; margin-top: 10px;">Admin Menu</h3>
            <a href="kelola_akun.php"><i class="fas fa-users-cog"></i> Kelola Akun</a>
            <a href="kelola_buku.php"><i class="fas fa-book"></i> Kelola Buku</a>
        <?php } ?>
    </div>

    <div class="main-content">
        <div class="welcome-box">
            <h3>Dashboard</h3>
            <p>Selamat datang di dashboard Anda, <?php echo htmlspecialchars($_SESSION['username']); ?>! Di sini Anda dapat mengelola pengaturan, melihat profil, dan banyak lagi.</p>
        </div>
    </div>
</body>
</html>
