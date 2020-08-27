<?php 
header('Content-Type: application/json');
include("../src/connect.php");

if ($user) {
	$stmt = $pdo->prepare("SELECT * FROM delivery_cart WHERE user_id = ? AND product_id = ?");
	$stmt->execute([$user['id'],$_POST['product_id']]);
	$cart_product = $stmt->fetch();
	try {
		if ($cart_product) {
			$stmt = $pdo->prepare("UPDATE delivery_cart SET quantity = quantity + ?"); // прибавление количества товара к уже существуещему в корзине
			$stmt->execute([$_POST['quantity']]);
		} else {
			$stmt = $pdo->prepare("INSERT INTO delivery_cart(user_id,product_id,quantity) VALUES (?,?,?)");
			$stmt->execute([$user['id'],$_POST['product_id'],$_POST['quantity']]);
		}
	} catch (Exception $e) {
		echo json_encode([
			"errors" => ["ПРОИЗОШЛА СИСТЕМНАЯ ОШИБКА! Обратитесь к администратору сайта"]
		]);
	}
	echo json_encode([
		"data" => "ok"
	]);	
} else {
	$cart = getCookieCart();
	
	@$cart[$_POST['product_id']] += $_POST['quantity'];
	
	setCookieCart($cart);

	echo json_encode([
		"data" => "ok"
	]);
}

