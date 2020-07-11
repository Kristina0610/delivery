<?php 
if (isset($_POST['reg_submit'])) {
	foreach ($_POST as $key => $value) {
		$_POST[$key] = trim($value);
	}

	$errors = [];

	if (empty($_POST['firstname'])) {
		$errors['firstname'] = "Укажите имя";
	}
	if (empty($_POST['phone'])) {
		$errors['phone'] = "Укажите номер телефона";
	}
	if (empty($_POST['password'])) {
		$errors['password'] = "Укажите пароль";
	} elseif (mb_strlen($_POST['password']) < 6) {
		$errors['password'] = "Пароль не должен быть менее 6-ти символов";
	}
	if (empty($_POST['re-password'])) {
		$errors['re-password'] = "Повторите пароль";
	} elseif ($_POST['re-password'] !== $_POST['password']) {
		$errors['re-password'] = "Повтор пароля не совпадает с исходным";
	}
}
if (count($errors) == 0) {
	try {
		$stmt = $pdo->prepare("INSERT INTO delivery_users(firstname,phone,password) VALUES (?,?,?)");
		$stmt->execute([$_POST['firstname'],$_POST['phone'],password_hash($_POST['password'], PASSWORD_DEFAULT)]);
	} catch (Exception $e) {
		$errors['dublicate'] = "Вы являетесь зарегистрированным пользователем, выполните ВХОД";
	}
}


//header("Location: /?section=test&success=1");