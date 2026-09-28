<?php
// The Model for the customer table. All the SQL for registering/logging in customers lives here and nowhere else. The Controller just calls these methods and gets back plain arrays/booleans.
require_once __DIR__ . '/../core/db_class.php';

class CustomerClass extends Database {

    // used to stop someone registering twice with the same email
    public function emailExists($email) {
        $stmt = $this->conn->prepare('SELECT customer_email FROM customer WHERE customer_email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    // Note: $pass is already hashed by the time it gets here. The Controller does the hashing, this class just stores whatever string it's given
    public function addCustomer($name, $email, $pass, $country, $city, $contact) {
        try {
            $stmt = $this->conn->prepare(
                'INSERT INTO customer (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            $stmt->bind_param('ssssss', $name, $email, $pass, $country, $city, $contact);
            $stmt->execute();
            $id = $stmt->insert_id;
            $stmt->close();
            return $id;
        } catch (mysqli_sql_exception $ex) {
            // could happen if two people submit the same email at the exact same time
            error_log('addCustomer failed: ' . $ex->getMessage());
            return false;
        }
    }

    public function getCustomerByEmail($email) {
        $stmt = $this->conn->prepare('SELECT * FROM customer WHERE customer_email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: false;
    }

    // Checks the typed password against the stored hash. never compares plain text passwords directly.
    public function login($email, $pass) {
        $row = $this->getCustomerByEmail($email);
        if ($row && password_verify($pass, $row['customer_pass'])) {
            return $row;
        }
        return false;
    }
}