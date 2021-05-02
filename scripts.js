// Toggle between showing the login and register screens
function toggleView(show) {
    if (show == 0) { // Show the login screen
        document.getElementById("register").style.display="none";
        document.getElementById("login").style.display="block";
    }
    else if (show == 2) { // Show the change User screen
        document.getElementById("login").style.display="none";
        document.getElementById("change").style.display="block";
    }
    else { // Show the registration screen
        document.getElementById("login").style.display="none";
        document.getElementById("register").style.display="block";
    }
}

// Automatically show the change user settings field if the user is logged in.
if (loggedIn) {
    toggleView(2);
}