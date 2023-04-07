<?php
    session_start();
    unset($_SESSION['user']); // Don't destroy the session, as it'll break snackbar functionality.
    $_SESSION['snack'] = array(
        'type' => 0,
        'message' => 'You have been logged out.'
    );
    header("Location: index.php");
    exit();