<!-- Scripts running at the start of every page-->
<?php
    // Connecting to database
    $log = fopen("php://stdout", "w");
    $connection = new mysqli('localhost', 'user', 'password');
    if ($connection->connect_error) {
        die("Connection unsuccessful!" . $connection->connect_error);
    }
    fwrite($log,"Connection successful.\n");
    
    // Creating the database and tables if they don't already exist
    $database = "CREATE DATABASE loginForm";
    if ($connection->query($database) === TRUE) {
        fwrite($log,"Database created.\n");
    } else {
        fwrite($log,mysqli_error($connection)."\n");
    }
    $connection->query("USE loginForm");
    $users = "CREATE TABLE users (
        username VARCHAR(50) NOT NULL PRIMARY KEY,
        email VARCHAR(80) NOT NULL,
        pass VARCHAR(128) NOT NULL,
        registered TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    if ($connection->query($users) === TRUE) {
        fwrite($log,"User table created.\n");
    }
    $connection->close();
?>

<!-- Navbar shown at the top of every page -->
<!DOCTYPE html>
<html>
    <head>
        <title>User Panel</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="public/style.css"/>
    </head>
    <body>
        <nav class="navbar bg-dark text-white text-center">
            <div>User Panel</div>
            
            <!-- <form method="post">
                <button type="submit" name="logout" style="background: none; border: none; color: white;">Log Out</button>
            </form> -->
        </nav>