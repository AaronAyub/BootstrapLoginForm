<?php
    // Connecting to database
    $log = fopen("php://stdout", "w");
    include 'phpUtils.php';
    $connection = connect();
    initSession($connection);
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
$st = $connection->prepare("SELECT * FROM users WHERE username=?");
$st->bind_param("s",$request);
$st->execute();
$result = $st->get_result();
if ($result->num_rows == 0) { // If there is no matching user
    $_SESSION['output'] = "The user named ".$user." is not registered! Please try again.";
}
else { // Otherwise, display the user's public details
    $user = $result->fetch_assoc();
    $realname = $user['firstname']." ".$user['lastname'] != " " ? $user['firstname']." ".$user['lastname'] : "Not specified";
    $loc = $user['loc'] != "" ? $user['loc'] : "Not specified";
    $job = $user['job'] != "" ? $user['job'] : "Not specified";
    echo "<div class=\"container text-center\"><h3>Profile of ".$request."</h3></div>";
    echo "<h4 class=\"spaced\">Real name</h4>".$realname;
    echo "<h4 class=\"spaced\">Location</h4>".$loc;
    echo "<h4 class=\"spaced\">Occupation</h4>".$job;
}
$st->close();

$connection->close();
?>
        </div>
    </div>
</div>
<?php include 'footer.php'?>