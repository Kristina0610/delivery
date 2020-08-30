<?php 
//БЛОК ФОРМИРОВАНИЯ МЕНЮ начало
$stmt = $pdo->prepare("SELECT * FROM delivery_categories WHERE id = ?");  
$stmt->execute([$_GET['category_id'] ?? NULL]);
$current_category = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM delivery_categories WHERE parent_id IS NULL");
$stmt->execute();
$categories = $stmt->fetchAll();

//БЛОК ФОРМИРОВАНИЯ МЕНЮ конец

for ($i=0; $i < count($categories); $i++) { 
	$stmt = $pdo->prepare("SELECT * FROM delivery_categories WHERE parent_id = ?");
	$stmt->execute([$categories[$i]['id']]);
	while ($child = $stmt->fetch()) {
		$stmt_count = $pdo->prepare("SELECT COUNT(*) FROM delivery_products WHERE category_id = ?");
		$stmt_count->execute([$child['id']]);
		$child['product_count'] = $stmt_count->fetchColumn();
		$categories[$i]['children'][] = $child;
	}
}
//var_dump($categories);
$params = $_GET;
unset($params['page']);
$build_query =http_build_query($params);
$inner = "";
$bind_inner = [];
$where = "";
$bind_where = [];
$order_by = " ORDER BY sequence_number";

if (isset($_GET['category_id'])) {
	if (!is_null($current_category['parent_id'])) {
		$where .= " AND category_id = :category_id";// :category_id - именнованный placeholder
		$bind_where['category_id'] = $_GET['category_id'];
	} else {
		$where .= " AND category_id IN (SELECT id FROM delivery_categories WHERE parent_id = :parent_id OR id = :self_category_id)";
		$bind_where['parent_id'] = $_GET['category_id'];
		$bind_where['self_category_id'] = $_GET['category_id'];
	}
}

if (isset($_GET['tag_id'])) {
	$inner .= " INNER JOIN delivery_product_tag ON delivery_product_tag.product_id=delivery_products.id AND delivery_product_tag.tag_id = :tag_id";
	$bind_inner['tag_id'] = $_GET['tag_id'];
}

/*if (isset($_GET['price'])) {
	$where .= " AND price < :price";
	$bind_array['price'] = $_GET['price'];
}*/
$stmt = $pdo->prepare("SELECT COUNT(*) FROM delivery_products ".$inner." WHERE 1 = 1".$where);//пагинация + config.json
foreach ($bind_where as $key => $value) {
	$stmt->bindValue(':'.$key,$value);
}
foreach ($bind_inner as $key => $value) {
	$stmt->bindValue(':'.$key,$value);
}	
$stmt->execute();
$product_count = $stmt->fetchColumn();

$page = isset($_GET['page']) && !empty($_GET['page']) ? $_GET['page'] : 1; //короткая нотация if вариант 2
$product_per_page = $config['product_per_page'];
$page_start = ($page-1)*$product_per_page;

$page_count = ceil($product_count/$product_per_page);
//var_dump($page_count);

/*$page = isset($_GET['page']) ? intval($_GET['page']) : 1; //короткая нотация if вариант 1 : если существует $_GET['page'], то он = $_GET['page'], если нет - то = 1*/

//var_dump($page);

$stmt = $pdo->prepare("SELECT * FROM delivery_products ".$inner." WHERE 1 = 1".$where.$order_by.
	" LIMIT :page_start,:product_per_page");
$stmt->bindValue(':page_start', $page_start, PDO::PARAM_INT);
$stmt->bindValue(':product_per_page', $product_per_page, PDO::PARAM_INT);
foreach ($bind_where as $key => $value) {
	$stmt->bindValue(':'.$key,$value);
}
foreach ($bind_inner as $key => $value) {
	$stmt->bindValue(':'.$key,$value);
}

$stmt->execute();

$products = $stmt->fetchAll();

//var_dump($products);

/*$stmt = $pdo->prepare("SELECT * FROM tags WHERE 1= 1");
$stmt->execute();
$tags = $stmt->fetchAll();*/

//var_dump($tags);

$stmt = $pdo->prepare("SELECT DISTINCT delivery_tags.* FROM delivery_tags, delivery_product_tag WHERE delivery_tags.id = delivery_product_tag.tag_id");
$stmt->execute();
$tags = $stmt->fetchAll();


include ("../src/widgets/recent_post.widget.php");
include ("../templates/catalog.phtml");