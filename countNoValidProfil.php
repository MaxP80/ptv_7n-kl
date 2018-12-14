<?php
header('Access-Control-Allow-Origin: *');
	include("DataBase.php");
	$request = $bdd->query('SELECT COUNT(valid_profil) as count from connexion WHERE valid_profil=0');
	echo ($request->fetch()['count']);
?>