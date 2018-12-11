<?php 
	session_start();
	if (isset($_SESSION['isConnected'])) {
		//Dans ce cas, la session est bien définit (n'a pas été détruite)

		if (($_SESSION['isConnected']) == true) {
			if (time() - $_SESSION['last_activity'] > (30 * 60)) 
			{
				$_SESSION['isConnected'] = false;
				// On détruit les variables de notre session
				session_unset();
				//$_SESSION = array();
				// On détruit notre session
				session_destroy();
				echo 'index.html';
			}
			else {
				//Dans ce cas, l'utilisateur est bien connecté et la session n'a pas expiré, il faut maintenant regarder s'il peut être sur cette page
				$_SESSION["last_activity"] = time();
				if ($_SESSION['profil_id'] > 0){
					//On regarde si le profil a été validé par l'admin!
					if ($_SESSION['valid_profil'] == 1){
						//Profil_validé
						echo "Success";
					} else {
						//Profil en attente
						echo "no_valid_profil.html";
					}
				} else {
					//Le profil n'a pas été initialisé, on le renvoie vers cette page!
					echo "set_profil.html";
				}
			}
		}
		else {
			echo "index.html";
		}
	} else {
		echo "index.html";
	}
?>