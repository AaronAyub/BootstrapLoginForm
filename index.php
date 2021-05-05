<?php
    // Connecting to database
    session_start();
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

    $output = ""; // Output is the message to send to the user, if any.
    $outputType = "error"; // This is changed to "success" if the user successfully performs an operation.

    // If the user makes a request to the server

    // User logs in
    if (isset($_POST['login'])) {
        fwrite($log,"Login request.\n");
        if (empty($_POST['username'])) {
            $output = $output . "Please enter a username!";
        }
        else if (empty($_POST['password'])) {
            $output = $output . "Please enter a password!";
        }
        else {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $query = "SELECT pass FROM users WHERE username='$username'";
            $query = $connection->query($query);
            if ($query->num_rows == 1) {
                $user = $query->fetch_assoc();
                if (password_verify($password,$user["pass"])) {
                    $output = "Successfully logged in.";
                    $outputType = "success";
                    $_SESSION['user'] = $username;
                }
                else {
                    $output = "Incorrect password.";
                }
            }
            else {
                $output = "No such user exists.";
            }
        }
    }
    // User registers for a new account
    else if (isset($_POST['register'])) {
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
            $query = "SELECT username FROM users WHERE username='$username'";
            $query = $connection->query($query);
            if ($query->num_rows > 0) {
                $output = "Sorry, the username $username is already taken!";
            }
            else if ($connection->query($add) === TRUE) {
                $output = "User successfully added!";
                $outputType = "success";
            }
            else {
                fwrite($log,"Can't create user!\n" . mysqli_error($connection));
            }
        }
    }
    // User changes the account's email
    else if (isset($_POST['changeemail'])) {
        if (empty($_SESSION['user'])) {
            $output = "You shouldn't be accessing this form unless you are logged in.";
        }
        else if (empty($_POST['newemail'])) {
            $output = "Please enter a new email address to change your email.";
        }
        else {
            $username = $_SESSION['user'];
            $newemail = $_POST['newemail'];

            // Update the record with the new email address
            $query = "UPDATE users SET email='$newemail' WHERE username='$username'";
            $query = $connection->query($query);
            
            if ($query) {
                $output = "Changed email address!";
                $outputType = "success";
            }
            else {
                fwrite($log,mysqli_error($connection)."\n");
            }
        }
    }
    // User changes the account's password
    else if (isset($_POST['changepass'])) {
        if (empty($_SESSION['user'])) {
            $output = "You shouldn't be accessing this form unless you are logged in.";
        }
        else if (empty($_POST['oldpass']) || empty($_POST['newpass']) || empty($_POST['newconfirm'])) {
            $output = "Please fill out all the fields to change your password.";
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
                    $output = "Please enter your old password correctly.";
                }
                else if ($newpass != $newconfirm) {
                    $output = "The new password must match with the confirmation password. Please try again.";
                }
                else {
                    // Hash and salt the new password, and update the record
                    $output = "Password changed!";
                    $outputType = "success";
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
    // User logs out
    else if (isset($_POST['logout'])) {
        $output = "Logged out!";
        $outputType = "success";
        session_unset();
    }

    // Display any feedback from the server requests to the user
    if ($output != "") {
        echo "<script>let outputMessage = \"".$output."\"; let outputType = \"".$outputType."\";</script>";
    }

    // If the user is logged in through a session variable, use javascript to display the correct form.
    if (!empty($_SESSION['user'])) {
        echo "<script>let loggedIn = true;</script>";
        echo "<script>let username = \"".$_SESSION['user']."\";</script>";
    }

    $connection->close();

    include 'index.html';
?>
