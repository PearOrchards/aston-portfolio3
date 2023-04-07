<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once __DIR__ . '/components/head.php'; ?>
    <title>Register</title>
    <link rel="stylesheet" href="public/styles/forms.css">
    <script src="./public/js/register.js" defer async></script>
</head>
<body>
<?php require_once __DIR__ . '/components/navbar.php'; ?>
<header id="main-header">
    <div class="col">
        <h1>Register</h1>
        <h2>Register for an account with AProject!</h2>
    </div>
</header>
<main>
    <?php
    // IMPORTS
    require_once __DIR__ . '/lib/database.php';
    require_once __DIR__ . '/lib/user.php';

    $message = "";

    if (isset($_POST["submitted"])) {
        // CSRF protection
        if (isset($_POST['token']) && hash_equals($_SESSION['token'], $_POST['token'])) {
            unset($_SESSION['token']);
        } else {
            // return 405 http status code
            header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
            exit;
        }

        // double check that the usernames and passwords are not empty, and match.
        if (empty($_POST["username"])) {
            $message = "FORM ERROR: No username provided!";
            return;
        } else if (empty($_POST["email"]) || empty($_POST["email2"])) {
            $message = "FORM ERROR: No email provided!";
            return;
        } else if (empty($_POST["password"]) || empty($_POST["password2"])) {
            $message = "FORM ERROR: No password provided!";
            return;
        } else if ($_POST["email"] !== $_POST["email2"]) {
            $message = "FORM ERROR: Emails do not match!";
            return;
        } else if ($_POST["password"] !== $_POST["password2"]) {
            $message = "FORM ERROR: Passwords do not match!";
            return;
        }

        // using the imported database class, create a new instance of it.
        $db = new Database();

        try {
            $newUser = $db->createUser($_POST["username"], $_POST["email"], $_POST["password"]);
            session_start();
            $_SESSION["user"] = $newUser->username;
            $_SESSION['snack'] = array(
                'type' => 0,
                'message' => 'Thank you for registering, ' . htmlspecialchars($newUser->username) . ', and welcome!'
            );
            header("Location: projects.php");
        } catch (Exception $e) {
            $message = "USER ERROR: " . $e->getMessage();
        }
    } else {
        try {
            // generate a new token
            $_SESSION['token'] = bin2hex(random_bytes(32));
        } catch (Exception $e) {
            // return 500 http status code if token generation fails
            header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error');
            exit;
        }
    }
    ?>
    <section class="formSection">
        <h2>Register</h2>
        <a href="login.php">Already have an account?</a>
        <form action="register.php" method="post" id="registerForm">
            <input type="hidden" name="submitted" value="yep it is.">
            <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
            <div class="row">
                <div class="settingWrapper">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="rainbow-1" placeholder="Choose a username..." required>
                </div>
            </div>
            <div class="row">
                <div class="settingWrapper">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="rainbow-2" placeholder="Please type your email..." required>
                </div>
                <div class="settingWrapper">
                    <label for="email2">Confirm Email</label>
                    <input type="email" name="email2" id="email2" class="rainbow-3" placeholder="Please confirm your email..." required>
                </div>
            </div>
            <div class="row">
                <div class="settingWrapper">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="rainbow-5" placeholder="Please enter your password..." required>
                </div>
                <div class="settingWrapper">
                    <label for="password2">Confirm Password</label>
                    <input type="password" name="password2" id="password2" class="rainbow-6" placeholder="Please confirm your password..." required>
                </div>
            </div>
            <div class="row">
                <button type="submit" class="rainbow-7">Register</button>
            </div>
        </form>
        <pre id="phpMessage"><?= $message ?></pre>
    </section>
</main>
<?php require_once __DIR__ . '/components/footer.php'; ?>
</body>
</html>