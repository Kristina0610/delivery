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

$stmt = $pdo->prepare("SELECT * FROM delivery_categories WHERE parent_id IS NULL ORDER BY sequence_number ASC");
$stmt->execute();
$categories = $stmt->fetchAll();

for ($i=0; $i < count($categories); $i++) { 
	$stmt = $pdo->prepare("SELECT * FROM delivery_categories WHERE parent_id = ? ORDER BY sequence_number ASC");
	$stmt->execute([$categories[$i]['id']]);
	while ($child = $stmt->fetch()) {
		$stmt_count = $pdo->prepare("SELECT COUNT(*) FROM delivery_products WHERE category_id = ?");
		$stmt_count->execute([$child['id']]);
		$child['product_count'] = $stmt_count->fetchColumn();
		$categories[$i]['children'][] = $child;
		$child_categories[$categories[$i]['id']][$child['id']] = $child['name'];
	}
}
//dump($child_categories);

if (isset($_POST['send'])) {
	foreach ($_POST as $key => $value) {
		$_POST[$key] = trim($value);
	}
	$errors = [];

	if (empty($_POST['category_name'])) {
		$errors['category_name'] = "Укажите название категории";
	}

	if (!isset($_POST['parent_id'])) {
		$errors['parent_id'] = "Выберите категорию, после которой будет выводиться новая категория"; 
	}


	if (isset($_FILES['image'])) {
		$type = $_FILES['image']['type'];
		/*$size = round($_FILES['image']['size']/1024,1);
		list($width, $height) = getimagesize($_FILES['image']['tmp_name']);
		$proportion = round($width/$height,1);*/
		
		$types = array('image/jpeg', 'image/png');

		if (in_array($type, $types) == false) {
			$errors['image'][] = "Файл изображения должен быть формата jpeg или png";
		}

		if($size > 400) {
			$errors['image'][] = "Файл изображения должен быть не более 400 Кб";
		}

		/*if($proportion > 1.8 || $proportion < 1.2) {
			$errors['image'][] = "Пропорции изображения не соответствуют требованиям";
		}*/

	}

	$in_home = intval(isset($_POST['in_home']));
	$in_menu = intval(isset($_POST['in_menu']));
	$related_excluded = intval(isset($_POST['related_excluded']));
	
	if (!$errors) {
		$category_name=rtrim(mb_strtoupper(mb_substr($_POST['category_name'], 0,1)).mb_substr($_POST['category_name'], 1));

		$parent_id = $_POST['parent_id'] ?? NULL; 
		//$_POST['parent_id'] если равно 0 или false, то присваиваем NULL, в противном случае, присвоится значение этой переменной!!!!!

		if (isset($_POST['children_id'])) {
			if ($_POST['children_id'] != 0) {
				$stmt = $pdo->prepare("SELECT sequence_number FROM delivery_categories WHERE id = ?");
				$stmt->execute([$_POST['children_id']]);
				$sequence_number = $stmt->fetchColumn();
			} else {
				$sequence_number = 0;
			}

			$stmt_max = $pdo->prepare("SELECT MAX(sequence_number) FROM delivery_categories WHERE parent_id = ?");
			$stmt_max->execute([$parent_id]);
			$max_sequence_number = $stmt_max->fetchColumn();

			$stmt = $pdo->prepare("UPDATE delivery_categories SET sequence_number = sequence_number+1 WHERE parent_id = ? AND sequence_number = ?");
			/*for ($i=$max_sequence_number; $i > $sequence_number; $i--) { 
				$stmt->execute([$parent_id,$i]);
			}*/
		} else {
			$stmt = $pdo->query("SELECT MAX(sequence_number) FROM delivery_categories WHERE parent_id IS NULL");
			$sequence_number = $stmt->fetchColumn();
		}
		dump($sequence_number+1);

		/*$stmt = $pdo->prepare("INSERT INTO delivery_categories(name,in_menu,in_home,image,sequence_number,related_excluded,parent_id) VALUES(?,?,?,?,?,?,?)");
		$stmt->execute([$_POST['']])*/
	}
}
dump($_POST);
include ("../templates/admin/admin_categories_create.phtml");