<?php 
include("../src/connect.php");
include ("../src/is_admin.php");

$errors = [];

switch (@$_GET['operation']) {
	case 'delete':
		if (!isset($_GET['id'])) {
			$errors[] = "Не перед идентификатор продукта";
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
					$errors[] = "Данный продукт найден в истории заказов";
				}
			}
			if (!$errors) {
				try {
					$stmt = $pdo->prepare("DELETE FROM delivery_products WHERE id = ?");
					$stmt->execute([$_GET['id']]);
					if ($stmt->rowCount() > 0) {
						header("Location: /?section=admin_products");
						exit;
					}
				} catch (Exception $e) {
					$errors[] = "Системная ошибка, попробуйте удалить позже";
				}
			}
			break; 
		}	
}

$stmt = $pdo->query("SELECT delivery_products.*, delivery_categories.name as category_name FROM delivery_categories, delivery_products WHERE delivery_products.category_id=delivery_categories.id");
$products = $stmt->fetchAll();

include("../templates/admin/admin_products.phtml");