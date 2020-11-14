<?php 
include("../src/connect.php");
include("../src/is_admin.php");

/*$stmt = $pdo->prepare("SELECT * FROM delivery_categories");
$stmt->execute();
$categories = $stmt->fetchAll();


$child = [];
foreach ($categories as $category) {
	if (!$category['parent_id']) {
		$stmt_child = $pdo->prepare("SELECT * FROM delivery_categories WHERE parent_id = ?");
		$stmt_child->execute([$category['id']]);
		$child[] = $stmt_child->fetchAll();
	}
}
dump($child);
foreach ($child as $key => $value) {
	echo($value);
}*/

$stmt = $pdo->prepare("SELECT * FROM delivery_categories WHERE parent_id IS NULL");
$stmt->execute();
$categories = $stmt->fetchAll();

for ($i=0; $i < count($categories); $i++) { 
	$stmt = $pdo->prepare("SELECT * FROM delivery_categories WHERE parent_id = ?");
	$stmt->execute([$categories[$i]['id']]);
	while ($child = $stmt->fetch()) {
		$stmt_count = $pdo->prepare("SELECT COUNT(*) FROM delivery_products WHERE category_id = ?");
		$stmt_count->execute([$child['id']]);
		$child['product_count'] = $stmt_count->fetchColumn();
		$categories[$i]['children'][] = $child;
	}
}

if (isset($_POST['send'])) {
	foreach ($_POST as $key => $value) {
		$_POST[$key] = trim($value);
	}
	$errors = [];
	if (empty($_POST['category_name'])) {
		$errors['category_name'] = "Укажите название категории";
	} else {
		$category_name=rtrim(mb_strtoupper(mb_substr($_POST['category_name'], 0,1)).mb_substr($_POST['category_name'], 1));
		//dump($category_name);
	}
	if (empty($_POST['category_id'])) {
		$errors['category_id'] = "Выберите категорию, после которой будет выводиться новая категория"; 
	}
	dump($errors);
	dump($_POST['category_id']);
}

include ("../templates/admin/admin_categories_create.phtml");