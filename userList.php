<?php
    // Connecting to database
    session_start();
    $log = fopen("php://stdout", "w");
    include 'phpUtils.php';
    $connection = connect();
?>

<?php include 'header.php'?>
<div class="container">
<?php
    $isAdmin = false; // Marked true if the currently signed in user is an admin.
    // Give the session the admin buttons if the user is an admin.
    if (isset($_SESSION['user'])) {
        $st = $connection->prepare("SELECT account FROM users WHERE username=?");
        $st->bind_param("s",$_SESSION['user']);
        if ($st->execute()) {
            if ($st->get_result()->fetch_assoc()['account'] == "admin") {
                $isAdmin = true;
            }
        }
        $st->close();
    }
    $st = $connection->prepare("SELECT * FROM users");
    $st->execute();
    $result = $st->get_result();
    if ($result->num_rows > 0) { // If there are any users, print out a table
        echo <<< EOT
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Username</th>
                    <th>Real Name</th>
                    <th>Location</th>
                    <th>Occupation</th>
        EOT;
        if ($isAdmin == true) { // To be implemented
            echo "<th>Delete User</th>";
        }
        echo <<< EOT
                </tr>
            </thead>
            <tbody>
        EOT;
        // Print out a table row for each user
        while ($user = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td scope=\"row\"><a href=\"user.php?user=".$user['username']."\">".$user["username"]."</td>";
            echo "<td>".$user["firstname"]." ".$user["lastname"]."</td>";
            echo "<td>".$user["loc"]."</td>";
            echo "<td>".$user["job"]."</td>";
            if ($isAdmin) {
                echo "<td>Delete User</td>";
            }
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
    }
    else { // If there are no users
        echo "There are no users registered yet!";
    }
    $st->close();
    $connection->close();
?>
</div>
<?php include 'footer.php'?>