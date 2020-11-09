<?php 
include("../src/connect.php");
include("../src/is_admin.php");
//Подключаем формат вывода в JSON:
header("Content-type: application/json");

$errors = [];
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
				//Выводит на экран массив json в корректой для js форме:
				echo json_encode([
					"status"=>"ok"
				]);
				exit;	
			}
		} catch(Exception $e) {
			$errors[] = "Системная ошибка, попробуйте удалить позже!";
		}	
	}
}
//Выводит на экран массив ошибок json в корректой для js форме:
echo json_encode([
	"status"=>"error",
	"messages"=>$errors
]);