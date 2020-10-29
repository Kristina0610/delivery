<?php 
include("../src/connect.php");
	
$faker = Faker\Factory::create('ru_RU');

if (@$_POST['submit']) {
	$stmt = $pdo->prepare("SELECT id,price FROM delivery_products");
	$stmt->execute();
	$product_ids = array_column($stmt->fetchAll(), 'price','id');

	$stmt_users = $pdo->prepare("SELECT id FROM delivery_users");
	$stmt_users->execute();
	$user_ids = array_column($stmt_users->fetchAll(), 'id');
	//dump($user_ids);
	
	//dump($user_id);
	for ($i=0; $i < 200 ; $i++) { 
		$addr_city = $faker->randomElement($array = ['Симферополь','Севастополь']);
		$addr_street = $faker->streetName;
		$addr_build = $faker->buildingNumber;
		$addr_flat = $faker->randomDigitNotNull;
		$addr_domophone_code = $faker->randomDigit;
		$client_phone = $faker->PhoneNumber;
		$client_name = $faker->name;
		$in_time = $faker->time($format = 'H:i:s', $max = 'now');
		$person_count=$faker->numberBetween($min = 1, $max = 30);
		$training_chopsticks = $faker->numberBetween($min = 0, $max = $person_count);
		$created_at = $faker->dateTimeThisMonth($max = 'now', $timezone = null);
		//$created_at = $in_time->sub(DateInterval::createFromDateString('60 minute'));
		$status = $faker->randomElement($array = ['new','processed','ready','sent','completed','rejected']);
		$rejected_reason = NULL;
		$comment = $faker->text($maxNbChars = 70);
		$change_for = NULL;
		$payment_status = $faker->randomElement($array = ['paid','none']);
		$payment_type = $faker->randomElement($array = ['online','card']);
		$user_id = $faker->randomElement([NULL,$faker->randomElement($user_ids)]);

		$stmt = $pdo->prepare("INSERT INTO delivery_orders(
			addr_city,
			addr_street,
			addr_build,
			addr_flat,
			addr_domophone_code,
			client_phone,
			client_name,
			in_time,
			person_count,
			training_chopsticks,
			created_at,
			status,
			rejected_reason,
			comment,
			change_for,
			payment_status,
			payment_type,
			user_id
		) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
		$stmt->execute([$addr_city,$addr_street,$addr_build,$addr_flat,$addr_domophone_code,$client_phone,
			$client_name,$in_time,$person_count,$training_chopsticks,$created_at->format("Y:m:d H:i:s"),$status,$rejected_reason,
			$comment,$change_for,$payment_status,$payment_type,$user_id]);
		$order_id = $pdo->lastInsertId();
		$order_product_ids = $faker->randomElements(array_keys($product_ids), $faker->numberBetween(1,5));
		//dump($order_product_ids);
		foreach ($order_product_ids as $product_id) {
			$quantity = $faker->numberBetween(1,5);
			$price = $product_ids[$product_id];
			//dump($price);
			$stmt = $pdo->prepare("INSERT INTO delivery_order_product(order_id,product_id,quantity,price) VALUES (?,?,?,?)");
			$stmt->execute([$order_id,$product_id,$quantity,$price]);
			
		} 
	}
}

/*if (@$_POST['submit']) {
	for ($i=0; $i < 6 ; $i++) { 
		$test_text = $faker->text(30);
		$id = $faker->unique->randomDigitNotNull;
		$arr[] = ['test_text'=>$test_text,'id'=>$id];
		$stmt = $pdo->prepare("INSERT INTO delivery_test(id,test_text) VALUES(?,?)");
		$stmt->execute([$id,$test_text]);
	}
	foreach ($arr as $key) {
			echo $key['id']."=>".$key['test_text']."<br>";
			$test_id = $key['id'];
			//dump($test_id);
			$product_id = $faker->numberBetween($min = 1, $max = 5);
			$quantity = $faker->numberBetween($min = 1, $max = 5);

			$stmt_price = $pdo->prepare("SELECT price FROM delivery_test_product WHERE id = ?");
			$stmt_price->execute([$product_id]);
			$price = $stmt_price->fetchColumn();
			dump($price);
			$stmt = $pdo->prepare("INSERT INTO delivery_test_product_order(test_id,product_id,quantity,price) VALUES (?,?,?,?)");
			$stmt->execute([$test_id,$product_id,$quantity,$price]);
		}	
	//dump($arr);
}*/



?>
<!DOCTYPE html>
<html>
<head>
	<title>test</title>
</head>
<body>
	<form method="POST">
		<input type="submit" name="submit" value="Добавить">
	</form>	
</body>
</html>
 
