<?php
    require __DIR__ . '/preventDirectAccess.php'; // Stop direct access to this file

    class Database {
        private PDO $conn;

        public function __construct() {
            require 'vendor/autoload.php'; // Loads the .env module
            $dotenv = Dotenv\Dotenv::createImmutable(__DIR__, "../.env");
            $dotenv->load();

            try {
                $dbname = $_ENV['DBNAME']; // Using .env attributes
                $dbhost = $_ENV['DBHOST'];
                $this->conn = new PDO("mysql:host=$dbhost;dbname=$dbname", $_ENV['DBUSER'], $_ENV['DBPASS'] ?? ""); // Nullish coll for password as it may throw a warning
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch(PDOException $e) {
                exit("Database Connection failed: " . $e->getMessage());
            }
        }

        /**
         * Creates a new user, and returns it, or throws an exception if there's an issue.
         * @throws Exception on error.
         */
        public function createUser($username, $email, $password): User  {
            // First check a user with that username doesn't already exist.
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                throw new Exception("User with that username already exists");
            }

            // Hash password, then insert into database.
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("INSERT INTO users (username, password, email) VALUES (:username, :password, :email)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            // Return the new user (not password).
            return new User($username, $email);
        }

        /**
         * Logins in the user, checking the username and password, before returning it, or throws an exception if there's an issue.
         * @param $username
         * @param $password
         * @return User
         * @throws Exception if there's an issue.
         */
        public function login($username, $password): User {
            // Prepare statement to get user from database, then execute (prevents SQL injection)
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the result as an associative array.
            if (!$result) {
                throw new Exception("No user found");
            } else if (!password_verify($password, $result['password'])) {
                throw new Exception("Incorrect password");
            } else {
                return new User($result['username'], $result['email']);
            }
        }

        /**
         * Simply returns a username from a uid.
         * @param $uid - uid of the user to search for.
         * @return string The name of the user with the given uid.
         */
        public function getNameFromUID($uid): string {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE uid = :uid");
            $stmt->bindParam(':uid', $uid);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['username'];
        }

        /**
         * Returns an email from a uid.
         * @param $uid - uid of the user to search for.
         * @return string The email of the user with the given uid.
         */
        public function getEmailFromUID($uid): string{
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE uid = :uid");
            $stmt->bindParam(':uid', $uid);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['email'];
        }

        /**
         * Lists all the user ids and names of all users in the database.
         * @return string[][] - An array of all users, where each user is an array split into uid and username.
         */
        public function listUsers(): array {
            $stmt = $this->conn->prepare("SELECT * FROM users");
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $users = array();

            foreach ($results as $user) {
                $users[] = array('uid' => $user['uid'], 'username' => $user['username']);
            }

            return $users;
        }

        /**
         * Returns all the projects in the database.
         * @return Project[] An array of all projects.
         */
        public function getAllProjects(): array {
            $stmt = $this->conn->prepare("SELECT * FROM projects");
            return $this->executeAndReturnProjects($stmt);
        }

        /**
         * Returns all projects with a given or similar name.
         * @param string $name The name to search for.
         * @return Project[] An array of all projects with a given/similar name.
         */
        public function getProjectsByName(string $name): array {
            $stmt = $this->conn->prepare("SELECT * FROM projects WHERE title LIKE :name");
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
            $stmt = $this->conn->prepare("SELECT * FROM projects WHERE end_date = :date");
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
            $stmt = $this->conn->prepare("SELECT * FROM projects WHERE pid = :pid");
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
            $stmt = $this->conn->prepare("INSERT INTO projects (title, start_date, end_date, phase, description, uid) VALUES (:title, :startDate, :endDate, :phase, :description, :uid)");
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
            $stmt = $this->conn->prepare("UPDATE projects SET title = :title, start_date = :startDate, end_date = :endDate, phase = :phase, description = :description, uid = :uid WHERE pid = :pid");
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
            $stmt = $this->conn->prepare("DELETE FROM projects WHERE pid = :pid");
            $stmt->execute(array(':pid' => $pid));

            // check if one row was affected
            if ($stmt->rowCount() != 1) {
                throw new Exception("Failed to delete project");
            };

            return true;
        }
    }