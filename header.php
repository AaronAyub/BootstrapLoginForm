<!-- Navbar shown at the top of every page -->
<!DOCTYPE html>
<html>
    <head>
        <title>User Panel</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="public/style.css"/>
    </head>
    <body>
        <nav class="navbar bg-dark text-white text-center">
            <div>
            <a href="userList.php">User List</a>
            </div>
            
            <div id="nav-right">
            <div>
                <?php
                if (isset($_SESSION['user'])) {
                    echo "<a href=\"settings.php\">Your Profile</a>";
                }
                ?>
            </div>
            <!-- This div switches between the login/logout button in the header-->
            <div>
            <?php
            if (isset($_SESSION['user'])) { // If the user is logged in, let them quickly log out
                echo <<< EOT
                <form method="post" action="index.php">
                    <button type="submit" name="logout" style="background: none; border: none; color: white;">Log Out</button>
                </form>
                EOT;
            }
            else { // Otherwise, let them quickly log in
                // A form is used instead of to make the buttons appear identical.
                echo <<< EOT
                <form method="get" action="index.php">
                    <button type="submit" style="background: none; border: none; color: white;">Log In</button>
                </form>
                EOT;
            }
            ?>
            </div>
            </div>
        </nav>