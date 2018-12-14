<?php 
header('Access-Control-Allow-Origin: *');
	session_start();
	include("DataBase.php");
	include("isConnectedMobilApp.php");
	//Il n'y a qu'un seul profil associé à ce compte pour l'instant. Pas de problème de doublon

	$newId = 1; //On met l(id du profil à 1 (aucun autre profil))
	if (!empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['bday']) && !empty($_POST['bmounth']) && !empty($_POST['byear'])) {
		//Le formulaire est complet!
		$addProfil = $bdd->prepare('INSERT INTO user (user_id, profil_id, prenom, nom, birthday, profil_parent, profil_adherent) VALUES (?,?,?,?,?,?,?)');
		$request = $addProfil->execute(array($_SESSION['user_id'], $newId, $_POST['prenom'], $_POST['nom'], $_POST['byear'].'-'.$_POST['bmounth'].'-'.$_POST['bday'],1,0));
		/*$bdd->query('INSERT INTO user (user_id, profil_id, prenom, nom, birthday, profil_parent, profil_adherent) VALUES ('.$_SESSION['user_id'].', '.$newId.', '.$_POST['prenom'].', '.$_POST['nom'].', '.$_POST['byear'].'-'.$_POST['bmounth'].'-'.$_POST['bday'].',1,0)');*/
		//On update la table de connexion
		$update = $bdd->prepare('UPDATE connexion SET profil_id=? WHERE user_id=? and profil_id=?');
		$update->execute(array($newId, $_SESSION['user_id'], 0));
		$_SESSION['profil_id'] = $newId; 
		exit("Votre profil a été créé avec succés");
	}
	else {
		exit ("Formulaire incomplet");
	}
?>