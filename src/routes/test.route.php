<?php 
//var_dump(getRelatedProducts(45));
//var_dump(getParentId(12));

//var_dump(getChildCategories(3));

//var_dump(getRelatedProducts(22));

/*use Curl\Curl;
$curl = new Curl("https://smsc.ru/sys/send.php");
$curl->get([
	"login" => "Kristi0610",
	"psw" => "parol123",
	"phones" => "+79781024275",
	"mes" => "code",
	"call" => "1",
	"fmt" => "3"
]);

dump($curl->response);*/
if (isset($_POST['reg_submit'])) {
	foreach ($_POST as $key => $value) {
		$_POST[$key] = trim($value);
	}

	$errors = [];

	if (empty($_POST['firstname'])) {
		$errors['firstname'] = "Укажите имя пользователя";
	}
	if (empty($_POST['phone'])) {
		$errors['phone'] = "Укажите номер телефона";
	}
	if (empty($_POST['password'])) {
		$errors['password'] = "Укажите пароль";
	} elseif (mb_strlen($_POST['password']) < 6) {
		$errors['password'] = "Пароль не должен быть меньше 6-ти симолов";
	}
	if (empty($_POST['re-password'])) {
		$errors['re-password'] = "Повторите пароль";
	} elseif ($_POST['re-password'] !== $_POST['password']) {
		$errors['re-password'] = "Пароль не совпадает с исходным паролем";
	}
}
var_dump($errors);
?>

<!--<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<button id=b1>Send</button>
	<script src="js/vendor/jquery-3.2.1.min.js"></script>
<script type="text/javascript">
	/*b1=document.getElementById('b1');
	b1.addEventListener("click", function(e) {
		alert(123);
	});*/
	$("#b1").click(function(e){
		alert($("#b1").html("asfgh"));
	})

	
</script>
</body>
</html>-->

