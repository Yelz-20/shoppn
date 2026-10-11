<?php
require_once __DIR__ . '/../core/db_class.php';

class ProductClass extends Database
{
    public function addBrand($name)
    {
        $stmt = $this->conn->prepare('INSERT INTO brands (brand_name) VALUES (?)');
        $stmt->bind_param('s', $name);
        $ok = $stmt->execute();
        $id = $ok ? $this->conn->insert_id : false;
        $stmt->close();
        return $id;
    }

    public function getAllBrands()
    {
        $result = $this->conn->query('SELECT * FROM brands ORDER BY brand_name ASC');
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getBrandById($id)
    {
        $stmt = $this->conn->prepare('SELECT * FROM brands WHERE brand_id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: false;
    }

    public function updateBrand($id, $name)
    {
        $stmt = $this->conn->prepare('UPDATE brands SET brand_name = ? WHERE brand_id = ?');
        $stmt->bind_param('si', $name, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function addCategory($name)
    {
        $stmt = $this->conn->prepare('INSERT INTO categories (cat_name) VALUES (?)');
        $stmt->bind_param('s', $name);
        $ok = $stmt->execute();
        $id = $ok ? $this->conn->insert_id : false;
        $stmt->close();
        return $id;
    }

    public function getAllCategories()
    {
        $result = $this->conn->query('SELECT * FROM categories ORDER BY cat_name ASC');
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getCategoryById($id)
    {
        $stmt = $this->conn->prepare('SELECT * FROM categories WHERE cat_id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: false;
    }

    public function updateCategory($id, $name)
    {
        $stmt = $this->conn->prepare('UPDATE categories SET cat_name = ? WHERE cat_id = ?');
        $stmt->bind_param('si', $name, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}