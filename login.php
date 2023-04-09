<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once __DIR__ . '/components/head.php'; ?>
    <title>Login</title>
    <link rel="stylesheet" href="public/styles/forms.css">
    <!-- We don't import register.js here as other than the required attributes that HTML already handles, we don't need more checks. -->
</head>
<body>
<?php require_once __DIR__ . '/components/navbar.php'; ?>
<header id="main-header">
    <div class="col">
        <h1>Login</h1>
        <h2>Login to your AProject account!</h2>
    </div>
</header>
<main>
    <?php
    // IMPORTS
    require_once __DIR__ . '/lib/user.php';

    $message = "";

    if (isset($_SESSION["user"])) {
        header("Location: projects.php");
    }

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
        if (empty($_POST["username"]) || empty($_POST["password"])) {
            $message = "Please fill out all fields.";
            return;
        }

        $db = new UserDatabase();
        try {
            $user = $db->login($_POST["username"], $_POST["password"]);
            $_SESSION["user"] = $user->username;
            $_SESSION['snack'] = array(
                'type' => 0,
                'message' => 'Welcome, ' . htmlspecialchars($user->username) . '!'
            );
            header("Location: projects.php");
        } catch (Exception $e) {
            $_SESSION['snack'] = array(
                'type' => 2,
                'message' => 'Failed to login! ' . $e->getMessage()
            );
            header("Location: login.php");
        }

    } else {
        try {
            $_SESSION['token'] = bin2hex(random_bytes(32));
        } catch (Exception $e) {
            // return 500 http status code if token generation fails
            header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error');
            exit;
        }
    }
    ?>
    <section class="formSection">
        <h2>Login</h2>
        <a href="register.php">Want to register instead?</a>
        <form action="login.php" method="post" class="thinner">
            <input type="hidden" name="submitted" value="uh huh.">
            <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
            <div class="settingWrapper">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" class="rainbow-1" placeholder="Enter your username..." required>
            </div>
            <div class="settingWrapper">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="rainbow-5" placeholder="Enter your password..." required>
            </div>
            <div class="row">
                <button type="submit" class="rainbow-7">Go!</button>
            </div>
        </form>
        <pre id="phpMessage"><?= $message ?></pre>
    </section>
</main>
<?php require_once __DIR__ . '/components/footer.php'; ?>
</body>
</html>