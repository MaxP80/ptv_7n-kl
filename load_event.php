<?php
	session_start();
	$res = NULL;
	include('DataBase.php');
	if (($_SESSION['isConnected'] == true)&& ($_SESSION['profil_id']> 0) && (isset($_GET['id_event']))) {
		//Dans ce cas, l'utilisateur est bien connecté (a le droit d'être là), et l'id de l'événement recherché est bien définit (isset)

		//Il faut tout de même vérifié qu'il a bien le droit de voir cet événement... Il y a alors deux cas, le profil est parent ou alors il est enfant!
		$isAllowed = false; //variable qui dit si l'utilisateur peut voir on non l'événement choisit
		//Profil parent ? 
		if ($bdd->query('SELECT profil_parent from user WHERE user_id="'.$_SESSION["user_id"].'" and profil_id="'.$_SESSION['profil_id'].'"')->fetch()['profil_parent'] == 1) {
			//Il s'agit d'un profil parent
			$requestIsAllowed = $bdd->prepare('SELECT id_groupe from user as u, membres_groupe as mg WHERE u.user_id = mg.user_id and u.profil_id = mg.profil_id and u.user_id=? and mg.id_groupe=(SELECT id_groupe from evenements WHERE id_evenement=?)');
			//avec cette requête, on peut savoir s'il existe un profil de l'utilisateur appartenant u groupe de l'événement qu'il cherche à voir!
			$requestIsAllowed->execute(array($_SESSION['user_id'], $_GET['id_event']));
			$isAllowed = ($requestIsAllowed->rowCount() > 0)? true : false;
			//Il y a soit 0 profil qui ont le droit, soit un ou plusieurs
		}
		else{
			//Le profil est un profil 'enfant'
			$requestIsAllowed = $bdd->prepare('SELECT id_groupe from user as u, membres_groupe as mg WHERE u.user_id = mg.user_id and u.profil_id = mg.profil_id and u.user_id=? and u.profil_id=? and mg.id_groupe=(SELECT id_groupe from evenements WHERE id_evenement=?)');
			//avec cette requête, on peut savoir s'il existe un profil de l'utilisateur appartenant u groupe de l'événement qu'il cherche à voir!
			$requestIsAllowed->execute(array($_SESSION['user_id'],$_SESSION['profil_id'],$_GET['id_event']));
			$isAllowed = ($requestIsAllowed->rowCount() > 0)? true : false;
			//Il y a soit 0 profil qui ont le droit, soit un seul!
		}
		if ($isAllowed == true) {
			//l'utilisateur peut voir ces informations
			//$request = $bdd->prepare('SELECT profil_parent, valid_profil from user WHERE user_id=? and profil_id=?');
			//requête pour sélectionner les informations liées à l'événement
			$request = $bdd->prepare('SELECT date_debut, date_fin, description, heure_debut, heure_fin, lieu, image, name, prenom, nom  from evenements LEFT JOIN user ON user_id=id_user_encadrant and profil_id=id_profil_encadrant WHERE id_evenement=?');
			$request->execute(array($_GET['id_event']));
			$res = $request->fetchAll();
		}
	}
	else {
		//Dans ce cas, l'utilisateur n'est pas connecté ou ne s'est pas connecté avant! Ou l'id de l'événement recherché n'est pas définit!
		//Do nothing
	}
	echo json_encode($res);
?>