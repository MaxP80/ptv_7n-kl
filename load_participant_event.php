<?php
	header('Access-Control-Allow-Origin: *');
	session_start();
	$res = NULL;
	include('DataBase.php');
	include("isConnectedMobilApp.php");
	if (($_SESSION['isConnected'] == true) && (isset($_GET['id_event']))) {
		//Dans ce cas, l'utilisateur est bien connecté (a le droit d'être là), et l'id de l'événement recherché est bien définit (isset)
		$request = $bdd->prepare('SELECT u.prenom, u.nom, u.photo from user as u, participants as p WHERE p.user_id=u.user_id and p.profil_id=u.profil_id and p.id_evenement_participant=?');
		$request->execute(array($_GET['id_event']));
		$res = $request->fetchAll();
	}
	else {
		//Dans ce cas, l'utilisateur n'est pas connecté ou ne s'est pas connecté avant! Ou l'id de l'événement recherché n'est pas définit!
		//Do nothing
		//echo "not connected";
	}
	echo json_encode($res);
?>