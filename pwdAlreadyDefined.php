<?php 
	session_start();
	//On est certain que l'utilisateur est bien connecté lorsqu'on apelle cette fonction
	include("DataBase.php");
	if (isset($_POST['profil_id'])) {
		$request = $bdd->query('SELECT login from connexion WHERE user_id="'.$_SESSION['user_id'].'" and profil_id="'.$_POST['profil_id'].'"');
		if ($request->fetch()['login'] != null)
			//Le login et mot de passe ont déjà été définit
			exit('true');
		else 
			exit('false');
	}
	else 
		exit('false');
?>