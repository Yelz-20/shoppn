
<?php

require_once __DIR__ . '/../core/db_class.php';

class CartClass extends Database
{
    public function isInCart($product_id, $ip)
    {
        $stmt = $this->conn->prepare(
            'SELECT cart_id FROM cart
             WHERE p_id = ? AND ip_add = ? LIMIT 1'
        );

        $stmt->bind_param('is', $product_id, $ip);
        $stmt->execute();

        $exists = $stmt->get_result()->num_rows > 0;
        $stmt->close();

        return $exists;
    }

    public function addToCart($product_id, $qty, $ip, $customer_id = null)
    {
        $product_id = (int) $product_id;
        $qty = (int) $qty;

        if ($product_id < 1 || $qty < 1) {
            return false;
        }

        if ($customer_id !== null) {
            $stmt = $this->conn->prepare(
                'INSERT INTO cart (p_id, ip_add, qty, customer_id)
                 VALUES (?, ?, ?, ?)'
            );

            $stmt->bind_param('isii', $product_id, $ip, $qty, $customer_id);
        } else {
            $stmt = $this->conn->prepare(
                'INSERT INTO cart (p_id, ip_add, qty)
                 VALUES (?, ?, ?)'
            );

            $stmt->bind_param('isi', $product_id, $ip, $qty);
        }

        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    public function getCartItems($ip)
    {
        $stmt = $this->conn->prepare(
            'SELECT cart.*, products.product_title,
                    products.product_price, products.product_image
             FROM cart
             INNER JOIN products
                ON cart.p_id = products.product_id
             WHERE cart.ip_add = ?
             ORDER BY cart.cart_id DESC'
        );

        $stmt->bind_param('s', $ip);
        $stmt->execute();

        $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $items;
    }

    public function getCartCount($ip)
    {
        $stmt = $this->conn->prepare(
            'SELECT COUNT(*) AS total FROM cart WHERE ip_add = ?'
        );

        $stmt->bind_param('s', $ip);
        $stmt->execute();

        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return (int) ($row['total'] ?? 0);
    }

    public function getCartTotal($ip)
    {
        $items = $this->getCartItems($ip);
        $total = 0;

        foreach ($items as $item) {
            $total += (int) $item['qty'] *
                      (float) $item['product_price'];
        }

        return round($total, 2);
    }

    public function removeFromCart($product_id, $ip)
    {
        $stmt = $this->conn->prepare(
            'DELETE FROM cart WHERE p_id = ? AND ip_add = ?'
        );

        $stmt->bind_param('is', $product_id, $ip);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    public function updateQty($product_id, $new_qty, $ip)
    {
        $product_id = (int) $product_id;
        $new_qty = (int) $new_qty;

        if ($product_id < 1 || $new_qty < 1) {
            return false;
        }

        $stmt = $this->conn->prepare(
            'UPDATE cart SET qty = ?
             WHERE p_id = ? AND ip_add = ?'
        );

        $stmt->bind_param('iis', $new_qty, $product_id, $ip);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    public function getCustomerEmail($customer_id)
    {
        $stmt = $this->conn->prepare(
            'SELECT customer_email
             FROM customer
             WHERE customer_id = ?
             LIMIT 1'
        );

        $stmt->bind_param('i', $customer_id);
        $stmt->execute();

        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row['customer_email'] ?? false;
    }

    public function processPayment($customer_id, $ip, $reference, $amount_pesewas)
    {
        $this->conn->begin_transaction();

        try {
            // Check whether this reference has already created an order.
            $stmt = $this->conn->prepare(
                'SELECT order_id, customer_id
                 FROM orders
                 WHERE payment_reference = ?
                 LIMIT 1'
            );

            $stmt->bind_param('s', $reference);
            $stmt->execute();

            $existing = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($existing) {
                if ((int) $existing['customer_id'] !== (int) $customer_id) {
                    throw new Exception('Payment reference belongs to another customer.');
                }

                $this->conn->commit();
                return (int) $existing['order_id'];
            }

            $items = $this->getCartItems($ip);

            if (empty($items)) {
                throw new Exception('Cart is empty.');
            }

            $cart_total = $this->getCartTotal($ip);
            $cart_pesewas = (int) round($cart_total * 100);

            if ($cart_pesewas !== (int) $amount_pesewas) {
                throw new Exception('Cart amount does not match payment.');
            }

            $invoice_no = random_int(100000, 999999);

            $stmt = $this->conn->prepare(
                "INSERT INTO orders
                 (customer_id, invoice_no, order_status, payment_reference)
                 VALUES (?, ?, 'paid', ?)"
            );

            $stmt->bind_param(
                'iis',
                $customer_id,
                $invoice_no,
                $reference
            );

            if (!$stmt->execute()) {
                throw new Exception('Could not create order.');
            }

            $order_id = $this->conn->insert_id;
            $stmt->close();

            $stmt = $this->conn->prepare(
                'INSERT INTO orderdetails (order_id, product_id, qty)
                 SELECT ?, p_id, qty
                 FROM cart
                 WHERE ip_add = ?'
            );

            $stmt->bind_param('is', $order_id, $ip);

            if (!$stmt->execute() || $stmt->affected_rows < 1) {
                throw new Exception('Could not save order details.');
            }

            $stmt->close();

            $stmt = $this->conn->prepare(
                'DELETE FROM cart WHERE ip_add = ?'
            );

            $stmt->bind_param('s', $ip);
            $stmt->execute();
            $stmt->close();

            $this->conn->commit();

            return $order_id;

        } catch (Throwable $e) {
            $this->conn->rollback();

            error_log('Shoppn payment error: ' . $e->getMessage());

            return false;
        }
    }

    public function getOrderById($order_id)
    {
        $stmt = $this->conn->prepare(
            'SELECT orders.*, customer.customer_name,
                    customer.customer_email
             FROM orders
             INNER JOIN customer
                ON orders.customer_id = customer.customer_id
             WHERE orders.order_id = ?
             LIMIT 1'
        );

        $stmt->bind_param('i', $order_id);
        $stmt->execute();

        $order = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$order) {
            return false;
        }

        $stmt = $this->conn->prepare(
            'SELECT orderdetails.product_id,
                    orderdetails.qty,
                    products.product_title,
                    products.product_price
             FROM orderdetails
             INNER JOIN products
                ON orderdetails.product_id = products.product_id
             WHERE orderdetails.order_id = ?'
        );

        $stmt->bind_param('i', $order_id);
        $stmt->execute();

        $order['items'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        $order['total'] = 0;

        foreach ($order['items'] as $item) {
            $order['total'] +=
                (int) $item['qty'] *
                (float) $item['product_price'];
        }

        return $order;
    }
}
