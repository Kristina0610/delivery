<?php 
header('Content-Type: application/json');
include("../src/connect.php");

$warning = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	foreach ($_POST as $key => $value) {
		$_POST[$key] = trim($value);
	}
	if (empty($_POST['phone'])) {
		$warning['phone'] = "Укажите номер телефона";
	} elseif (preg_match('/^\+7[0-9]{10}$/', $_POST['phone']) == false) { //регулярное выражение
		$warning['phone'] = "Укажите номер в формате +79781024275";
	} else {
		$stmt = $pdo->prepare("SELECT COUNT(*) FROM delivery_users WHERE phone LIKE ?");
		$stmt->execute([$_POST['phone']]);
		$result = $stmt->fetchColumn();
		if ($result > 0) {
			$warning['phone'] = "Данный номер телефона уже зарегистрирован";
		}
	}
}
if ($warning) {
	echo json_encode([
		"warning" => $warning
	]);
} else {
	echo json_encode([
		"data" => "ok"
	]);
}