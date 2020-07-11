<?php 
$stmt = $pdo->prepare("SELECT * FROM delivery_categories WHERE in_home = 1 ORDER by sequence_number");
$stmt->execute();
$in_home = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT p.*, c.sequence_number,c.name as category_name FROM delivery_products p, delivery_categories c WHERE p.favorite = 1 AND p.category_id = c.id ORDER by c.sequence_number");
$stmt->execute();
$favorite_products = $stmt->fetchAll();





include ("../templates/index.phtml");