<?php
    require __DIR__ . '/preventDirectAccess.php'; // Stop direct access to this file

/**
 * This "class" is simply used to store user data when returning from a function. It's more an object than a class.
 */
class User {
    public string $username;
    public string $email;

    public function __construct($username, $email) {
        $this->username = $username;
        $this->email = $email;
    }
}

class UserDatabase {
    private PDO $userConnection;

    public function __construct() {
        require_once __DIR__ . '/connection.php';
        $this->userConnection = Connection::getConnection();
    }

    /**
     * Creates a new user, and returns it, or throws an exception if there's an issue.
     * @throws Exception on error.
     */
    public function createUser($username, $email, $password): User  {
        // First check a user with that username doesn't already exist.
        $stmt = $this->userConnection->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            throw new Exception("User with that username already exists!");
        }

        // Check a user with that email doesn't already exist.
        $stmt = $this->userConnection->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            throw new Exception("User with that email already exists! Have you tried logging in?");
        }

        // Hash password, then insert into database.
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->userConnection->prepare("INSERT INTO users (username, password, email) VALUES (:username, :password, :email)");
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
        $stmt = $this->userConnection->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the result as an associative array.
        if (!$result || !password_verify($password, $result['password'])) {
            throw new Exception("No user found!"); // Throw same exception as if user doesn't exist, to prevent leaking information.
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
        $stmt = $this->userConnection->prepare("SELECT * FROM users WHERE uid = :uid");
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
        $stmt = $this->userConnection->prepare("SELECT * FROM users WHERE uid = :uid");
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
        $stmt = $this->userConnection->prepare("SELECT * FROM users");
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $users = array();

        foreach ($results as $user) {
            $users[] = array('uid' => $user['uid'], 'username' => $user['username']);
        }

        return $users;
    }
}