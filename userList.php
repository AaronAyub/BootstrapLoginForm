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
    $query = "SELECT * FROM users";
    $query = $connection->query($query);
    if ($query->num_rows > 0) { // If there are any users, print out a table
        echo <<< EOT
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Username</th>
                    <th>Real Name</th>
                    <th>Location</th>
                    <th>Occupation</th>
                </tr>
            </thead>
            <tbody>
        EOT;
        // Print out a table row for each user
        while ($user = $query->fetch_assoc()) {
            echo "<tr>";
            echo "<td scope=\"row\"><a href=\"user.php?user=".$user['username']."\">".$user["username"]."</td>";
            echo "<td>".$user["firstname"]." ".$user["lastname"]."</td>";
            echo "<td>".$user["loc"]."</td>";
            echo "<td>".$user["job"]."</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
    }
    else { // If there are no users
        echo "There are no users registered yet!";
    }
    $connection->close();
?>
</div>
<?php include 'footer.php'?>