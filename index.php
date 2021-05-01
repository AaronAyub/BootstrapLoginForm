<?php
    session_start();
    $log = fopen("php://stdout", "w");
    $connection = new mysqli('localhost', 'user', 'password');
    if ($connection->connect_error) {
        die("Connection unsuccessful!" . $connection->connect_error);
    }
    fwrite($log,"Connection successful.\n");
    
    $database = "CREATE DATABASE loginForm";
    if ($connection->query($database) === TRUE) {
        fwrite($log,"Database created.\n");
    } else {
        fwrite($log,"Database not created.\n" . mysqli_error($connection));
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

    $output = ""; // Output is the message to send to the user, if any.

    if (isset($_POST['login'])) {
        fwrite($log,"Login request.\n");
    }

    if (isset($_POST['register'])) {
        fwrite($log,"Registration request.\n");
        if (empty($_POST['username'])) {
            $output = $output . "Please enter a username!";
        }
        else if (empty($_POST['password'])) {
            $output = $output . "Please enter a password!";
        }
        else if (empty($_POST['email'])) {
            $output = $output . "Please enter an email address!";
        }
        else if ($_POST['password'] != $_POST['confirm']) {
            $output = $output . "Passwords do not match!";
        }
        else if (empty($_POST['readTerms'])) {
            $output = $output . "To register, you must read and accept the terms and conditions.";
        }
        else { // Otherwise, the user has been created
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'],PASSWORD_DEFAULT);
            $add = "INSERT INTO users (username, email, pass)
            VALUES ('$username', '$email', '$password')";
            $dup = "SELECT username FROM users WHERE username='$username'";
            $query = $connection->query($dup);
            if ($query->num_rows > 0) {
                $output = "Sorry, the username $username is already taken!";
            }
            else if ($connection->query($add) === TRUE) {
                $output = "User successfully added!";
            }
            else {
                fwrite($log,"Can't create user!\n" . mysqli_error($connection));
            }
        }
    }

    if ($output != "") { // If there was any error, let the user know
        echo $output;
    }

    $connection->close();
?>
<!DOCTYPE html>
<html>
    <head>
        <script src="scripts.js"></script>
        <title>Login Form</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="style.css"/>
    </head>
    <body>
        <nav class="navbar navbar-expand-sm bg-dark text-white text-center justify-content-center">
            Login Form<br>
            Aaron Ayub
        </nav>
        <div class="container">
            <div class="card bg-light">
                <div class="card-body">
                    <form id="login" method="post" action="index.php">
                        <div class="container text-center">
                            <h3>Login</h3>
                        </div>
                        <div class="form-group">
                            <label for="username">Username: </label>
                            <input required type="text" name="username" id="username" class="form-control" placeholder="Your Username">
                        </div>
                        <div class="form-group">
                            <label for="password">Password: </label>
                            <input required type="password" name="password" id="password" class="form-control" placeholder="Your Password">
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" name="hide" class="form-check-input" id="hide">
                            <label class="form-check-label" for="hide">Hide my online status</label>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" name="remember" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        <button type="submit" name="login" class="btn btn-primary">Login</button>
                        <button class="btn btn-secondary" onclick="toggleView(1)">Sign Up</button>
                    </form>

                    <form id="register" method="post">
                        <div class="container text-center">
                            <h3>Register</h3>
                        </div>
                        <div class="form-group">
                            <label for="username">Username: </label>
                            <input required type="text" name="username" id="username" class="form-control" placeholder="Your Username">
                        </div>
                        <div class="form-group">
                            <label for="email">Email: </label>
                            <input required type="email" name="email" id="email" class="form-control" placeholder="Your Email Address">
                        </div>
                        <div class="form-group">
                            <label for="password">Password: </label>
                            <input required minlength="6" name="password" type="password" id="password" class="form-control" placeholder="Your Password">
                        </div>
                        <div class="form-group">
                            <label for="confirm">Confirm Password: </label>
                            <input required type="password" name="confirm" id="confirm" class="form-control" placeholder="Confirm Password">
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" name="subscribe" class="form-check-input" id="newsletter">
                            <label class="form-check-label" for="newsletter">Subscribe to the newsletter (Optional)</label>
                        </div>
                        <div class="form-group form-check">
                            <input required type="checkbox" name="readTerms" class="form-check-input" id="conditions">
                            <label class="form-check-label" for="conditions">I have read and agree to the Terms and Conditions</label>
                        </div>
                        <button type="submit" name="register" class="btn btn-primary">Register</button>
                        <button class="btn btn-secondary" onclick="toggleView(0)">Log In</button>
                    </form>
                </div>
            </div>
        </div>
        <footer class="footer bg-light">
            This website is just a demonstration of html, javascript, css, php, and bootstrap. There is no service to log into or register for here.
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>
    </body>
</html>