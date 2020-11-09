<?php 
include("../src/connect.php");
include("../src/is_admin.php");

$errors = [];
switch (@$_GET['operation']) {
	case 'delete':
		if (!isset($_GET['id'])) {
			$errors[] = "Не передан идентификатор категории";
		} else {
			$stmt = $pdo->prepare("SELECT * FROM delivery_categories WHERE id = ?");
			$stmt->execute([$_GET['id']]);
			$category = $stmt->fetch();
			if (!$category) {
				$errors[] = "Данная категория не найдена в БД";
			} else {
				$stmt = $pdo->prepare("SELECT COUNT(*) FROM delivery_categories WHERE parent_id = ?");
				$stmt->execute([$_GET['id']]);
				$child_count = $stmt->fetchColumn();
				if ($child_count > 0) {
					$errors[] = "Невозможно удалить, так как данный элемент содержит дочерние категории";
				} else {
					$stmt = $pdo->prepare("SELECT COUNT(*) FROM delivery_products WHERE category_id = ?");
					$stmt->execute([$_GET['id']]);
					$product_count = $stmt->fetchColumn();
					if ($product_count > 0) {
						$errors[] = "Невозможно удалить, так как данная категория содержит продукты";
					}
				}	
			}
			if (!$errors) {
				try{
					$stmt = $pdo->prepare("DELETE FROM delivery_categories WHERE id = ?");
					$stmt->execute([$_GET['id']]);
					if ($stmt->rowCount() > 0) {
						header("Location: /?section=admin_categories");
						exit;	
					}
				} catch(Exception $e) {
					dump($e->getMessage());
					$errors[] = "Системная ошибка, попробуйте удалить позже!";
				}	
			}
		}

		break;
	default:
		
		break;
}
//dump($categories);


$stmt = $pdo->prepare("SELECT * FROM delivery_categories");
$stmt->execute();
$categories = $stmt->fetchAll();

$categories = array_combine(array_column($categories, 'id'), $categories);



include ("../templates/admin/admin_categories.phtml");