<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once __DIR__ . '/components/head.php'; ?>
    <title>Delete Project</title>
    <link rel="stylesheet" href="./public/styles/projects.css">
    <script src="./public/js/collapsibleHandler.js" defer async></script>
</head>
<body>
<?php require_once __DIR__ . '/components/navbar.php'; ?>
<header id="main-header">
    <div class="col">
        <h1>Delete Project</h1>
        <h2>Are you sure you want to delete this project?</h2>
    </div>
</header>
<main>
    <section>
        <?php
            require_once __DIR__ . '/lib/database.php';
            require_once __DIR__ . '/lib/project.php';
            require_once __DIR__ . '/components/collapseElement.php';
            $db = new Database();

            if (!isset($_SESSION['user'])) {
                $_SESSION['snack'] = array(
                    'type' => 2,
                    'message' => 'You must be logged in to access this page.'
                );
                header('Location: login.php');
            } else if (isset($_POST['pid'])) {
                // CSRF protection
                if (isset($_POST['token']) && hash_equals($_SESSION['token'], $_POST['token'])) {
                    unset($_SESSION['token']);
                } else {
                    // return 405 http status code
                    header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
                    exit;
                }

                try {
                    $db->deleteProject($_POST['pid']);
                    $_SESSION['snack'] = array(
                        'type' => 0,
                        'message' => 'Project deleted successfully!'
                    );
                    header('Location: projects.php');
                } catch (Exception $e) {
                    $_SESSION['snack'] = array(
                        'type' => 2,
                        'message' => 'An error occurred while deleting the project!'
                    );
                    header('Location: projects.php');
                }
            } else if (!isset($_GET['pid'])) {
                $_SESSION['snack'] = array(
                    'type' => 2,
                    'message' => 'You must specify a project to delete!'
                );
                header('Location: projects.php');
            }

            // Generate CSRF token
            try {
                $_SESSION['token'] = bin2hex(random_bytes(32));
            } catch (Exception $e) {
                // return 500 http status code if token generation fails
                // this is a server error, not a client error, and needs urgent fixing. hence no snackbar.
                header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error');
                exit;
            }

            $project = null;
            try {
                $project = $db->getProject($_GET['pid']);
            } catch (Exception $e) {
                $_SESSION['snack'] = array(
                    'type' => 2,
                    'message' => 'The project you specified does not exist!'
                );
                header('Location: projects.php');
            }


            $username = $db->getNameFromUID($project->uid);
            $email = $db->getEmailFromUID($project->uid);
            $assigned = $username . ' (' . $email . ')';
            collapseElement($project->pid, $project->title, $project->description, $project->phase, $project->startDate, $project->endDate, $assigned, true);
        ?>


        <form action="delete.php" method="post" onsubmit="return confirm('Are you sure you want to delete this project? This change will be irreversible!');">
            <input type="hidden" name="pid" value="<?= $project->pid; ?>">
            <input type="hidden" name="token" value="<?= $_SESSION['token']; ?>">
            <a href="projects.php"><button type="button">Return to Projects...</button></a>
            <button type="submit">Delete!</button>
        </form>
    </section>
</main>
</body>