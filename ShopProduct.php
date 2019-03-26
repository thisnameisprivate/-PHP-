<?php

class ShopProduct {
    public static function getInstance ($id, PDO $dbh) {
        $query = "select * from productes whre id = ?";
        $stmt = $dbh->prepare($query);
        if (! $stmt->execute(array($id))) {
            $error = $dbh->errorInfo();
            die("failed: " . $error[1]);
        }
        $row = $stmt->fetch();
        if (empty($row)) return null;
        if ($row['type'] == 'book') {
            // new BookProduct object
        } else if ($row['type'] == 'cd') {
            // new CdProduct object
        } else {
            // new ShopProduct object
        }
        $product->setId($row['id']);
        $product->setDiscount($row['discount']);
        return $product;
    }
}