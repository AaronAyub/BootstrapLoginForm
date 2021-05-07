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
                </tr>
            </thead>
            <tbody>
        EOT;
        // Print out a table row for each user
        while ($user = $query->fetch_assoc()) {
            echo "<tr>";
            echo "<td scope=\"row\">".$user["username"]."</td>";
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