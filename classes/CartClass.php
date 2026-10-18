<?php

require_once __DIR__ . '/../core/db_class.php';

class CartClass extends Database
{
    public function isInCart($product_id, $ip)
    {
        $stmt = $this->conn->prepare(
            'SELECT cart_id
             FROM cart
             WHERE p_id = ? AND ip_add = ?
             LIMIT 1'
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

            $stmt->bind_param(
                'isii',
                $product_id,
                $ip,
                $qty,
                $customer_id
            );
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
            'SELECT
                cart.*,
                products.product_title,
                products.product_price,
                products.product_image
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
            'SELECT COUNT(*) AS total
             FROM cart
             WHERE ip_add = ?'
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
            $total +=
                (int) $item['qty'] *
                (float) $item['product_price'];
        }

        return $total;
    }

    public function removeFromCart($product_id, $ip)
    {
        $stmt = $this->conn->prepare(
            'DELETE FROM cart
             WHERE p_id = ? AND ip_add = ?'
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
            'UPDATE cart
             SET qty = ?
             WHERE p_id = ? AND ip_add = ?'
        );

        $stmt->bind_param(
            'iis',
            $new_qty,
            $product_id,
            $ip
        );

        $success = $stmt->execute();

        $stmt->close();

        return $success;
    }
}