<?php
session_start();
session_unset(); 
session_destroy(); 
header("Location: login.php"); 
exit();
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Logout</title>
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <h2>You have logged out!</h2>
    <a href="login.html">Login again</a>

    <script src="script.js"></script>
  </body>
</html>
