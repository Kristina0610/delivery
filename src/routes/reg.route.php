<?php 

//echo $_POST['phone'];
header('Content-Type: application/json');//задаем тип страницы json, вместо html
use Curl\Curl;
include("../src/connect.php");
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	foreach ($_POST as $key => $value) {
		$_POST[$key] = trim($value);
	}
	if (empty($_POST['firstname'])) {
		$errors['firstname'] = "Укажите имя";
	}
	if (empty($_POST['phone'])) {
		$errors['phone'] = "Укажите номер телефона";
	} elseif (preg_match('/^\+7[0-9]{10}$/', $_POST['phone']) == false) { //регулярное выражение
		$errors['phone'] = "Укажите номер в формате +79781024275";
		/*
		^ - начало рег.выражения
		\+7|7|8 - с чего должно начинаться
		+ - должно встретиться ОДИН РАЗ
		? - встречается НОЛЬ или ОДИН РАЗ
		[0-9] - какие цифры могут присутствовать
		{10} - обязательное количество раз - в данном случае - 10
		$ - окончание рег.выражения
		*/
	}
	if (empty($_POST['password'])) {
		$errors['password'] = "Укажите пароль";
	} elseif (mb_strlen($_POST['password']) < 6) {
		$errors['password'] = "Пароль не должен быть менее 6-ти символов";
	}
	if (empty($_POST['re_password'])) {
		$errors['re_password'] = "Повторите пароль";
	} elseif ($_POST['re_password'] !== $_POST['password']) {
		$errors['re_password'] = "Повтор пароля не совпадает с исходным";
	}
	if ($errors) {
		echo json_encode([
			"errors" => $errors
		]);
	} else {
		$curl = new Curl("https://smsc.ru/sys/send.php");
		$curl->get([
			"login" => "Kristi0610",
			"psw" => "parol123",
			"phones" => $_POST['phone'],
			"mes" => "code",
			"call" => "1",
			"fmt" => "3"
		]);
		if (!isset($curl->response->code)) {
			echo json_encode([
				"errors" => ['Сбой отправки кода, обратитесь к администрации сайта']
			]);
			exit;
		}
		try {
			$stmt = $pdo->prepare("INSERT INTO delivery_users(firstname,lastname,phone,password) VALUES (?,?,?,?)");
			$stmt->execute([$_POST['firstname'],$_POST['lastname'],$_POST['phone'],password_hash($_POST['password'], PASSWORD_DEFAULT)]);
			$stmt_code = $pdo->prepare("INSERT INTO delivery_phone_verification(code,user_id,created_at) VALUES (?,?,NOW())");
			$stmt_code->execute([$curl->response->code,$pdo->lastInsertId()]);
			echo json_encode([
				"data" => "ok"
			]);
		} catch (Exception $e) {
			$errors['dublicate'] = "Вы являетесь зарегистрированным пользователем, выполните ВХОД";
			echo json_encode([
				"errors" => $e->getMessage()
			]);
		}
	}
}


