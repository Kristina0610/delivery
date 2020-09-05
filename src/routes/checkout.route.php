<?php 
include("../src/connect.php");
$errors = [];
if (isset($_POST['submit'])) {
	foreach ($_POST as $key => $value) {
		$_POST[$key] = trim($value);
	}
	if (empty($_POST['client_name'])){
		$errors['client_name'] = "Укажите своё имя";
	}
	if (empty($_POST['client_phone'])) {
		$errors['client_phone'] = "Укажите номер телефона";
	} elseif (preg_match('/^\+7[0-9]{10}$/', $_POST['phone']) == false) { //регулярное выражение
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
	if (empty($_POST['build'])) {
		$errors['addr_street'] = "Укажите номер дома";
	}
	if (count($errors) == 0) {
		try {
			$stmt = $pdo->prepare("INSERT INTO delivary_orders(addr_city,addr_street,addr_build,addr_flat,addr_domophone_code,client_phone,client_name) VALUES(?,?,?,?,?,?,?)");
			$stmt->execute([$_POST['addr_city'],$_POST['addr_street'],$_POST['addr_build'],$_POST['addr_flat'],$_POST['addr_domophone_code'],$_POST['client_phone'],$_POST['client_name']]);
		} catch (Exception $e) {
			$errors['system'] = "Системная ошибка, свяжитесь с оператором для формирования заказа";
		}
	}
}

include("../templates/checkout.phtml");