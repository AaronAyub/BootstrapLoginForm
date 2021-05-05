// Display outputs messages, if any exist
function showOutput (outputMessage, outputType) {
    if (typeof outputMessage !== "undefined") {
        box = document.getElementById("outputbox");
        box.style.display = "block"; // Show the output box
        box.innerText = outputMessage;
        if (outputType == "success") { // Colour it green for successful operations
            box.classList.add("success");
        }
    }
}

// Automatically show the change user settings field if the user is logged in.
function showName (username) {
    document.getElementById("settingsName").innerText = "Welcome, " + username;
}