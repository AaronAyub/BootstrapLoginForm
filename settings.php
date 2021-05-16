<?php
    // Connecting to database
    $log = fopen("php://stdout", "w");
    include 'phpUtils.php';
    $connection = connect();
    initSession($connection);

    // User changes the account's email
    if (isset($_POST['changeemail'])) {
        if (empty($_POST['newemail'])) {
            $_SESSION['output'] = "Please enter a new email address to change your email.";
        }
        else {
            $username = $_SESSION['user'];
            $newemail = $_POST['newemail'];

            // Update the record with the new email address
            $st = $connection->prepare("UPDATE users SET email=? WHERE username=?");
            $st->bind_param("ss",$newemail,$username);

            if ($st->execute()) {
                $_SESSION['output'] = "Changed email address!";
                $_SESSION['outputType'] = "success";
            }
            else {
                fwrite($log,mysqli_error($connection)."\n");
            }
            $st->close();
        }
    }
    // User changes the account's password
    else if (isset($_POST['changepass'])) {
        if (empty($_POST['oldpass']) || empty($_POST['newpass']) || empty($_POST['newconfirm'])) {
            $_SESSION['output'] = "Please fill out all the fields to change your password.";
        }
        else {
            $oldpass = $_POST['oldpass'];
            $newpass = $_POST['newpass'];
            $newconfirm = $_POST['newconfirm'];
            $username = $_SESSION['user'];

            $st = $connection->prepare("SELECT pass FROM users WHERE username=?");
            $st->bind_param("s",$username);

            // If this user exists, first check that the old password matches, then check that the new password is confirmed correctly.
            if ($st->execute()) {
                if (!password_verify($oldpass,$st->get_result()->fetch_assoc()['pass'])) {
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
                    $st->close();
                    $st = $connection->prepare("UPDATE users SET pass=? WHERE username=?");
                    $st->bind_param("ss",$new,$username);
                    $st->execute();
                }
            }
            else {
                fwrite($log,mysqli_error($connection)."\n");
            }
            $st->close();
        }
    }
    // User updates public details
    else if (isset($_POST['updateProfile'])) {
        $firstname = htmlspecialchars($_POST['firstname']);
        $lastname = htmlspecialchars($_POST['lastname']);
        $loc = htmlspecialchars($_POST['loc']);
        $job = htmlspecialchars($_POST['job']);
        $username = $_SESSION['user'];

        $st = $connection->prepare("UPDATE users SET firstname=?,lastname=?,loc=?,job=? WHERE username=?");
        $st->bind_param("sssss",$firstname,$lastname,$loc,$job,$username);
        $st->execute();
        $st->close();
        $_SESSION['output'] = "Profile updated!";
        $_SESSION['outputType'] = "success";
    } // User deletes the account
    else if (isset($_POST['deleteAccount'])) {
        $password = $_POST['pass'];
        $username = $_SESSION['user'];

        $st = $connection->prepare("SELECT pass FROM users WHERE username=?");
        $st->bind_param("s",$username);
        if ($st->execute()) { // If the currently logged in user exists (should pass)
            if (!password_verify($password,$st->get_result()->fetch_assoc()['pass'])) { // Check that the password is correct
                $_SESSION['output'] = "Please enter your password correctly.";
            }
            else { // Otherwise, delete the account
                $st->close();
                $st = $connection->prepare("DELETE FROM users WHERE username=?");
                $st->bind_param("s",$username);
                
                if ($st->execute()) {
                    $_SESSION['output'] = "Your account has been deleted.";
                    $_SESSION['outputType'] = "success";
                    unset($_SESSION['user']); // Log the user out as well
                    session_write_close(); // Write session data before redirecting
                    header('Location: index.php');
                }
                else {
                    $_SESSION['output'] = "Something unexpected happened. Please contact the administrator.";
                }
                $st->close();
            }
        }
        $st->close();
    }

    // Return details of the user's profile, so they can enter only the information that needs to be changed.
    $username = $_SESSION['user'];
    $query = "SELECT * FROM users WHERE username='$username'";
    $query = $connection->query($query);
    $st = $connection->prepare("SELECT * FROM users WHERE username=?");
    $st->bind_param("s",$username);
    $st->execute();
    $result = $st->get_result();
    if ($result->num_rows == 0) {
        $_SESSION['output'] = "You shouldn't be accessing this form unless you are logged in.";
    }
    else {
        $user = $result->fetch_assoc();
        $firstname = $user['firstname'];
        $lastname = $user['lastname'];
        $loc = $user['loc'];
        $job = $user['job'];
        $email = $user['email'];
    }
    $st->close();
    $connection->close();
?>
<?php include 'header.php'?>
<div class="container">
    <div id="outputbox"></div>
    <div class="card bg-light">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="profileTab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true">Profile</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="emailPassTab" data-bs-toggle="tab" data-bs-target="#emailPass" type="button" role="tab" aria-controls="emailPass" aria-selected="false">Email / Password</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="deleteTab" data-bs-toggle="tab" data-bs-target="#delete" type="button" role="tab" aria-controls="delete" aria-selected="false">Delete Account</button>
            </li>
        </ul>
        <div class="tab-content">
            <div class="card-body tab-pane show active fade" id="profile" role="tabpanel" aria-labelledby="profileTab">
                <form method="post">
                    <div class="container text-center">
                        <h3>Profile</h3>
                    </div>
                    <div class="form-group">
                        <label for="firstname">First Name</label>
                        <input type="text" name="firstname" id="firstname" class ="form-control" placeholder="(Optional)">
                    </div>
                    <div class="form-group">
                        <label for="lastname">Last Name</label>
                        <input type="text" name="lastname" id="lastname" class ="form-control" placeholder="(Optional)">
                    </div>
                    <div class="form-group">
                        <label for="loc">Location</label>
                        <input type="text" name="loc" id="loc" class ="form-control" placeholder="(Optional)">
                    </div>
                    <div class="form-group">
                        <label for="job">Occupation</label>
                        <input type="text" name="job" id="job" class ="form-control" placeholder="(Optional)">
                    </div>
                    <button type="submit" name="updateProfile" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
            <div class="card-body tab-pane fade" id="emailPass" role="tabpanel" aria-labelledby="emailPassTab">
                <form method="post">
                    <div class="container text-center">
                        <h3>User Settings</h3>
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
            </div>
            <div class="card-body tab-pane fade" id="delete" role="tabpanel" aria-labelledby="deleteTab">
            You may delete your account here. If you do so, your username will be made available for other users to register under. You will have to enter your password to confirm deleting your account.
                <form method="post">
                    <div class="form-group">
                    <label for="pass">Enter Password</label>
                        <input required type="password" name="pass" id="pass" class="form-control" placeholder="Enter your password">
                    </div>
                    <button type="submit" name="deleteAccount" class="btn btn-danger">Delete Account</button>
                </form>
            </div>
        </div>
        <hr>
        <form method="post" action="index.php" style="margin-left: 1rem; margin-bottom: 1rem;">
            <button type="submit" name="logout" class="btn btn-secondary">Log Out</button>
        </form>
    </div>
</div>
<?php include 'footer.php'?>
<?php
// Automatically re-enter previous profile details into the form
echo "<script>document.getElementById(\"firstname\").value = \"".$firstname."\";</script>";
echo "<script>document.getElementById(\"lastname\").value = \"".$lastname."\";</script>";
echo "<script>document.getElementById(\"loc\").value = \"".$loc."\";</script>";
echo "<script>document.getElementById(\"job\").value = \"".$job."\";</script>";
echo "<script>document.getElementById(\"newemail\").value = \"".$email."\";</script>";
?>