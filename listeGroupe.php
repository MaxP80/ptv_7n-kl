<?php
header('Access-Control-Allow-Origin: *');
	include ("DataBase.php");
	$request = $bdd->query('SELECT * from groupe_description');
	echo json_encode($request->fetchAll());
?>