<?php 
include("../src/connect.php");
include("../src/is_admin.php");

/*SELECT SUM(op.quantity*op.price) as sum FROM delivery_orders o, delivery_order_product op WHERE DATE(created_at) = DATE(NOW()) AND o.id = op.order_id GROUP BY o.id*/
/*

SELECT * FROM delivery_orders WHERE DATE(created_at) > DATE_SUB(NOW(),INTERVAL 14 day)*/

/*SELECT SUM(t1.summa) as total_summa, COUNT(t1.created_at) as count_orders, t1.created_at FROM (SELECT SUM(op.quantity*op.price) as summa, DATE(o.created_at) as created_at FROM delivery_orders o, delivery_order_product op WHERE DATE(created_at) > DATE_SUB(NOW(),INTERVAL 14 day) AND o.id = op.order_id GROUP BY o.id) t1 GROUP BY t1.created_at*/
 //ЗАКАЗЫ, ИХ КОЛИЧЕСТВО И СУММА ЗА ПОСЛЕДНИЕ 14 ДНЕЙ

/*SELECT COUNT(*),IF(user_id IS NOT NULL, "user","guest") as type FROM `delivery_orders` GROUP BY type*/


$stmt_today = $pdo->prepare("SELECT SUM(op.quantity*op.price) as sum FROM delivery_orders o, delivery_order_product op WHERE DATE(created_at) = DATE(NOW()) AND o.id = op.order_id GROUP BY o.id");
$stmt_today->execute();
$today = array_column($stmt_today->fetchAll(),'sum');

$summa_orders_today = count($today); 
$summa_total_today  = array_sum($today);

$stmt_week = $pdo->prepare("SELECT SUM(t1.summa) as total_summa, COUNT(t1.created_at) as count_orders, t1.created_at FROM (SELECT SUM(op.quantity*op.price) as summa, DATE(o.created_at) as created_at FROM delivery_orders o, delivery_order_product op WHERE DATE(created_at) > DATE_SUB(NOW(),INTERVAL 7 day) AND o.id = op.order_id GROUP BY o.id) t1 GROUP BY t1.created_at");
$stmt_week->execute();
$week = $stmt_week->fetchAll();

foreach ($week as $day) {
	$arr_week_total_sum[] = $day['total_summa'];
	$arr_week_total_count_orders[] = $day['count_orders'];
}

$summa_orders_week = array_sum($arr_week_total_count_orders);
$summa_total_week = array_sum($arr_week_total_sum);

$stmt_users = $pdo->prepare("SELECT COUNT(*) as count,IF(user_id IS NOT NULL, 'user','guest') as type FROM `delivery_orders` GROUP BY type");
$stmt_users->execute();
$users = $stmt_users->fetchAll();
//dump($users);
foreach ($users as $user) {
	if($user['type'] == 'guest') {
		$count_guest = $user['count']; 
	} elseif ($user['type'] == 'user') {
		$count_user = $user['count'];
	}
}
$all_users = $count_user+$count_guest;
$persent_guest = round($count_guest*100/$all_users);
$persent_users = 100 - $persent_guest;

$stmt_count_orders_status= $pdo->prepare("SELECT COUNT(*) FROM delivery_orders WHERE status = 'new'");
$stmt_count_orders_status->execute();
$count_orders_status = $stmt_count_orders_status->fetchColumn();



include ("../templates/admin/index.phtml");
