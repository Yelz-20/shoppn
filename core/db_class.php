<?php
// Base class every Model extends. This is the only file that actually opens a connection to MySQL. Everything else just inherits $this->conn.
class Database {

    protected $conn; // protected so the Model classes that extend this can use it

    public function __construct() {
        require_once __DIR__ . '/db_cred.php';

        try {
            $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

            // Depending on the PHP version this either sets connect_error or throws straight away, so just handle both
            if ($this->conn->connect_error) {
                throw new mysqli_sql_exception($this->conn->connect_error);
            }

            $this->conn->set_charset('utf8mb4'); // so names with accents/emoji don't break
        } catch (mysqli_sql_exception $ex) {
            // Don't leak the real DB error to whoever's looking at the page, just log it for us and show something generic
            error_log('DB connection failed: ' . $ex->getMessage());
            die('Connection failed.');
        }
    }
}