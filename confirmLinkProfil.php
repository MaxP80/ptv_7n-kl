<?php
	include("DataBase.php");

	if (isset($_GET['login']) && isset($_GET['uid']) && isset($_GET['pid'])){
		$request = $bdd->query('SELECT * from connexion WHERE login="'.$_GET['login'].'"');
		if ($request->rowCount() == 1){
			$uid_pid = $request->fetch();
			$statut_profil = $bdd->query('SELECT COUNT(profil_id) as nbProfils from user WHERE profil_id="'.$uid_pid['profil_id'].'" and user_id="'.$uid_pid['user_id'].'"');
			$nbProfil = $statut_profil->fetch()['nbProfils'];
			if (($nbProfil == 1)){
				
				$requestDataDemandeur = $bdd->query('SELECT prenom, nom from user WHERE profil_id="'.$_GET['pid'].'" and user_id="'.$_GET['uid'].'"');
				$dataDemander = $requestDataDemandeur->fetch();

				$requestMaxPID = $bdd->query('SELECT MAX(profil_id) as maxPID from user WHERE user_id = "'.$_GET['uid'].'"');
				$newPID = ($requestMaxPID->fetch()['maxPID']) + 1;

				//On met à jour les différentes tables
				$bdd->query('UPDATE user SET user_id="'.$_GET['uid'].'" , profil_id="'.$newPID.'" WHERE user_id="'.$uid_pid['user_id'].'" and profil_id="'.$uid_pid['profil_id'].'"');
				$bdd->query('UPDATE connexion SET user_id="'.$_GET['uid'].'" , profil_id="'.$newPID.'" WHERE user_id="'.$uid_pid['user_id'].'" and profil_id="'.$uid_pid['profil_id'].'"');
				$bdd->query('UPDATE membres_groupe SET user_id="'.$_GET['uid'].'" , profil_id="'.$newPID.'" WHERE user_id="'.$uid_pid['user_id'].'" and profil_id="'.$uid_pid['profil_id'].'"');
				$bdd->query('UPDATE participants SET user_id="'.$_GET['uid'].'" , profil_id="'.$newPID.'" WHERE user_id="'.$uid_pid['user_id'].'" and profil_id="'.$uid_pid['profil_id'].'"');
				$bdd->query('UPDATE verif_connexion SET user_id="'.$_GET['uid'].'" , profil_id="'.$newPID.'" WHERE user_id="'.$uid_pid['user_id'].'" and profil_id="'.$uid_pid['profil_id'].'"');
				$bdd->query('UPDATE coachs_groupe SET coach_user_id="'.$_GET['uid'].'" , coach_profil_id="'.$newPID.'" WHERE user_id="'.$uid_pid['user_id'].'" and profil_id="'.$uid_pid['profil_id'].'"');
				exit("Votre profil a bien été lié au compte de ".$dataDemander['prenom']." ".$dataDemander['nom'].".");
			} else {
				//Le profil est déjà lié à d'autres profils ou est déjà lié à celui-ci. Sachant que si deux profils sont liés, il y a au moins l'un des deux qui est parent! (Par contre on peut lier n'importe quel type de profil, enfant ou parent)
				exit ("Une errreur est survenue");
			}
		} else {
			exit("Une errreur est survenue");
		}
	} else {
		exit("Adresse invalide");
	}
?>