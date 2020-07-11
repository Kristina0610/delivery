<?php 
if (isset($_POST['auth_submit'])) {
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
			$errors['user'] = "Данный номер телефона не найден в базе";
		} else {
			if (password_verify($_POST['password'], $user['password']) == false) {
				$errors['password'] = "Неверный пароль";
			}
		}
	}
}



