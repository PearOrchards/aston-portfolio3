<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once __DIR__ . '/components/head.php'; ?>
    <title>Projects</title>
    <link rel="stylesheet" href="./public/styles/projects.css">
    <script src="./public/js/collapsibleHandler.js" defer async></script>
    <script src="./public/js/search.js" defer async></script>
</head>
<body>
<?php require_once __DIR__ . '/components/navbar.php'; ?>
<header id="main-header">
    <div class="col">
        <h1>Projects</h1>
        <h2>See all the projects that we're working on!</h2>
    </div>
</header>
<main>
    <section class="projects">
        <!-- Each project will be represented by a row, which can then be expanded to show more details. -->
        <!-- An add button, as well as modify and delete options on the projects, are available to logged-in users. -->

        <form class="row bottom search" action="projects.php" method="post">
            <div class="col">
                <!-- This first one isn't required in case you want to "search an empty string", which will reset the search -->
                <label for="searchText">Search: </label>
                <input type="text" name="searchText" id="searchText" placeholder="Search by name">
            </div>
            <div class="col">
                <label for="searchBy">Search by: </label>
                <select name="searchBy" id="searchBy" required>
                    <option value="name" selected>Name</option>
                    <option value="startDate">Start Date</option>
                    <option value="endDate">End Date</option>
                </select>
            </div>
            <div class="col bottom">
                <button type="submit" id="searchButton"><i class="fa-solid fa-magnifying-glass"></i></button>
            </div>
        </form>

        <?php
            require_once __DIR__ . '/lib/project.php';
            require_once __DIR__ . '/lib/user.php';
            require_once __DIR__ . '/components/collapseElement.php';

            $pdb = new ProjectDatabase();
            $udb = new UserDatabase();
            $projects = null;

            if (isset($_POST['searchBy'])) {
                switch ($_POST['searchBy']) {
                    case "startDate":
                        try {
                            $date = new DateTime($_POST['searchText']);
                            $projects = $pdb->getProjectsByStartDate($date);
                        } catch (Exception $e) {
                            echo '<p class="error">Error trying to search by date!</p>';
                        }
                        break;

                    case "endDate":
                        try {
                            $date = new DateTime($_POST['searchText']);
                            $projects = $pdb->getProjectsByEndDate($date);
                        } catch (Exception $e) {
                            echo '<p class="error">Error trying to search by date!</p>';
                        }
                        break;

                    case "name":
                        if ($_POST['searchText'] != '') {
                            $projects = $pdb->getProjectsByName(htmlspecialchars($_POST['searchText']));
                            break;
                        }

                    default:
                        $projects = $pdb->getAllProjects();
                        break;
                }
            } else $projects = $pdb->getAllProjects();

            foreach ($projects as $project) {
                $username = $udb->getNameFromUID($project->uid);
                $email = $udb->getEmailFromUID($project->uid);
                $assigned = $username . ' (' . $email . ')';
                collapseElement($project->pid, $project->title, $project->description, $project->phase, $project->startDate, $project->endDate, $assigned);
            }

            if (isset($_SESSION['user'])) {
                require_once __DIR__ . '/components/addProjectButton.php';
            }
        ?>
    </section>
</main>
<?php require_once __DIR__ . '/components/footer.php'; ?>
</body>