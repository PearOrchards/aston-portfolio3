<?php

/**
 * This singleton class is used to connect to the database.
 */
class Connection {
    private static $dbConnection;

    /**
     * This constructor is private, so that it cannot be called.
     * @throws Exception If the constructor is called. Because you weren't supposed to call it.
     */
    private final function __construct() {
        throw new Exception("Cannot instantiate this class!");
    }

    /**
     * This function is used to initialize the connection.
     */
    private static function init(): void {
        require_once 'vendor/autoload.php'; // Loading the .env module.
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__, "../.env");
        $dotenv->load();

        try {
            $dbname = $_ENV['DBNAME']; // Using .env attributes
            $dbhost = $_ENV['DBHOST'];
            self::$dbConnection = new PDO("mysql:host=$dbhost;dbname=$dbname", $_ENV['DBUSER'], $_ENV['DBPASS'] ?? ""); // Nullish coll for password as it may throw a warning
            self::$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            exit("Database Connection failed: " . $e->getMessage());
        }
    }

    /**
     * This function is used to get the connection.
     * @return PDO The connection.
     */
    public static function getConnection(): PDO {
        if (!isset(self::$dbConnection)) {
            self::init();
        }
        return self::$dbConnection;
    }
}