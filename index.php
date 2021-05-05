<?php
    // Connecting to database
    session_start();
    $log = fopen("php://stdout", "w");
    $connection = new mysqli('localhost', 'user', 'password','loginForm');
    if ($connection->connect_error) {
        die("Connection unsuccessful!" . $connection->connect_error);
    }
    fwrite($log,"Connection successful.\n");

    // If the user makes a request to the server
    // User logs in
    if (isset($_POST['login'])) {
        if (empty($_POST['username'])) {
            $_SESSION['output'] = $_SESSION['output'] . "Please enter a username!";
        }
        else if (empty($_POST['password'])) {
            $_SESSION['output'] = $_SESSION['output'] . "Please enter a password!";
        }
        else {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $query = "SELECT pass FROM users WHERE username='$username'";
            $query = $connection->query($query);
            if ($query->num_rows == 1) {
                $user = $query->fetch_assoc();
                if (password_verify($password,$user["pass"])) {
                    $_SESSION['output'] = "Successfully logged in.";
                    $_SESSION['outputType'] = "success";
                    $_SESSION['user'] = $username;
                    $connection->close();
                    header('Location: settings.php');
                }
                else {
                    $_SESSION['output'] = "Incorrect password.";
                }
            }
            else {
                $_SESSION['output'] = "No such user exists.";
            }
        }
    }
    // User logs out
    else if (isset($_POST['logout'])) {
        $_SESSION['output'] = "Logged out!";
        $_SESSION['outputType'] = "success";
        unset($_SESSION['user']);
    }
    $connection->close();
?>
<?php include 'header.php'?>
<div class="container">
    <div id="outputbox"></div>
    <div class="card bg-light">
        <div class="card-body">
            <form id="login" method="post">
                <div class="container text-center">
                    <h3>Login</h3>
                </div>
                <div class="form-group">
                    <label for="username">Username: </label>
                    <input required type="text" name="username" id="username" class="form-control"
                        placeholder="Your Username">
                </div>
                <div class="form-group">
                    <label for="password">Password: </label>
                    <input required type="password" name="password" id="password" class="form-control"
                        placeholder="Your Password">
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
                <a href="register.php" class="btn btn-secondary">Register</a>
            </form>
        </div>
    </div>
</div>
<?php include 'footer.php'?>