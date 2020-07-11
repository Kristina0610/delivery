<?php 
include("../src/connect.php");
include("../src/functions.php");
include("../vendor/autoload.php");

$config = json_decode(file_get_contents("../src/config.json"), true);

if(isset($_GET['section'])) {
	$section = $_GET['section'];
} else {
	$section = "index";
}

$user = auth();
$top_menu = topMenu();


require_once "../src/routes/".$section.".route.php";

