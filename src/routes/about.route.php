<?php 
$stmt = $pdo->prepare("SELECT * FROM delivery_teams WHERE 1=1");
$stmt->execute();
$teams = $stmt->fetchAll();

//var_dump($teams);


include ("../src/routes/auth.route.php");
include ("../src/routes/reg.route.php");

include ("../templates/about.phtml");


