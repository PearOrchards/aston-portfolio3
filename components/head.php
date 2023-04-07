<meta charset="UTF-8">

<!-- Ideally I would self-host these, but in order to simplify the bundle I am doing it this way. -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,200;0,400;0,600;1,200;1,400;1,600&family=Righteous&display=swap" rel="stylesheet">

<!-- Font Awesome -->
<script src="https://kit.fontawesome.com/fb08371e49.js" crossorigin="anonymous"></script>

<link rel="stylesheet" href="./public/styles/global.css">

<script src="./public/js/createSnackbar.js"></script>
<?php
    // We start the session here so that we can access the snack variable.
    // Oh and the navbar needs it. So specify it now, don't worry about it later.
    session_start();

    if (isset($_SESSION['snack'])) {
        $type = (isset($_SESSION['snack']['type'])) ? $_SESSION['snack']['type'] : 0;
        $message = (isset($_SESSION['snack']['message'])) ? htmlspecialchars($_SESSION['snack']['message']) : 'A snackbar was called, but no message was provided.';
        unset($_SESSION['snack']);

        // Waits for the page to load, then create the snackbar.
        echo "<script>window.addEventListener('load', () => { createSnackbar($type, '$message'); });</script>";
    }
?>