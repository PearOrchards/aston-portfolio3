<?php
    require __DIR__ . '/preventDirectAccess.php'; // Stop direct access to this file

/**
 * Again, we treat this more as an object, rather than a class. I love OOP.
 */
class Project {
    public int $pid;
    public string $title;
    public DateTime $startDate;
    public DateTime $endDate;
    public string $phase;
    public string $description;
    public int $uid;

    public function __construct(?int $pid, string $title, DateTime $startDate, DateTime $endDate, string $phase, string $description, int $uid) {
        $this->pid = $pid;
        $this->title = $title;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->phase = $phase;
        $this->description = $description;
        $this->uid = $uid;
    }
}

/**
 * This class is used to hold commands for the project table.
 */
class ProjectDatabase {
    private PDO $projectConnection;

    public function __construct() {
        require_once __DIR__ . '/connection.php';
        $this->projectConnection = Connection::getConnection();
    }

    /**
     * Returns all the projects in the database.
     * @return Project[] An array of all projects.
     */
    public function getAllProjects(): array {
        $stmt = $this->projectConnection->prepare("SELECT * FROM projects");
        return $this->executeAndReturnProjects($stmt);
    }

    /**
     * Returns all projects with a given or similar name.
     * @param string $name The name to search for.
     * @return Project[] An array of all projects with a given/similar name.
     */
    public function getProjectsByName(string $name): array {
        $stmt = $this->projectConnection->prepare("SELECT * FROM projects WHERE title LIKE :name");
        $searchStr = "%" . $name . "%";
        $stmt->bindParam(':name', $searchStr);
        return $this->executeAndReturnProjects($stmt);
    }

    /**
     * Returns all projects with a given end date.
     * @param DateTime $date The end date to search for.
     * @return Project[] An array of all projects with the given date
     */
    public function getProjectsByEndDate(DateTime $date): array {
        $stmt = $this->projectConnection->prepare("SELECT * FROM projects WHERE end_date = :date");
        $correctDate = $date->format('Y-m-d');
        $stmt->bindParam(':date', $correctDate);
        return $this->executeAndReturnProjects($stmt);
    }

    /**
     * Returns all projects with a given start date.
     * @param DateTime $date The start date to search for.
     * @return Project[] An array of all projects with the given date
     */
    public function getProjectsByStartDate(DateTime $date): array {
        $stmt = $this->projectConnection->prepare("SELECT * FROM projects WHERE start_date = :date");
        $correctDate = $date->format('Y-m-d');
        $stmt->bindParam(':date', $correctDate);
        return $this->executeAndReturnProjects($stmt);
    }

    /**
     * INTERNAL USE ONLY: Executes a statement and returns the projects to remove duplicate code.
     * @param $stmt PDOStatement The statement to execute.
     * @return Project[] An array of all projects returned by the statement.
     */
    private function executeAndReturnProjects(PDOStatement $stmt): array {
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $projects = array();
        foreach ($results as $project) {
            try {
                $startDate = new DateTime($project['start_date']);
                $endDate = new DateTime($project['end_date']);
                $projects[] = new Project($project['pid'], $project['title'], $startDate, $endDate, $project['phase'], $project['description'], $project['uid']);
            } catch (Exception $e) { /* just skip */ }
        }
        return $projects;
    }

    /**
     * Returns a project with the given pid.
     * @param int $pid The pid of the project to get.
     * @return Project The project with the given pid.
     * @throws Exception if there's an issue.
     */
    public function getProject(int $pid): Project {
        $stmt = $this->projectConnection->prepare("SELECT * FROM projects WHERE pid = :pid");
        $stmt->bindParam(':pid', $pid);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            throw new Exception("No project found");
        } else {
            $startDate = new DateTime($result['start_date']);
            $endDate = new DateTime($result['end_date']);
            return new Project($result['pid'], $result['title'], $startDate, $endDate, $result['phase'], $result['description'], $result['uid']);
        }
    }

    /**
     * Creates a new project or throws an exception if there's an issue.
     * @param Project $project The project to create.
     * @return bool True if successful.
     * @throws Exception on error.
     */
    public function createProject(Project $project): bool {
        // Check all the fields are set. Except pid, as that's auto-incremented.
        if (!$project->title || !$project->startDate || !$project->endDate || !$project->phase || !$project->description || !$project->uid) {
            throw new Exception("All fields must be set");
        }

        // Insert into database.
        $stmt = $this->projectConnection->prepare("INSERT INTO projects (title, start_date, end_date, phase, description, uid) VALUES (:title, :startDate, :endDate, :phase, :description, :uid)");
        $stmt->execute(array(
            ':title' => $project->title,
            ':startDate' => $project->startDate->format('Y-m-d'),
            ':endDate' => $project->endDate->format('Y-m-d'),
            ':phase' => $project->phase,
            ':description' => $project->description,
            ':uid' => $project->uid
        ));

        // check if one row was created
        if($stmt->rowCount() != 1) {
            throw new Exception("Failed to create project");
        };

        return true; // we are good.
    }

    /**
     * Modifies a project with the given pid, and a Project object.
     * @param int $pid
     * @param Project $project
     * @return bool True if successful.
     * @throws Exception on error.
     */
    public function modifyProject(int $pid, Project $project): bool {
        // Check all the fields are set. Except pid again.
        if (!$project->title || !$project->startDate || !$project->endDate || !$project->phase || !$project->description || !$project->uid) {
            throw new Exception("All fields must be set in the project object");
        }

        // Check the project exists.
        try {
            $oldProj = $this->getProject($pid);
        } catch (Exception $e) {
            throw new Exception("Project to update does not exist!");
        }

        // Check any change has been made.
        if ($oldProj->title == $project->title
            && $oldProj->startDate == $project->startDate
            && $oldProj->endDate == $project->endDate
            && $oldProj->phase == $project->phase
            && $oldProj->description == $project->description
            && $oldProj->uid == $project->uid) {
            throw new Exception("You did not make any changes!");
        }

        // Update the project in the database.
        $stmt = $this->projectConnection->prepare("UPDATE projects SET title = :title, start_date = :startDate, end_date = :endDate, phase = :phase, description = :description, uid = :uid WHERE pid = :pid");
        $stmt->execute(array(
            ':title' => $project->title,
            ':startDate' => $project->startDate->format('Y-m-d'),
            ':endDate' => $project->endDate->format('Y-m-d'),
            ':phase' => $project->phase,
            ':description' => $project->description,
            ':uid' => $project->uid,
            ':pid' => $pid
        ));

        // check if one row was changed
        if($stmt->rowCount() != 1) {
            throw new Exception("Failed to modify project");
        };

        return true; // we are good.
    }

    /**
     * Deletes a project with the given pid.
     * @param int $pid
     * @return bool True if successful.
     * @throws Exception on error.
     */
    public function deleteProject(int $pid): bool {
        // Delete the project from the database.
        $stmt = $this->projectConnection->prepare("DELETE FROM projects WHERE pid = :pid");
        $stmt->execute(array(':pid' => $pid));

        // check if one row was affected
        if ($stmt->rowCount() != 1) {
            throw new Exception("Failed to delete project");
        };

        return true;
    }
}