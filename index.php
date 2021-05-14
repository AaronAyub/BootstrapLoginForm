<?php
    // Connecting to database
    session_start();
    $log = fopen("php://stdout", "w");
    include 'phpUtils.php';
    $connection = connect();

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
            $st = $connection->prepare("SELECT pass FROM users WHERE username=?");
            $st->bind_param("s",$_POST['username']);
            $st->execute();
            $result = $st->get_result();
            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                if (password_verify($_POST['password'],$user["pass"])) {
                    $_SESSION['output'] = "Successfully logged in.";
                    $_SESSION['outputType'] = "success";
                    $_SESSION['user'] = $_POST['username'];
                    // If the user wants to be remember, create persistent session in the database, and a give the user a session token
                    if (isset($_POST['remember'])) {
                        $st->close();
                        // Make an entry in the logins table to save this login
                        $token = bin2hex(random_bytes(64));
                        $st = $connection->prepare("INSERT INTO logins (token, username) VALUES (?,?)");
                        $st->bind_param("ss",$token,$_POST['username']);
                        $st->execute();
                        // And add that token as a cookie
                        setcookie("session_token",$token,time() + (86400 * 14), "."); // Keep the user logged in for 14 days
                    }
                    session_write_close(); // Write session data before redirecting
                    header('Location: settings.php');
                }
                else {
                    $_SESSION['output'] = "Incorrect password.";
                }
            }
            else {
                $_SESSION['output'] = "No such user exists.";
            }
            $st->close();
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