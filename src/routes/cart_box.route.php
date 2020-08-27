<?php 
header('Content-Type: application/json');
include("../src/connect.php");
//var_dump('123');
$data = $user ? getUserCart($user['id']) : getGuestCart();
echo json_encode([
	"data" => $data
]);