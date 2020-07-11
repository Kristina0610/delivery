<?php
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

