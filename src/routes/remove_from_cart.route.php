<?php 
header('Content-Type: application/json');
include("../src/connect.php");


if ($user) {
	try {
		$stmt = $pdo->prepare("DELETE FROM delivery_cart WHERE user_id = ? AND product_id = ?");
		$stmt->execute([$user['id'],$_POST['product_id']]);
	} catch (Exception $e) {
		echo json_encode([
			"errors" => ["ПРОИЗОШЛА СИСТЕМНАЯ ОШИБКА! Обратитесь к администратору сайта",$e->getMessage()]
		]);
		exit;
	}
	echo json_encode([
		"data" => "ok"
	]);	
} else {
	$cart = getCookieCart();
	
	unset($cart[$_POST['product_id']]);

	if (count($cart) == 0 ) {
		clearCookieCart();
	} else {
		setCookieCart($cart);
	}
	echo json_encode([
		"data" => "ok"
	]);
}