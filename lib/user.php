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