<?php
    // Connecting to database
    $log = fopen("php://stdout", "w");
    include 'phpUtils.php';
    $connection = connect();
    initSession($connection);

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
            $st = $connection->prepare("SELECT username FROM users WHERE username=?");
            $st->bind_param("s",$username);
            $st->execute();
            if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
                $_SESSION['output'] = "Please enter a valid email address.";
            }
            else if (!preg_match('/^[a-zA-Z0-9 _\-]+$/',$username)) {
                $_SESSION['output'] = "Please enter a valid username. You may use any alphanumeric characters, spaces, underscores, and dashes.";
            }
            else if ($st->get_result()->num_rows != 0) { // If the username is taken
                $_SESSION['output'] = "Sorry, the username $username is already taken!";
            }
            else {
                $st->close();
                $st = $connection->prepare("INSERT INTO users (username, email, pass) VALUES (?,?,?)");
                $st->bind_param("sss",$username,$email,$password);
                if ($st->execute()) {
                    $_SESSION['output'] = "User successfully added!";
                    $_SESSION['outputType'] = "success";
                    $_SESSION['user'] = $username;
                    session_write_close(); // Write session data before redirecting
                    header('Location: settings.php');
                }
                else { // If the username is free, but the query was unsuccessful
                    $_SESSION['output'] = "Couldn't create user!";
                    fwrite($log,"Can't create user!\n" . mysqli_error($connection));
                }
            }
            
            $st->close();
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
                    <label class="form-check-label" for="conditions">I have read and agree to the </label>
                    <span data-bs-toggle="modal" data-bs-target="#termsModal" style="color: blue">Terms and Conditions</span>
                </div>
                <button type="submit" name="register" class="btn btn-primary">Register</button>
            </form>
            <hr>
            <a href="index.php" class="btn btn-secondary">Sign In</a>
        </div>
    </div>
</div>
<!-- The modal or window which appears to display the terms and conditions to the user-->
<div id="termsModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Terms and Conditions</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                This is just a demo website. There aren't any terms to agree to. Feel free to edit your user details after registering.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'?>