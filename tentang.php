<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang</title>
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
        }

        body {
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

        .form-container {
            background-color: rgba(255, 255, 255, 0.8); 
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .navbar {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 10px;
            border-radius: 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: center;
            align-items: center;
            position: fixed;
            top: 0;
            left: 16rem;
            right: 0;

        }

        .navbar h2 {
            color: #fd1b7d;
            margin: 0;
            font-size: 2rem;
            font-weight: 600;
        }

        .navbar nav {
            display: flex;
            gap: 15px;
        }

    .btn {
    padding: 10px 20px;
    font-size: 0.9rem;
    background-color: #fd1b7d;
    color: white;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    text-transform: uppercase;
    text-decoration: none;
    transition: background-color 0.3s ease;
    font-family: 'Poppins', sans-serif;
    font-weight: 500;
}


        .btn:hover {
            background-color: hotpink;
        }

        .navbar .logout-btn {
            margin-right: 10px;
        }

        .form-container {
            margin: 100px auto;
            max-width: 400px;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
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

.sidebar i {
    margin-right: 10px; 
}

    </style>
</head>
<body>
    <div class="navbar">
    <h2>About Me</h2>
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
        <main>
            <h3>Tentang Kami</h3>
            <p>Selamat datang di Perpustakaan, pusat informasi dan literasi yang berkomitmen untuk meningkatkan wawasan dan pengetahuan masyarakat. Kami hadir sebagai tempat yang menyediakan beragam koleksi buku, jurnal, majalah, dan sumber digital yang mencakup berbagai bidang ilmu, baik akademis, literatur, maupun hiburan.</p>
        </main>
    </div>
</body>
</html>
