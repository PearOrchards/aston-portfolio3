<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once __DIR__ . '/components/head.php'; ?>
    <title>Project Submission</title>
    <link rel="stylesheet" href="public/styles/forms.css">
</head>
<body>
<?php
    require_once __DIR__ . '/components/navbar.php';
    require_once __DIR__ . '/lib/database.php';
    require_once __DIR__ . '/lib/project.php';

    $db = new Database();

    function handleSubmit(): void {
        global $db; // Use the global database object, rather than redefining one.
        try {
            // CSRF protection
            if (isset($_POST['token']) && hash_equals($_SESSION['token'], $_POST['token'])) {
                unset($_SESSION['token']);
            } else {
                // return 405 http status code
                header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
                exit;
            }

            $startTime = new DateTime($_POST['startDate']);
            $endTime = new DateTime($_POST['endDate']);
            $newProject = new Project(0, $_POST['name'], $startTime, $endTime, $_POST['phase'], $_POST['description'], $_POST['uid']);

            if ($_POST['submitted'] == 'PUT') {
                $db->createProject($newProject);
            } else if ($_POST['submitted'] == 'PATCH') {
                $pid = $_POST['pid'];
                $db->modifyProject($pid, $newProject);
            }

            $_SESSION['snack'] = array(
                'type' => 0,
                'message' => 'Your changes have been recorded.'
            );
        } catch (Exception $e) {
            $_SESSION['snack'] = array(
                'type' => 2,
                'message' => 'There was an issue trying to record your changes: ' . $e->getMessage()
            );
        }

        header('Location: projects.php');
    }

    if (!isset($_SESSION['user'])) { // Login check
        $_SESSION['snack'] = array(
            'type' => 2,
            'message' => 'You must be logged in to access this page.'
        );
        header('Location: login.php');
    } else if (isset($_POST['submitted'])) { // On submit
        handleSubmit();
        header('Location: projects.php');
    } else if (!isset($_GET['mode']) || ( $_GET['mode'] != 'new' && $_GET['mode'] != 'modify') ) { // Not submitting, checking queries
        $_SESSION['snack'] = array(
            'type' => 2,
            'message' => 'You must specify a valid mode to access this page.'
        );
        header('Location: projects.php');
    } else if ($_GET['mode'] == 'modify' && !isset($_GET['pid'])) { // modify query missing a pid
        $_SESSION['snack'] = array(
            'type' => 2,
            'message' => 'You must specify a valid project ID to access this page.'
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

    // All good, let's go!!!!
    $keyword = null; $project = null;
    if (isset($_GET['mode']) && $_GET['mode'] == 'modify') {
        $keyword = "Modify";
        try {
            $project = $db->getProject($_GET['pid']);
        } catch (Exception $e) {
            $_SESSION['snack'] = array(
                'type' => 2,
                'message' => 'There was an issue trying to fetch the project.'
            );
            header('Location: projects.php');
        }
    } else {
        $keyword = "Add";
    }
?>
<header id="main-header">
    <div class="col">
        <h1>Projects</h1>
        <h2><?= $keyword ?> a project here!</h2>
    </div>
</header>
<main>
    <section class="formSection">
        <h2><?= $keyword ?> Project</h2>
        <a href="projects.php">Return to projects...</a>
        <form action="upsert.php" method="post" onsubmit="return confirm('Are you sure you want to do this? This change will be irreversible!');">
            <!-- Doing this as HTML forms do not officially support PUT or PATCH methods. But we'll do this, so we know how to process the request once it's submitted -->
            <input type="hidden" name="submitted" value="<?= (isset($_GET['mode']) && $_GET['mode'] == 'new') ? 'PUT' : 'PATCH' ?>">
            <!-- Values are only set when updating (PATCH) -->
            <input type="hidden" name="pid" value="<?= $project->pid ?? null ?>">
            <!-- CSRF token -->
            <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
            <div class="row">
                <div class="settingWrapper">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" class="rainbow-1" placeholder="Enter your project name..." value="<?= $project->title ?? null ?>" required>
                </div>
                <div class="settingWrapper shrunk">
                    <label for="phase">Phase</label>
                    <select name="phase" id="phase" class="rainbow-2">
                        <option value="design" <?= (isset($project) && $project->phase == "design") ? "selected" : "" ?>>Design</option>
                        <option value="development" <?= (isset($project) && $project->phase == "development") ? "selected" : "" ?>>Development</option>
                        <option value="testing" <?= (isset($project) && $project->phase == "testing") ? "selected" : "" ?>>Testing</option>
                        <option value="deployment" <?= (isset($project) && $project->phase == "deployment") ? "selected" : "" ?>>Deployment</option>
                        <option value="complete" <?= (isset($project) && $project->phase == "complete") ? "selected" : "" ?>>Complete</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="settingWrapper">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="rainbow-3" placeholder="Enter a description for your project..." rows="5" required><?= $project->description ?? null ?></textarea>
                </div>
            </div>
            <div class="row">
                <div class="settingWrapper shrunk">
                    <label for="startDate">Start Date</label>
                    <input type="date" name="startDate" id="startDate" class="rainbow-4" value="<?= (isset($project)) ? $project->startDate->format("Y-m-d") : null ?>" required>
                </div>
                <div class="settingWrapper shrunk">
                    <label for="endDate">End Date</label>
                    <input type="date" name="endDate" id="endDate" class="rainbow-5" value="<?= (isset($project)) ? $project->endDate->format("Y-m-d") : null ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="settingWrapper shrunk">
                    <label for="uid">Assigned to:</label>
                    <select name="uid" id="uid" class="rainbow-6">
                        <?php
                            // $db is already a thing.
                            $users = $db->listUsers();
                            foreach ($users as $user) {
                                $pretty = htmlspecialchars($user['uid'] . " - " . $user['username']);
                                $selected = (isset($project) && $project->uid == $user['uid']) ? "selected" : "";
                                $uid = htmlspecialchars($user['uid']);
                                echo "<option value='$uid' $selected>$pretty</option>";
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <button type="submit" class="rainbow-7"><?= $keyword ?>!</button>
            </div>
        </form>
    </section>
</main>
<?php require_once __DIR__ . '/components/footer.php'; ?>
</body>
</html>