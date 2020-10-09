<?php 
include("../src/connect.php");
use Carbon\Carbon;

$stmt = $pdo->prepare("SELECT * FROM delivery_articles WHERE id = ?");
$stmt->execute([$_GET['article_id']]);
$article = $stmt->fetch();

$carbon =  Carbon::createFromDate($article['created_at']);

$carbon->toDateTimeString(); 
$created_at = $carbon->format('d-m-Y');


$stmt = $pdo->prepare("SELECT * FROM delivery_articles WHERE id != ? ORDER BY RAND() LIMIT 0,3");
$stmt->execute([$_GET['article_id']]);
$related_articles= $stmt->fetchAll();

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

include ("../templates/blog_details.phtml");