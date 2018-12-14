<?php
header('Access-Control-Allow-Origin: *');
	include("DataBase.php");
	$delete = false;
	if (isset($_POST['uid']) && isset($_POST['pid'])) {
		//On supprime le profil de la base de données. Il faut aussi supprimer les profils enfant si plus aucun profil ne peut y avoir accès!

		//On compte le nombre de profil parent autre que le profil à supprimé
		$request = $bdd->query('SELECT user_id, profil_id from user WHERE profil_parent=1 and user_id="'.$_POST['uid'].'" and profil_id<>"'.$_POST['pid'].'"');
		$nbProfilParent = $request->rowCount();
		$dataParent = $request->fetchAll();
		//On compte le nombre de profil seulement adhérent et n'auant pas de login/pwd.
		$request = $bdd->query('SELECT user.user_id as user_id, user.profil_id as profil_id from user, connexion WHERE profil_parent=0 and profil_adherent=1 and user.profil_id = connexion.profil_id and user.user_id = connexion.user_id and connexion.login is null and user.user_id="'.$_POST['uid'].'" and user.profil_id<>"'.$_POST['pid'].'"');
		$nbProfilEnfant = $request->rowCount();
		$dataEnfant = $request->fetchAll();
		//On regarde s'il est parent
		$request = $bdd->query('SELECT profil_parent from user WHERE user_id="'.$_POST['uid'].'" and profil_id="'.$_POST['pid'].'"');
		$isParent = $request->fetch()['profil_parent'];

		//echo ("nbProfilParent =".$nbProfilParent." || nbProfilEnfant =".$nbProfilEnfant. ' || isParent = '.$isParent);
		if (($isParent == 1) && ($nbProfilParent == 0) && ($nbProfilEnfant > 0)){
			//La suppresion de ce profil entraîne la naissance de profil fantôme (on ne peut plus y avoir accès)
			//On redemande confirmation pour supprimer le profil
			//echo ("In boucle");
			//echo($_POST['confirm_delete']);
			if (isset($_POST['confirm_delete']) && ($_POST['confirm_delete'] == 'true')){
				//on a bien confirmer la supression de tous les comptes
				for ($i = 0; $i < $nbProfilEnfant; $i++){
					//echo ('del :'.$i.' => uid :'.$dataEnfant[$i]['user_id'].' & pid :'.$dataEnfant[$i]['profil_id']);
					$bdd->query('DELETE FROM user WHERE user_id="'.$dataEnfant[$i]['user_id'].'" and profil_id="'.$dataEnfant[$i]['profil_id'].'"');
					$bdd->query('DELETE FROM connexion WHERE user_id="'.$dataEnfant[$i]['user_id'].'" and profil_id="'.$dataEnfant[$i]['profil_id'].'"');
					$bdd->query('DELETE FROM  coachs_groupe WHERE coach_user_id="'.$dataEnfant[$i]['user_id'].'" and coach_profil_id="'.$dataEnfant[$i]['profil_id'].'"');
					$bdd->query('DELETE FROM connexion WHERE user_id="'.$dataEnfant[$i]['user_id'].'" and profil_id="'.$dataEnfant[$i]['profil_id'].'"');
					$bdd->query('DELETE FROM membres_groupe WHERE user_id="'.$dataEnfant[$i]['user_id'].'" and profil_id="'.$dataEnfant[$i]['profil_id'].'"');
					$bdd->query('DELETE FROM participants WHERE user_id="'.$dataEnfant[$i]['user_id'].'" and profil_id="'.$dataEnfant[$i]['profil_id'].'"');
					$bdd->query('DELETE FROM verif_connexion WHERE user_id="'.$dataEnfant[$i]['user_id'].'" and profil_id="'.$dataEnfant[$i]['profil_id'].'"');
					$bdd->query('UPDATE evenements SET id_user_encadrant=NULL, id_profil_encadrant=NULL WHERE id_user_encadrant="'.$dataEnfant[$i]['user_id'].'" and id_profil_encadrant="'.$dataEnfant[$i]['profil_id'].'"');
				}
				$delete = true;
			} else {
				echo ("La supression de ce compte entraîne la supression d'autres comptes qui lui sont liés");
				$delete = false;
			}
		} else {
			//Il n'y a aucune confirmation a demandé.
			//Dans le cas de l'appel de la page depuis la validation d'un profil, on est toujours ici car un tel profil n'a pas encore put créer de profil enfant!
			//echo ("in autre conditions");
			$delete = true;
		}
		if ($delete) {		
			$bdd->query('DELETE FROM user WHERE user_id="'.$_POST['uid'].'" and profil_id="'.$_POST['pid'].'"');
			$bdd->query('DELETE FROM connexion WHERE user_id="'.$_POST['uid'].'" and profil_id="'.$_POST['pid'].'"');
			$bdd->query('DELETE FROM  coachs_groupe WHERE coach_user_id="'.$_POST['uid'].'" and coach_profil_id="'.$_POST['pid'].'"');
			$bdd->query('DELETE FROM connexion WHERE user_id="'.$_POST['uid'].'" and profil_id="'.$_POST['pid'].'"');
			$bdd->query('DELETE FROM membres_groupe WHERE user_id="'.$_POST['uid'].'" and profil_id="'.$_POST['pid'].'"');
			$bdd->query('DELETE FROM participants WHERE user_id="'.$_POST['uid'].'" and profil_id="'.$_POST['pid'].'"');
			$bdd->query('DELETE FROM verif_connexion WHERE user_id="'.$_POST['uid'].'" and profil_id="'.$_POST['pid'].'"');
			$bdd->query('UPDATE evenements SET id_user_encadrant=NULL, id_profil_encadrant=NULL WHERE id_user_encadrant="'.$_POST['uid'].'" and id_profil_encadrant="'.$_POST['pid'].'"');
			echo ("Le profil a été supprimé");
		}
	} else {
		echo('Une erreur est survenue');
	}
?>