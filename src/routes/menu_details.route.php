<?php 

$stmt = $pdo->prepare("SELECT c.name AS category_name,p.* FROM delivery_categories c,delivery_products p WHERE p.category_id = c.id AND p.id = ?");
$stmt->execute([$_GET['product_id']]);
$product = $stmt->fetch();

$stmt = $pdo->prepare("SELECT id, name FROM delivery_categories WHERE id = (SELECT parent_id FROM delivery_categories WHERE id = ?)");
$stmt->execute([$product['category_id']]);
$product['parent_category'] = $stmt->fetch();
//var_dump($product);

/*$stmt = $pdo->prepare("SELECT DISTINCT tags.* FROM tags, product_tag WHERE tags.id=product_tag.tag_id AND product_tag.product_id = (SELECT id FROM products WHERE id = ?)");
$stmt->execute([$product['id']]);
$product['id'] = $stmt->fetch();
var_dump($product);*/

/*$stmt = $pdo->prepare("SELECT tags.name AS tag_name,products.* FROM product_tag,tags,products WHERE tags.id = product_tag.tag_id AND product_tag.product_id= ?");
$stmt->execute([$_GET['product_id']]);
$product_tag = $stmt->fetchAll();
var_dump($product_tag);*/

$stmt = $pdo->prepare("SELECT t.* FROM delivery_product_tag pt,delivery_tags t WHERE pt.tag_id = t.id AND pt.product_id = ?");
$stmt->execute([$_GET['product_id']]);
$product_tag = $stmt->fetchAll();///ПОЧЕМУ НЕ СРАБОТАЛ IMPLODE??
//var_dump(array_column($product_tag, 'name','id'));
$arr = array_column($product_tag, 'name','id');
$arr_new = implode(', ', $arr);
//var_dump($arr_new);

//DELETE FROM `product_tag` WHERE tag_id NOT IN (SELECT tags.id FROM tags) 

$related_products = getRelatedProducts($_GET['product_id']);


include ("../src/widgets/recent_post.widget.php");
include ("../templates/menu_details.phtml");
