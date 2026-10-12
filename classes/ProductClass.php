<?php

require_once __DIR__ . '/../core/db_class.php';

class ProductClass extends Database
{
    // ==========================================
    // BRAND MANAGEMENT
    // ==========================================

    public function addBrand($name)
    {
        $stmt = $this->conn->prepare(
            'INSERT INTO brands (brand_name) VALUES (?)'
        );

        $stmt->bind_param('s', $name);
        $ok = $stmt->execute();

        $id = $ok ? $this->conn->insert_id : false;

        $stmt->close();

        return $id;
    }

    public function getAllBrands()
    {
        $result = $this->conn->query(
            'SELECT * FROM brands ORDER BY brand_name ASC'
        );

        return $result
            ? $result->fetch_all(MYSQLI_ASSOC)
            : [];
    }

    public function getBrandById($id)
    {
        $stmt = $this->conn->prepare(
            'SELECT * FROM brands WHERE brand_id = ?'
        );

        $stmt->bind_param('i', $id);
        $stmt->execute();

        $row = $stmt->get_result()->fetch_assoc();

        $stmt->close();

        return $row ?: false;
    }

    public function updateBrand($id, $name)
    {
        $stmt = $this->conn->prepare(
            'UPDATE brands
             SET brand_name = ?
             WHERE brand_id = ?'
        );

        $stmt->bind_param('si', $name, $id);
        $ok = $stmt->execute();

        $stmt->close();

        return $ok;
    }


    // ==========================================
    // CATEGORY MANAGEMENT
    // ==========================================

    public function addCategory($name)
    {
        $stmt = $this->conn->prepare(
            'INSERT INTO categories (cat_name) VALUES (?)'
        );

        $stmt->bind_param('s', $name);
        $ok = $stmt->execute();

        $id = $ok ? $this->conn->insert_id : false;

        $stmt->close();

        return $id;
    }

    public function getAllCategories()
    {
        $result = $this->conn->query(
            'SELECT * FROM categories ORDER BY cat_name ASC'
        );

        return $result
            ? $result->fetch_all(MYSQLI_ASSOC)
            : [];
    }

    public function getCategoryById($id)
    {
        $stmt = $this->conn->prepare(
            'SELECT * FROM categories WHERE cat_id = ?'
        );

        $stmt->bind_param('i', $id);
        $stmt->execute();

        $row = $stmt->get_result()->fetch_assoc();

        $stmt->close();

        return $row ?: false;
    }

    public function updateCategory($id, $name)
    {
        $stmt = $this->conn->prepare(
            'UPDATE categories
             SET cat_name = ?
             WHERE cat_id = ?'
        );

        $stmt->bind_param('si', $name, $id);
        $ok = $stmt->execute();

        $stmt->close();

        return $ok;
    }


    // ==========================================
    // TASK 9: ADD PRODUCT
    // ==========================================

    public function addProduct(
        $cat,
        $brand,
        $title,
        $price,
        $desc,
        $image_filename,
        $keywords
    ) {
        $price = (float) $price;

        $stmt = $this->conn->prepare(
            'INSERT INTO products (
                product_cat,
                product_brand,
                product_title,
                product_price,
                product_desc,
                product_image,
                product_keywords
            ) VALUES (?, ?, ?, ?, ?, ?, ?)'
        );

        $stmt->bind_param(
            'iisdsss',
            $cat,
            $brand,
            $title,
            $price,
            $desc,
            $image_filename,
            $keywords
        );

        $ok = $stmt->execute();

        $id = $ok ? $this->conn->insert_id : false;

        $stmt->close();

