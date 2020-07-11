<?php 

/*$params = $_GET;
unset($params['page']);//unset - разустанавливает данную переменную.
$build_query = http_build_query($params);
$bind_where = [];

$stmt = $pdo->prepare("SELECT COUNT(*) FROM delivery_articles WHERE 1=1");
foreach ($bind_where as $key => $value) {
	$stmt->bindValue(':'.$key,$value);
}
$stmt->execute();
$article_count = $stmt->fetchColumn();

$page = $_GET['page'] ?? 1;
$article_per_page = 9;

$page_start = ($page - 1)*$article_per_page;

$page_count = ceil($article_count/$article_per_page);

$stmt = $pdo->prepare("SELECT * FROM delivery_articles WHERE 1=1 LIMIT :page_start, :article_per_page");
$stmt->bindValue(':page_start',$page_start,PDO::PARAM_INT);
$stmt->bindValue(':article_per_page',$article_per_page,PDO::PARAM_INT);

foreach ($bind_where as $key => $value) {
	$stmt->bindValue(':'.$key,$value);
}
$stmt->execute();
$articles = $stmt->fetchAll();*/

$params = $_GET;
unset($params['page']);
$build_query = http_build_query($params);
$bind_where = [];

$stmt = $pdo->prepare("SELECT COUNT(*) FROM delivery_articles WHERE 1=1");
foreach ($bind_where as $key => $value) {
	$stmt->bindValue(':'.$key,$value);
}
$stmt->execute();
$article_count = $stmt->fetchColumn();

$page = isset($_GET['page']) && !empty($_GET['page']) ? $_GET['page'] : 1;
$article_per_page = 6;

$page_start = ($page - 1)*$article_per_page;
$page_count = ceil($article_count/$article_per_page);

$stmt = $pdo->prepare("SELECT * FROM delivery_articles WHERE 1=1 LIMIT :page_start, :article_per_page");
$stmt->bindValue(':page_start',$page_start,PDO::PARAM_INT);
$stmt->bindValue(':article_per_page',$article_per_page,PDO::PARAM_INT);
foreach ($bind_where as $key => $value) {
	$stmt->bindValue(':'.$key,$value);
}
$stmt->execute();
$articles = $stmt->fetchAll();

include ("../src/routes/auth.route.php");
include ("../src/routes/reg.route.php");

include("../templates/blog_mesonry.phtml");