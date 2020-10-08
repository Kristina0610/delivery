<?php
function authorize($user_id) {
	global $pdo;
	$label = uniqid();
	try{
		$stmt = $pdo->prepare("INSERT INTO delivery_user_session VALUES(null,?,NOW(),NOW()+INTERVAL 7 DAY,?)");
		$stmt->execute([$label,$user_id]);
	} catch (Exception $e) {
		return false;
	}
	setcookie('label',$label,time()+(7*24*60*60),'/');
	return true;
}

function auth() {
	global $pdo;
	$user = null;
	if(isset($_COOKIE['label'])) {
		$stmt = $pdo->prepare("SELECT * FROM delivery_user_session WHERE label = ?");
		$stmt->execute([$_COOKIE['label']]);

		$user_session = $stmt->fetch();
		// удаление куки в учебных целях
		if($user_session['expired_at'] < (new \DateTime())->format('Y-m-d H:i:s')) {
			logout();
		} else {
			$stmt = $pdo->prepare("SELECT * FROM delivery_users WHERE id = ?");
			$stmt->execute([$user_session['user_id']]);

			$user = $stmt->fetch();
		}
	}

	return $user;
}

function logout() {
	global $pdo;

	$stmt = $pdo->prepare("DELETE FROM delivery_user_session WHERE label = ?");
	$stmt->execute([$_COOKIE['label']]);

	setcookie("label", null, -1, "/");
	header("Location: index.php?section=index");
}

function topMenu() {
	global $pdo;

	$stmt = $pdo->prepare("SELECT * FROM delivery_categories WHERE in_menu = 1");
    $stmt->execute();
    
    return $stmt->fetchAll();

}

function getRelatedProducts($product_id) {
	global $pdo;

	$stmt = $pdo->prepare("SELECT * FROM delivery_products WHERE id = ?");
	$stmt->execute([$product_id]);

	$product = $stmt->fetch();
	$parent_id = getParentId($product['category_id']);

	$stmt = $pdo->prepare("SELECT related_excluded FROM delivery_categories WHERE id = ?");
	$stmt->execute([$parent_id]);
	$related_excluded = $stmt->fetchColumn();

	if ($related_excluded == 1) {
		$stmt = $pdo->query("SELECT id FROM delivery_categories WHERE parent_id IS NULL AND related_excluded = 0
			 ORDER BY RAND() LIMIT 0,1");
		$parent_id = $stmt->fetchColumn();
	}

	$children_id = getChildCategories($parent_id);

	$question_marks = substr(str_repeat('?,', count($children_id)),0,-1);
	array_push($children_id, $product_id);

	$stmt = $pdo->prepare("SELECT * FROM delivery_products WHERE category_id IN ({$question_marks}) AND id != ? ORDER BY RAND() LIMIT 0,1");
	$stmt->execute($children_id);
	$related_products[] = $stmt->fetch();

	$stmt = $pdo->query("SELECT id FROM delivery_categories WHERE related_excluded = 1");
	while ($category_id = $stmt->fetchColumn()) {
		$stmt_product = $pdo->prepare("SELECT * FROM delivery_products WHERE category_id = ? ORDER BY RAND() LIMIT 0,1");
		$stmt_product->execute([$category_id]);
		$related_products[] = $stmt_product->fetch();
	}
	return $related_products;
}

function getParentId($category_id) {
	global $pdo;

	$stmt = $pdo->prepare("SELECT parent_id FROM delivery_categories WHERE id = ?");
	$stmt->execute([$category_id]);

	$parent_id = $stmt->fetchColumn();

	if (is_null($parent_id)) {
		return $category_id;
	} else {
		return $parent_id;
	}
}

function getChildCategories($parent_id) {
	global $pdo;

	$stmt = $pdo->prepare("SELECT id FROM delivery_categories WHERE parent_id = ?");
	$stmt->execute([$parent_id]);

	$children_id = array_column($stmt->fetchAll(), 'id');
	if (empty($children_id)) {
		$children_id[] = $parent_id;
	}

	return $children_id;
}

function getUserCart($user_id) {
	global $pdo;
	$stmt = $pdo->prepare("SELECT p.name, p.photo_small, p.price, c.product_id, c.quantity FROM delivery_products p, delivery_cart c WHERE p.id = c.product_id AND c.user_id = ?");
	$stmt->execute([$user_id]);
	//var_dump($stmt->fetchAll());
	$total_price = 0;
	while ($item = $stmt->fetch()) {
		$cart[] = $item;
		$total_price += $item['price']*$item['quantity'];
	}
	//var_dump($total_price);
	return [
		"items"=>$cart,
		"total_price"=>$total_price
	];
}

function getGuestCart() {
	global $pdo;
	$cart = [];
	$total_price = 0;
	if (isset($_COOKIE['cart'])) {
		$to_cart = json_decode($_COOKIE['cart'],true);
		$product_ids = array_keys($to_cart);

		$questions = mb_substr(str_repeat('?,', count($product_ids)),0,-1);
		$stmt = $pdo->prepare("SELECT p.id product_id, p.name, p.photo_small, p.price FROM delivery_products p WHERE p.id IN (".$questions.")");
		$stmt->execute($product_ids);
		
		while ($item = $stmt->fetch()) {
			$item['quantity'] = $to_cart[$item['product_id']];
			$cart[] = $item;
			$total_price += $item['price']*$item['quantity'];
		}	
	} 
	return [
		"items"=>$cart,
		"total_price"=>$total_price
	];
}

function getCookieCart() {
	return isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'],true) : [];
}

function setCookieCart($cart) {
	return setcookie('cart',json_encode($cart),time()+(7*24*60*60),'/');
}

function clearCookieCart() {
	return setcookie('cart','',-1,'/');
}
function clearDbCart($user_id) {
	global $pdo;
	$stmt = $pdo->prepare("DELETE FROM delivery_cart WHERE user_id = ?");
	$stmt->execute([$user_id]);

	return (boolean) $stmt->rowCount();	//данная конструкция возвращает true/false
}
