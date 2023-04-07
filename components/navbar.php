<nav>
    <a href="index.php">Home</a>
    <a href="projects.php">Projects</a>
    <?php
        // If head is imported, then session is already started.
        if (isset($_SESSION['user'])) {
            echo '<a href="logout.php">Logout</a>';
        } else {
            echo '<a href="login.php">Login</a>';
        }
    ?>
</nav>