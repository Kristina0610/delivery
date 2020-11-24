<?php 
include("../src/connect.php");
include("../src/is_admin.php");

$stmt = $pdo->query("SELECT delivery_products.*, delivery_categories.name as category_name FROM `delivery_products`, delivery_categories WHERE delivery_products.category_id = delivery_categories.id");
$products = $stmt->fetchAll();
//dump($products);

include ("../templates/admin/admin_products.phtml");