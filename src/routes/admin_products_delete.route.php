<?php 
include("../src/connect.php");
include("../src/is_admin.php");

header("Content-type: application/json");

$errors = [];
if (!isset($_GET['id'])) {
	$errors[] = "Не передан идентификатор продукта";
} else {
	$stmt = $pdo->prepare("SELECT * FROM delivery_products WHERE id = ?");
	$stmt->execute([$_GET['id']]);
	$product = $stmt->fetch();
	if (!$product) {
		$errors[] = "Данный продукт не найден в БД";
	} else {
		$stmt = $pdo->prepare("SELECT COUNT(*) FROM delivery_order_product WHERE product_id = ?");
		$stmt->execute([$_GET['id']]);
		if ($stmt->fetchColumn() > 0) {
			$errors[] = "Данный продукт есть в истории заказов";
		}
	}
	if (!$errors) {
		try {
			$stmt = $pdo->prepare("DELETE FROM delivery_products WHERE id = ?");
			$stmt->execute([$_GET['id']]);
			if ($stmt->rowCount() > 0) {
				echo json_encode([
					"status"=>"ok"
				]);
				exit;
			}
		} catch(Exception $e) {
			$errors[] = "Системная ошибка, попробуйте удалить позже";
		}
	}
}
echo json_encode([
	"status"=>"error",
	"messages"=>$errors
]);
