<?php
/**
 * Database + site configuration.
 * Site at domain root: BASE_URL = '' (use '/booknest' if served from a subfolder)
 * INFINITYFREE: set BASE_URL to '' and use the credentials from your control panel.
 */
define('BASE_URL', '');

class Database {
    private $host = "localhost";
    private $db_name = "booknest_db";
    private $username = "root";
    private $password = "";

    public function getConnection() {
        try {
            $conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
            return $conn;
        } catch (PDOException $e) {
            die("Connection error: " . $e->getMessage());
        }
    }
}
