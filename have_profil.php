<?php 
	header('Access-Control-Allow-Origin: *');
	session_start();
	include("DataBase.php");
	include("isConnectedMobilApp.php");
	//On sait à ce moment, que l'utilisateur est bien connecté§
	if ($_SESSION['profil_id'] != 0) {
		echo "Success";
	}
	else
		echo "Failed";
?>