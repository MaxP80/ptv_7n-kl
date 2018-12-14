<?php
header('Access-Control-Allow-Origin: *');
	session_start();
	include ("DataBase.php");
	include("isConnectedMobilApp.php");
	$res = null;
	if (isset($_SESSION['user_id']) && isset($_SESSION['profil_id'])){
		$statusRequest = $bdd->query('SELECT is_admin, in_comite from user WHERE user_id="'.$_SESSION['user_id'].'" and profil_id="'.$_SESSION['profil_id'].'"');
		$status = $statusRequest->fetch();
		//On enregistre les données du status!
		//On récupère l'ensemble des groupes où l'utilisateur est définit en tant que coach
		$trameRequest = ('SELECT * from groupe_description WHERE id_groupe IN (SELECT id_groupe from coachs_groupe WHERE coach_user_id="'.$_SESSION['user_id'].'" and coach_profil_id="'.$_SESSION['profil_id'].'")');
		if (($status['is_admin'] == 'true') || ($status['in_comite'])){
			//Dans ce cas, l'utilisateur peut envoyer un message à tous les groupes
			$trameRequest = ('SELECT * from groupe_description');
		}
		$request = $bdd->query($trameRequest);
		$res = ($request->fetchAll());
	}
	//echo 'fin';
	echo json_encode($res);
?>