<?php  
include("../src/connect.php");

$errors = [];

if (@$_GET['remove_product_id']) {
	if($user) {
		$stmt = $pdo->prepare("DELETE FROM delivery_cart WHERE user_id = ? AND product_id = ?");
		$stmt->execute([$user['id'],$_GET['remove_product_id']]);
	} else {
		$cart = getCookieCart();
		//var_dump($cart);
		unset($cart[$_GET['remove_product_id']]);
		//var_dump($cart);
		setCookieCart($cart);
		header("Location: /?section=cart");
		exit();
	}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	//var_dump($_POST['cart']);
	foreach ($_POST['cart'] as $quantity) {
		if ($quantity <= 0) {
			$errors['quantity'] = "Количество товара не должно быть меньше 1";
		}
		//var_dump($errors['quantity']);
	}

	if (count($errors) == 0) {
		if($user) {
		$stmt = $pdo->prepare("DELETE FROM delivery_cart WHERE user_id = ?");
		$stmt->execute([$user['id']]);

		$stmt = $pdo->prepare("INSERT INTO delivery_cart(user_id,product_id,quantity) VALUES (?,?,?)");
		foreach ($_POST['cart'] as $product_id => $quantity) {
			$stmt->execute([$user['id'],$product_id,$quantity]);
		}
			
		} else {
			setCookieCart($cart);
			header("Location: /?section=cart");
			exit();
		}
	}	
}

$data = $user ? getUserCart($user['id']) : getGuestCart();    //содержимое корзины
//var_dump($data);




include("../templates/cart.phtml");