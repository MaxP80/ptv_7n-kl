<?php
	header('Access-Control-Allow-Origin: *');
	session_start();
	include("DataBase.php");
	include("isConnectedMobilApp.php");
	if ((!empty($_POST['old_pwd'])) && (!empty($_POST['new_pwd'])) && (!empty($_POST['new_pwd_confirm']))){
		$request = $bdd->query('SELECT * from connexion WHERE user_id="'.$_SESSION['user_id'].'" and profil_id="'.$_SESSION['profil_id'].'" and login="'.$_SESSION['login'].'" and pwd="'.$_POST['old_pwd'].'"');
		if ($request->rowCount() != 1){
			//Il y aune erreur avec le mot de passe saisit en tant que mot de passe actuel
			exit("Mot de passe incorect");
		}
		else {
			if ($_POST['new_pwd'] == $_POST['new_pwd_confirm']){
				$update = $bdd->query('UPDATE connexion SET pwd = "'.$_POST['new_pwd'].'" WHERE user_id="'.$_SESSION['user_id'].'" and profil_id="'.$_SESSION['profil_id'].'"');
				exit("Mot de passe changé avec succès");
			} else {
				exit("Erreur lors de la confirmation du mot de passe");
			}
		}
	} else {
		exit('Procèdure avortée');
	}
?>