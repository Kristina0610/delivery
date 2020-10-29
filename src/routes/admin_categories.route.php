<?php 
include("../src/connect.php");
include("../src/is_admin.php");

$stmt = $pdo->prepare("SELECT * FROM delivery_categories");
$stmt->execute();
$categories = $stmt->fetchAll();

$categories = array_combine(array_column($categories, 'id'), $categories);
//dump($categories);
include ("../templates/admin/admin_categories.phtml");