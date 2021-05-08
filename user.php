<?php
    // Connecting to database
    session_start();
    $log = fopen("php://stdout", "w");
    include 'phpUtils.php';
    $connection = connect();
?>
<?php include 'header.php'?>
<div class="container">
    <div id="outputbox"></div>
    <div class="card bg-light">
        <div class="card-body">
<?php
$request = $_REQUEST['user']; // The user being searched for

$query = "SELECT * FROM users WHERE username='$request'";
$query = $connection->query($query);
if ($query->num_rows == 0) {
    $_SESSION['output'] = "The user named ".$user." is not registered! Please try again.";
}
else {
    $user = $query->fetch_assoc();
    $realname = $user['firstname']." ".$user['lastname'] != "   " ? $user['firstname']." ".$user['lastname'] : "Not specified";
    $loc = $user['loc'] != " " ? $user['loc'] : "Not specified";
    $job = $user['job'] != " " ? $user['job'] : "Not specified";
    echo "<div class=\"container text-center\"><h3>Profile of ".$request."</h3></div>";
    echo "<h4>Real name</h4>".$realname;
    echo "<h4>Location</h4>".$loc;
    echo "<h4>Occupation</h4>".$job;
}

$connection->close();
?>
        </div>
    </div>
</div>
<?php include 'footer.php'?>