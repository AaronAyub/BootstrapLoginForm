<?php
    // Connecting to database
    session_start();
    $log = fopen("php://stdout", "w");
    include 'phpUtils.php';
    $connection = connect();

    // User changes the account's email
    if (isset($_POST['changeemail'])) {
        if (empty($_SESSION['user'])) {
            $_SESSION['output'] = "You shouldn't be accessing this form unless you are logged in.";
        }
        else if (empty($_POST['newemail'])) {
            $_SESSION['output'] = "Please enter a new email address to change your email.";
        }
        else {
            $username = $_SESSION['user'];
            $newemail = $_POST['newemail'];

            // Update the record with the new email address
            $query = "UPDATE users SET email='$newemail' WHERE username='$username'";
            $query = $connection->query($query);
            
            if ($query) {
                $_SESSION['output'] = "Changed email address!";
                $_SESSION['outputType'] = "success";
            }
            else {
                fwrite($log,mysqli_error($connection)."\n");
            }
        }
    }
    // User changes the account's password
    else if (isset($_POST['changepass'])) {
        if (empty($_SESSION['user'])) {
            $_SESSION['output'] = "You shouldn't be accessing this form unless you are logged in.";
        }
        else if (empty($_POST['oldpass']) || empty($_POST['newpass']) || empty($_POST['newconfirm'])) {
            $_SESSION['output'] = "Please fill out all the fields to change your password.";
        }
        else {
            $oldpass = $_POST['oldpass'];
            $newpass = $_POST['newpass'];
            $newconfirm = $_POST['newconfirm'];
            $username = $_SESSION['user'];

            $query = "SELECT pass FROM users WHERE username='$username'";
            $query = $connection->query($query);

            // If this user exists, firt check that the old password matches, then check that the new password is confirmed correctly.
            if ($query) {
                if (!password_verify($oldpass,$query->fetch_assoc()['pass'])) {
                    $_SESSION['output'] = "Please enter your old password correctly.";
                }
                else if ($newpass != $newconfirm) {
                    $_SESSION['output'] = "The new password must match with the confirmation password. Please try again.";
                }
                else {
                    // Hash and salt the new password, and update the record
                    $_SESSION['output'] = "Password changed!";
                    $_SESSION['outputType'] = "success";
                    $new = password_hash($newpass,PASSWORD_DEFAULT);
                    $query = "UPDATE users SET pass='$new' WHERE username='$username'";
                    $query = $connection->query($query);
                }
            }
            else {
                fwrite($log,mysqli_error($connection)."\n");
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
            <div id="change">
                <form method="post">
                    <div class="container text-center">
                        <h3 id="settingsName">User Settings</h3>
                    </div>
                    <hr>
                    <div class="container text-center">
                        <h5>Change Email Address</h5>
                    </div>
                    <div class="form-group">
                        <label for="newemail">New Email Address: </label>
                        <input required type="email" name="newemail" id="newemail" class="form-control" placeholder="Email Address">
                    </div>
                    <button type="submit" name="changeemail" class="btn btn-primary">Change Email</button>
                </form>
                <hr>
                <div class="container text-center">
                    <h5>Change Password</h5>
                </div>
                <form method="post">
                    <div class="form-group">
                        <label for="oldpass">Previous Password: </label>
                        <input required type="password" name="oldpass" id="oldpass" class="form-control"
                            placeholder="Previous Password">
                    </div>
                    <div class="form-group">
                        <label for="newpass">New Password: </label>
                        <input required minlength="6" type="password" name="newpass" id="newpass" class="form-control"
                            placeholder="New Password">
                    </div>
                    <div class="form-group">
                        <label for="newconfirm">Confirm New Password: </label>
                        <input required type="password" name="newconfirm" id="newconfirm" class="form-control"
                            placeholder="Confirm New Password">
                    </div>
                    <button type="submit" name="changepass" class="btn btn-primary">Change Password</button>
                </form>
                <hr>
                <form method="post" action="index.php">
                    <button type="submit" name="logout" class="btn btn-secondary">Log Out</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'?>