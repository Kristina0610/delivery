<?php 
header('Content-Type: application/json');
include("../src/connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	foreach ($_POST as $key => $value) {
		$_POST[$key] = trim($value);
	}
	$errors = [];
	if (empty($_POST['phone'])) {
		$errors['phone'] = "Укажите телефон";
	}
	if (empty($_POST['password'])) {
		$errors['password'] = "Укажите пароль";
	}
	if (count($errors) == 0) {
		$stmt=$pdo->prepare("SELECT * FROM delivery_users WHERE phone = ?");
		$stmt->execute([$_POST['phone']]);
		$user = $stmt->fetch();
		if ($user == false) {
			$errors['phone'] = "Данный номер телефона не найден в базе";
		} else {
			if (password_verify($_POST['password'], $user['password']) == false) {
				$errors['password'] = "Неверный пароль";
			}
		}
		if (count($errors) == 0) {
			if (!authorize($user['id'])) {
				echo json_encode([
					"errors"=>['Ошибка авторизации, свяжитесь с администрацией']
				]);
			} else {
				echo json_encode([
					"data" =>'ok'
				]);
			}
		} else {
			echo json_encode([
				"errors" => $errors
			]);
		}
	} else {
		echo json_encode([
			"errors" => $errors
		]);
	}
}



