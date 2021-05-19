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

    // If the user wants to post to another user's profile
    if (isset($_POST['addpost'])) {
        if (empty($_POST['post'])) { // Don't allow blank messages
            $_SESSION['output'] = "Please enter a message if you would like to add a post.";
        }
        else {
            $message = htmlspecialchars($_POST['post']);
            $st->close();
            // If the user is logged in, prepare a post with a user attached
            if (isset($_SESSION['user'])) {
                $poster = $_SESSION['user'];
                $st = $connection->prepare("INSERT INTO posts (recipient,poster,contents) VALUES (?,?,?)");
                $st->bind_param("sss",$request,$poster,$message);
            }
            else { // Otherwise, post this as a guest
                $isGuest = 1;
                $st = $connection->prepare("INSERT INTO posts (recipient,isGuest,contents) VALUES (?,?,?)");
                $st->bind_param("sis",$request,$isGuest,$message);
            }
            
            if ($st->execute()) {
                $_SESSION['output'] = "Posted message!";
                $_SESSION['outputType'] = "success";
            }
            else {
                $_SESSION['output'] = "Sorry, the message couldn't be posted.";
                fwrite($log,$st->error);
            }
        }
    } 
    $user = $result->fetch_assoc();
    $realname = $user['firstname']." ".$user['lastname'] != " " ? $user['firstname']." ".$user['lastname'] : "Not specified";
    $loc = $user['loc'] != "" ? $user['loc'] : "Not specified";
    $job = $user['job'] != "" ? $user['job'] : "Not specified";
    echo "<div class=\"container text-center\"><h3>Profile of ".$request."</h3></div>";
    echo "<h4 class=\"spaced\">Real name</h4>".$realname;
    echo "<h4 class=\"spaced\">Location</h4>".$loc;
    echo "<h4 class=\"spaced\">Occupation</h4>".$job;

    $st = $connection->prepare("SELECT * FROM posts WHERE recipient=?");
    $st->bind_param("s",$request);
    $st->execute();
    $result = $st->get_result();
    echo "<div class=\"container\"><div class=\"container text-center\"><h3>Messages</h3></div>";
    if ($result->num_rows == 0) { // If the user has no messages on their profile
        echo "This user has no messages profile messages yet! Would you like to add one?";
    }
    else { // Otherwise, display all messages
        while ($post = $result->fetch_assoc()) {
            if ($post['isGuest']) { // If this is a guest message
                echo "From: Guest<br>";
            }
            else { // Otherwise, this is from a registered user
                echo "From: ".$post['poster']."<br>";
            }
            echo $post['contents']."<br>";
        }
    }
    echo "<form id=\"addpost\" method=\"post\" action=\"user.php?user=".$request."\">";
    echo <<< EOT
        <div class="form-group">
            <label for="post">Write a post: </label>
            <textarea required class="form-control" rows="4" name="post" id="post"></textarea>
        </div>
        <button type="submit" name="addpost" class="btn btn-primary">Add Post</button>
    </form>
    EOT;
    echo "</div>";
}
$st->close();

$connection->close();
?>
        </div>
    </div>
</div>
<?php include 'footer.php'?>