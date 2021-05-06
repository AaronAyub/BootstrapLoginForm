<!-- Footer shown at the bottom of each page -->
        <footer class="footer bg-light">
            This website is just a demonstration of html, javascript, css, php, and bootstrap. There is no service to log into or register for here.
        </footer>

        <script src="public/scripts.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>
    </body>
</html>

<?php
    // Display any feedback from the server requests to the user
    if (isset($_SESSION['output']) && $_SESSION['output'] != "") {
        echo "<script>showOutput(\"".$_SESSION['output']."\",\"".$_SESSION['outputType']."\")</script>";
        unset($_SESSION['output']);
        unset($_SESSION['outputType']);
    }
?>