        return $id;
    }


    // ==========================================
    // TASK 9: UPDATE PRODUCT
    // ==========================================

    public function updateProduct(
        $id,
        $cat,
        $brand,
        $title,
        $price,
        $desc,
        $image_filename,
        $keywords
    ) {
        $price = (float) $price;

        $stmt = $this->conn->prepare(
            'UPDATE products
             SET product_cat = ?,
                 product_brand = ?,
                 product_title = ?,
                 product_price = ?,
                 product_desc = ?,
                 product_image = ?,
                 product_keywords = ?
             WHERE product_id = ?'
        );

        $stmt->bind_param(
            'iisdsssi',
            $cat,
            $brand,
            $title,
            $price,
            $desc,
            $image_filename,
            $keywords,
            $id
        );

        $ok = $stmt->execute();

        $stmt->close();

        return $ok;
    }


    // ==========================================
    // TASK 9: GET PRODUCT BY ID
    // ==========================================

    public function getProductById($id)
    {
        $stmt = $this->conn->prepare(
            'SELECT
                p.*,
                c.cat_name,
                b.brand_name
             FROM products p
             LEFT JOIN categories c
                ON p.product_cat = c.cat_id
             LEFT JOIN brands b
                ON p.product_brand = b.brand_id
             WHERE p.product_id = ?'
        );

        $stmt->bind_param('i', $id);
        $stmt->execute();

        $row = $stmt->get_result()->fetch_assoc();

        $stmt->close();

        return $row ?: false;
    }


    // ==========================================
    // TASK 10: GET ALL PRODUCTS
    // ==========================================

    public function getAllProducts()
    {
        $result = $this->conn->query(
            'SELECT
                p.*,
                c.cat_name,
                b.brand_name
             FROM products p
             LEFT JOIN categories c
                ON p.product_cat = c.cat_id
             LEFT JOIN brands b
                ON p.product_brand = b.brand_id
             ORDER BY p.product_id DESC'
        );

        return $result
            ? $result->fetch_all(MYSQLI_ASSOC)
            : [];
    }


    // ==========================================
    // TASK 10: GET FEATURED PRODUCTS
    // ==========================================

    public function getFeaturedProducts($limit = 6)
    {
        $limit = max(1, min(50, (int) $limit));

        $stmt = $this->conn->prepare(
            'SELECT
                p.*,
                c.cat_name,
                b.brand_name
             FROM products p
             LEFT JOIN categories c
                ON p.product_cat = c.cat_id
             LEFT JOIN brands b
                ON p.product_brand = b.brand_id
             ORDER BY RAND()
             LIMIT ?'
        );

        $stmt->bind_param('i', $limit);
        $stmt->execute();

        $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        return $products ?: [];
    }


    // ==========================================
    // TASK 10: GET PRODUCTS BY CATEGORY
    // ==========================================

    public function getProductsByCategory($cat_id)
    {
        $stmt = $this->conn->prepare(
            'SELECT
                p.*,
                c.cat_name,
                b.brand_name
             FROM products p
             LEFT JOIN categories c
                ON p.product_cat = c.cat_id
             LEFT JOIN brands b
                ON p.product_brand = b.brand_id
             WHERE p.product_cat = ?
             ORDER BY p.product_title ASC'
        );

        $stmt->bind_param('i', $cat_id);
        $stmt->execute();

        $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        return $products ?: [];
    }


    // ==========================================
    // TASK 10: GET PRODUCTS BY BRAND
    // ==========================================

    public function getProductsByBrand($brand_id)
    {
        $stmt = $this->conn->prepare(
            'SELECT
                p.*,
                c.cat_name,
                b.brand_name
             FROM products p
             LEFT JOIN categories c
                ON p.product_cat = c.cat_id
             LEFT JOIN brands b
                ON p.product_brand = b.brand_id
             WHERE p.product_brand = ?
             ORDER BY p.product_title ASC'
        );

        $stmt->bind_param('i', $brand_id);
        $stmt->execute();

        $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        return $products ?: [];
    }


    // ==========================================
    // TASK 10: SEARCH PRODUCTS
    // ==========================================

    public function searchProducts($query)
    {
        $search = '%' . $query . '%';

        $stmt = $this->conn->prepare(
            'SELECT
                p.*,
                c.cat_name,
                b.brand_name
             FROM products p
             LEFT JOIN categories c
                ON p.product_cat = c.cat_id
             LEFT JOIN brands b
                ON p.product_brand = b.brand_id
             WHERE p.product_title LIKE ?
                OR p.product_keywords LIKE ?
             ORDER BY p.product_title ASC'
        );

        $stmt->bind_param('ss', $search, $search);
        $stmt->execute();

        $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        return $products ?: [];
    }
}