<?php 
include("../src/connect.php");
require_once("../vendor/autoload.php");
use Carbon\Carbon;

$errors = [];

$date = new Carbon();
$step = 30;
$minute = $date->minute;

$data = $user ? getUserCart($user['id']) : getGuestCart(); 
//dump($_POST);
/*for ($i=0; $i < 5; $i++) {
	$date->addMinute($step);
	$hour = $date->format("H");
	$minute = $date->minute;
	var_dump($minute);
	if ($minute < 30) {
		$minute == 30;
		echo $hour.":".$minute."<br>";
	} else {
		$minute == '00';
		echo $hour.":".$minute."<br>";
	}
}*/
	//$minutes = ($date->format('i')) - ($date->format('i') % $step);
	//$minutes = $minutes == 0 ? $minutes."0" : $minutes;
	/*if ($minute < 30) {
		$arr_hour = [];
		$arr_minute = [];
		$arr_hour[] = $hour;
		$arr_minute[] = $minutes;
		$new_time= array_combine($arr_hour, $arr_minute);
		var_dump($new_time);*/


		/*$string = $hour.":".$minutes;
		var_dump($string);*/
	/*} else {
		$hour = $date->format("H")+1;
		//echo $hour.":".$minutes."<br>";
	}*/
	

/*$step = 30;
for ($i = 0; $i < 10; $i++) {
	$min = $i+$step."<br>";
	$new_min =$min - $min % $step;
	echo $new_min;
}*/
if ($minute < 30) {
	for ($i=0; $i < 5; $i++) {
		$date->addMinute($step);
		$hour = $date->format("H");
		$minutes = ($date->format('i')) - ($date->format('i') % $step);
		$minutes = $minutes == 0 ? $minutes."0" : $minutes;
		$arr_time[] = $hour.":".$minutes;		
	}
} else {
	for ($i=0; $i < 5; $i++) {
		$arr = [];
		$date->addMinute($step);
		//var_dump($date->format('H:i'));
		$hour = $date->format("H")+1;
		$minutes = ($date->format('i')) - ($date->format('i') % $step);
		$minutes = $minutes == 0 ? $minutes."0" : $minutes;
		//echo $hour.":".$minutes."<br>";
		$arr_time[] = $hour.":".$minutes;
	}	
}

$data = $user ? getUserCart($user['id']) : getGuestCart();

$delivery_cost = 100;
$payment_type = ['online'=>'On-line','card'=>'Картой при получении','cash'=>'Оплата наличными'];
$addr_city = ['Севастополь','Симферополь'];

if (isset($_POST['submit'])) {
	foreach ($_POST as $key => $value) {
		$_POST[$key] = trim($value);
	}
	if (empty($_POST['client_name'])){
		$errors['client_name'] = "Укажите своё имя";
	}
	if (empty($_POST['client_phone'])) {
		$errors['client_phone'] = "Укажите номер телефона";
	} elseif (preg_match('/^\+7[0-9]{10}$/', $_POST['client_phone']) == false) { //регулярное выражение
		$errors['client_phone'] = "Укажите номер в формате +79781024275";
	}
	if (!@$_POST['addr_city']) {
		$errors['addr_city'] = "Выберите город доставки";
	} elseif (!in_array($_POST['addr_city'], ['Симферополь','Севастополь'])) {
		$errors['addr_city'] = "Данного города нет в списке";
	}
	if (empty($_POST['addr_street'])) {
		$errors['addr_street'] = "Укажите улицу";
	}
	if (empty($_POST['addr_build'])) {
		$errors['addr_build'] = "Укажите номер дома";
	}
	if (empty($_POST['person_count'])) {
		$errors['person_count'] = "Укажите количество персон";
	} elseif (($_POST['person_count'] > 30) || ($_POST['person_count'] < 1)) {
		$errors['person_count'] = "Количество персон не более 30-ти человек и не менее 1-го";
	}
	if ($_POST['training_chopsticks'] > $_POST['person_count']) {
		$errors['training_chopsticks'] = "Количество учебных палочек не должно превышать количество персон";
	} elseif ($_POST['training_chopsticks'] > 30 || $_POST['training_chopsticks'] < 0) {
		$errors['training_chopsticks'] = "Количество учебных палочек не может быть отрицательным или более 30-ти штук";
	}
	if (!isset($_POST['payment_type'])) {
		$errors['payment_type'] = "Выберите способ оплаты заказа";
	} elseif ($_POST['payment_type'] == "cash" && strval($_POST['change_for']) !== strval(intval($_POST['change_for']))) {
		$errors['change_for'] = "Укажите целое число";
	}
	
	//var_dump($errors);
	//var_dump(gettype($_POST['change_for']));
	if (count($errors) == 0) {
		$_POST['addr_domophone_code'] = !empty($_POST['addr_domophone_code']) ? $_POST['addr_domophone_code'] : null;//САНИТИЗАЦИЯ
		$_POST['training_chopsticks'] = !empty($_POST['training_chopsticks']) ? $_POST['training_chopsticks'] : null;
		$_POST['change_for'] = !empty($_POST['change_for']) ? $_POST['change_for'] : null;
		$pdo->beginTransaction();
		try {
			$stmt = $pdo->prepare("INSERT INTO delivery_orders(addr_city,addr_street,addr_build,addr_flat,addr_domophone_code,client_phone,client_name,in_time,person_count,training_chopsticks,created_at,comment,change_for,payment_type) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
			$stmt->execute([$_POST['addr_city'],
				$_POST['addr_street'],
				$_POST['addr_build'],
				$_POST['addr_flat'],
				$_POST['addr_domophone_code'],
				$_POST['client_phone'],
				$_POST['client_name'],
				$_POST['in_time'],
				$_POST['person_count'],
				$_POST['training_chopsticks'],
				Carbon::now()->toDateTimeString(),
				$_POST['comment'],
				$_POST['change_for'],
				$_POST['payment_type']]);

			$order_id = $pdo->lastInsertId();
			//var_dump($last_id);
			$count = count($data['items']);
			

			foreach ($data['items'] as $product_cart) {
				$stmt = $pdo->prepare("INSERT INTO delivery_order_product (order_id,product_id,quantity,price) VALUES (?,?,?,?)");
				$stmt->execute([$order_id,$product_cart['product_id'],$product_cart['quantity'],$product_cart['price']]);
			}	
			$pdo->commit();
			$user ? clearDbCart($user['id']) : clearCookieCart(); // Удаление данных из БД либо очистка куки
			header("Location: index.php?section=order_complete&order_id=".$order_id);
			exit;
			
			$errors['system'] = "Системная ошибка, свяжитесь с оператором для формирования заказа";
		}
	}
}

include("../templates/checkout.phtml");