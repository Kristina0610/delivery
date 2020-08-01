<?php 
use Curl\Curl;
$curl = new Curl("https://smsc.ru/sys/send.php");
$curl->get([
	"login" => "Kristi0610",
	"psw" => "parol123",
	"phones" => "+79781024275",
	"mes" => "code",
	"call" => "1",
	"fmt" => "3"
]);
dump($curl->response->code);