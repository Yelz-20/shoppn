<?php
// Sits between the Action files and the Model. Doesn't touch SQL, doesn't echo anything, doesn't redirect, just calls CustomerClass and hands back a simple result the Action can act on.
require_once __DIR__ . '/../classes/CustomerClass.php';

class CustomerController {

    private $customer;

    public function __construct() {
        $this->customer = new CustomerClass();
    }

    // returns ['success' => true, 'customer' => [...]] or
    //         ['success' => false, 'error' => '...']
    public function register(array $data) {
        if ($this->customer->emailExists($data['email'])) {
            return ['success' => false, 'error' => 'Email already registered'];
        }

        // hashing happens right here, so this is the one place in the whole app that decides how passwords get protected
        $hash = password_hash($data['password'], PASSWORD_BCRYPT);

        $id = $this->customer->addCustomer(
            $data['name'], $data['email'], $hash,
            $data['country'], $data['city'], $data['contact']
        );

        if (!$id) {
            return ['success' => false, 'error' => 'Registration failed. Please try again.'];
        }

        // pull the row back so we return real data (customer_id etc), not just the values we were given
        $row = $this->customer->getCustomerByEmail($data['email']);
        unset($row['customer_pass']); // don't let the hash leave this layer
        return ['success' => true, 'customer' => $row];
    }

    public function login($email, $pass) {
        $row = $this->customer->login($email, $pass);
        if (!$row) {
            return ['success' => false, 'error' => 'Invalid email or password'];
        }
        unset($row['customer_pass']);
        return ['success' => true, 'customer' => $row];
    }
}