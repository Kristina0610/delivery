<?php 
header('Content-Type: application/json');//задаем тип страницы json, вместо html
use Curl\Curl;
include("../src/connect.php");
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	foreach ($_POST as $key => $value) {
		$_POST[$key] = trim($value);
	}
	if (empty($_POST['code'])) {
		$errors['code'] = "Укажите 6-тизначный код";
	}
	if ($errors) {
		echo json_encode([
			"errors" => $errors
		]);
	} else {
		$stmt = $pdo->prepare("SELECT user_id FROM delivery_phone_verification WHERE code = ? AND status = 0");
		$stmt->execute([$_POST['code']]);
		$user_id = $stmt->fetchColumn();
		if (!$user_id) {
			echo json_encode([
				"errors" => ["Указан неверный код"]
			]);
		} else {
			try {
				$pdo->beginTransaction();

				$stmt = $pdo->prepare("UPDATE delivery_users SET status = 'active' WHERE id = ?");
				$stmt->execute([$user_id]);
				$stmt = $pdo->prepare("UPDATE delivery_phone_verification SET status = '1' WHERE user_id = ?");
				$stmt->execute([$user_id]);

				if (!authorize($user_id)) {
					throw new Exception("Ошибка авторизации", 1);	
				}
				$pdo->commit();
				
				echo json_encode([
					"data" => "ok"
				]);

			} catch (Exception $e) {
				$pdo->rollBack();
				echo json_encode([
					"errors" => ["Системная ошибка, обратитесь к администрации сайта"]
				]);
			}
		}
	}
}