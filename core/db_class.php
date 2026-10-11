<?php
// Base class every Model extends.
// This is the only file that opens a connection to MySQL.
// Everything else just inherits $this->conn.

class Database
{
    // Protected so Model classes that extend this class can use the connection.
    protected $conn;

    public function __construct()
    {
        // Load the database credentials from their separate file.
        require_once __DIR__ . '/db_cred.php';

        try {
            // Open the database connection using the stored credentials.
            $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

            // If MySQL reports a connection error, handle it below.
            if ($this->conn->connect_error) {
                throw new mysqli_sql_exception($this->conn->connect_error);
            }

            // Keep text, names, and special characters working correctly.
            $this->conn->set_charset('utf8mb4');
        } catch (mysqli_sql_exception $ex) {
            // Save the detailed error in the server log.
            error_log('DB connection failed: ' . $ex->getMessage());

            // Show visitors only a safe general message.
            die('Connection failed.');
        }
    }
}