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
        registered TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    if ($connection->query($users) === TRUE) {
        fwrite($log,"User table created.\n");
    }
    $connection->close();
}

// Returns a connection to the database
function connect() {
    $connection = new mysqli($GLOBALS['hostname'], $GLOBALS['user'], $GLOBALS['password'], "loginForm");
    if ($connection->connect_error) {
        die("Connection unsuccessful!" . $connection->connect_error);
    }
    fwrite($GLOBALS['log'],"Connection successful.\n");
    return $connection;
}

// This function deletes the database and recreates it, essentially resetting all the data
function resetDatabase($log) {
    // Connect to the database
    $connection = connect();

    $connection->query("DROP DATABASE IF EXISTS loginForm");
    if ($connection) {
        fwrite($log,"Dropped database successfully.\n");
    }

    $connection->close();

    // And recreate the database
    initializeDatabase($log);
}

// If the user runs this script directly from the terminal with no arguments, assume they want to start the database
if (isset($argc) && $argc < 2) {
    initializeDatabase($log);
}
else if ($argv[1] == "reset") { // The user can also add the argument reset to reset the database
    resetDatabase($log);
}
?>