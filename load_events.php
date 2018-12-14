<?php
	header('Access-Control-Allow-Origin: *');
	session_start();
	$res = NULL;
	include('DataBase.php');
	include("isConnectedMobilApp.php");
	if ($_SESSION['isConnected'] == true) {
		//Dans ce cas, l'utilisateur est bien connecté (a le droit d'être là)
		//Soit il est déjà connecté a un profil
		if ($_SESSION['profil_id'] == 0) {
			//auncun profil de choisi!
			//echo 'profil_id == 0';
			echo json_encode($res);
		}
		else{
			$requestParent = $bdd->prepare('SELECT profil_parent from user WHERE user_id=? and profil_id=?;'); //On est certains que ces infrmations sont définis étant donné qu'on est connecté
			$requestParent->execute(array($_SESSION['user_id'],$_SESSION['profil_id']));
			$isParent = $requestParent->fetch();
			if ($isParent['profil_parent'] == 1) {//Si ce profil est parent, on affiche toutes les informations de ces profils membres
				$requestEvents = $bdd->prepare('SELECT e.id_evenement,e.name, e.date_debut, e.image from evenements as e, user as u, membres_groupe as mg, groupe_description as gd WHERE mg.id_groupe=gd.id_groupe and gd.id_groupe=e.id_groupe and mg.user_id=u.user_id and mg.profil_id=u.profil_id /*jointure des tables*/and u.user_id =?');
				//On ne fait qu'une sélection sur l'user_id étant donné qu'il s'agit du profil parent
				$requestEvents->execute(array($_SESSION['user_id']/*16*/));
				$res = $requestEvents->fetchAll();
				echo json_encode($res);
				//echo "isParent";
			}
			else{			//Si ce profil est un membre, on n'affiche que les informations liès à celui-là
				$requestEvents = $bdd->prepare('SELECT e.id_evenement, e.date_debut, e.image, e.date_fin, e.name from evenements as e, user as u, membres_groupe as mg, groupe_description as gd WHERE mg.id_groupe=gd.id_groupe and gd.id_groupe=e.id_groupe and mg.user_id=u.user_id and mg.profil_id=u.profil_id /*jointure des tables*/and u.user_id =? and u.profil_id=?');
				//On ne fait qu'une sélection sur l'user_id étant donné qu'il s'agit du profil parent
				$requestEvents->execute(array($_SESSION['user_id'], $_SESSION['profil_id']));
				$res = $requestEvents->fetchAll();
				echo json_encode($res);
				//echo "notParent";
			}
		}

	}
	else {
		//Dans ce cas, ;'utilisateur n'est pas connecté ou ne s'est pas connecté avant!
		echo json_encode($res);
		//echo "not connected";
	}
?>