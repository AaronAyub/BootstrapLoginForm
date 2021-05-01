// Toggle between showing the login and register screens
function toggleView(show) {
    if (show == 0) { // Show the login screen
        document.getElementById("register").style.display="none";
        document.getElementById("login").style.display="block";
    }
    else { // Show the registration screen
        document.getElementById("login").style.display="none";
        document.getElementById("register").style.display="block";
    }
}

mysqli_connect('localhost', 'user', 'password', 'loginform');