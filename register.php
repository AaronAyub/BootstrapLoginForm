<?php
    // Connecting to database
    session_start();
    $log = fopen("php://stdout", "w");
    include 'phpUtils.php';
    $connection = connect();

    // User registers for a new account
    if (isset($_POST['register'])) {
        if (empty($_POST['username'])) {
            $_SESSION['output'] = $_SESSION['output'] . "Please enter a username!";
        }
        else if (empty($_POST['password'])) {
            $_SESSION['output'] = $_SESSION['output'] . "Please enter a password!";
        }
        else if (empty($_POST['email'])) {
            $_SESSION['output'] = $_SESSION['output'] . "Please enter an email address!";
        }
        else if ($_POST['password'] != $_POST['confirm']) {
            $_SESSION['output'] = $_SESSION['output'] . "Passwords do not match!";
        }
        else if (empty($_POST['readTerms'])) {
            $_SESSION['output'] = $_SESSION['output'] . "To register, you must read and accept the terms and conditions.";
        }
        else { // Otherwise, the user has been created
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'],PASSWORD_DEFAULT);
            $add = "INSERT INTO users (username, email, pass)
            VALUES ('$username', '$email', '$password')";
            $query = "SELECT username FROM users WHERE username='$username'";
            $query = $connection->query($query);
            if ($query->num_rows > 0) {
                $_SESSION['output'] = "Sorry, the username $username is already taken!";
            }
            else if ($connection->query($add) === TRUE) {
                $_SESSION['output'] = "User successfully added!";
                $_SESSION['outputType'] = "success";
                $_SESSION['user'] = $username;
                $connection->close();
                header('Location: settings.php');
            }
            else {
                fwrite($log,"Can't create user!\n" . mysqli_error($connection));
            }
        }
    }
    $connection->close();
?>
<?php include 'header.php'?>
<div class="container">
    <div id="outputbox"></div>
    <div class="card bg-light">
        <div class="card-body">
            <form id="register" method="post">
                <div class="container text-center">
                    <h3>Register</h3>
                </div>
                <div class="form-group">
                    <label for="username">Username: </label>
                    <input required type="text" name="username" id="username" class="form-control"
                        placeholder="Your Username">
                </div>
                <div class="form-group">
                    <label for="email">Email: </label>
                    <input required type="email" name="email" id="email" class="form-control"
                        placeholder="Your Email Address">
                </div>
                <div class="form-group">
                    <label for="password">Password: </label>
                    <input required minlength="6" name="password" type="password" id="password" class="form-control"
                        placeholder="Your Password">
                </div>
                <div class="form-group">
                    <label for="confirm">Confirm Password: </label>
                    <input required type="password" name="confirm" id="confirm" class="form-control"
                        placeholder="Confirm Password">
                </div>
                <div class="form-group form-check">
                    <input type="checkbox" name="subscribe" class="form-check-input" id="newsletter">
                    <label class="form-check-label" for="newsletter">Subscribe to the newsletter (Optional)</label>
                </div>
                <div class="form-group form-check">
                    <input required type="checkbox" name="readTerms" class="form-check-input" id="conditions">
                    <label class="form-check-label" for="conditions">I have read and agree to the Terms and
                        Conditions</label>
                </div>
                <button type="submit" name="register" class="btn btn-primary">Register</button>
            </form>
            <hr>
            <a href="index.php" class="btn btn-secondary">Sign In</a>
        </div>
    </div>
</div>
<?php include 'footer.php'?>