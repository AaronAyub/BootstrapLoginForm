<?php
// This file provides functions used to directly modify the database

// Variables, change these if using a different user and password, or connecting remotely
$hostname = 'localhost';
$user = 'user';
$password = 'password';
$log = fopen("php://stdout", "w"); // Use $log to display feedback

// This function creates the database used for this program (if not already existing)
function initializeDatabase($log) {
    // Connect to the database
    $connection = new mysqli($GLOBALS['hostname'], $GLOBALS['user'], $GLOBALS['password']);
    if ($connection->connect_error) {
        die("Connection unsuccessful!" . $connection->connect_error);
    }
    fwrite($log,"Connection successful.\n");
    
    // Creating the database if it doesn't already exist
    $database = "CREATE DATABASE loginForm";
    if ($connection->query($database) === TRUE) {
        fwrite($log,"Database created.\n");
    } else {
        fwrite($log,mysqli_error($connection)."\n");
    }
    
    // Now create the tables if they don't already exist
    $connection->query("USE loginForm");
    $users = "CREATE TABLE users (
        username VARCHAR(50) NOT NULL PRIMARY KEY,
        email VARCHAR(80) NOT NULL,
        pass VARCHAR(128) NOT NULL,
        firstname VARCHAR(80) DEFAULT '',
        lastname VARCHAR(80) DEFAULT '',
        loc VARCHAR(80) DEFAULT '',
        job VARCHAR(80) DEFAULT '',
        account VARCHAR(32) DEFAULT 'user',
        registered TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    if ($connection->query($users) === TRUE) {
        fwrite($log,"User table created.\n");
    }
    $logins = "CREATE TABLE logins (
        token VARCHAR(128) NOT NULL PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (username) REFERENCES users (username)
            ON DELETE CASCADE
            ON UPDATE CASCADE
    )";
    if ($connection->query($logins) === TRUE) {
        fwrite($log,"Logins table created.\n");
    }
    $posts = "CREATE TABLE posts (
        id INT(64) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        poster VARCHAR(50),
        isGuest BOOLEAN DEFAULT 0,
        recipient VARCHAR(50) NOT NULL,
        created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        contents VARCHAR(4096) NOT NULL,
        FOREIGN KEY (recipient) REFERENCES users (username)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
        FOREIGN KEY (poster) REFERENCES users (username)
            ON DELETE CASCADE
            ON UPDATE CASCADE
    )";
    if ($connection->query($posts) === TRUE) {
        fwrite($log,"Posts table created.\n");
    }

    // Add an admin account. For this test, the admin will be called "admin" with the password "password".
    $password = password_hash("password",PASSWORD_DEFAULT);
    $st = $connection->prepare("INSERT INTO users (username,email,pass,account) VALUES ('admin','',?,'admin')");
    $st->bind_param("s",$password);
    $st->execute();
    $st->close();
    $connection->close();
}

// Returns a connection to the database
function connect() {
    // Now connect to the database
    $connection = new mysqli($GLOBALS['hostname'], $GLOBALS['user'], $GLOBALS['password'], "loginForm");
    if ($connection->connect_error) {
        die("Connection unsuccessful!" . $connection->connect_error);
    }
    // fwrite($GLOBALS['log'],"Connection successful.\n");
    return $connection;
}

// Manages session variables, called at the start of every webpage. This makes sure the user is logged in.
function initSession($connection) {
    session_start();
    if (isset($_SESSION['user'])) return; // If the user is logged in already, nothing needs to be done
    // Otherwise, try to log in through the user's session token cookie
    if (isset($_COOKIE['session_token'])) {
        $st = $connection->prepare("SELECT username FROM logins WHERE token=?");
        $st->bind_param("s",$_COOKIE['session_token']);
        if ($st->execute()) {
            $result = $st->get_result();
            if ($result->num_rows == 0) { // If this token is not in the database, then remove it from the user's side
                setcookie("session_token","",time() - 1);
            }
            else { // Otherwise, this log the user in
                $_SESSION['user'] = $result->fetch_assoc()['username'];
            }
        }
    }
}

// This function deletes the database and recreates it, essentially resetting all the data
function resetDatabase($log) {
    // Connect to the database
    $connection = connect();

    $st = $connection->prepare("DROP DATABASE IF EXISTS loginForm");
    if ($st->execute()) {
        fwrite($log,"Dropped database successfully.\n");
    }
    $st->close();
    $connection->close();

    // And recreate the database
    initializeDatabase($log);
}

// If the user runs this script directly from the terminal with no arguments, assume they want to start the database
if (isset($argc) && $argc < 2) {
    initializeDatabase($log);
}
else if (isset($argv) && $argv[1] == "reset") { // The user can also add the argument reset to reset the database
    resetDatabase($log);
}
?>