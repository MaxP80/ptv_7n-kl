<?php 
	include("DataBase.php");
	if (isset($_POST['uid']) && isset($_POST['pid'])) {
		//On supprime le profil de la base de données (à ce stade, les informations de son profil sont dans la table user et connexion)
		$bdd->query('DELETE FROM user WHERE user_id="'.$_POST['uid'].'" and profil_id="'.$_POST['pid'].'"');
		$bdd->query('DELETE FROM connexion WHERE user_id="'.$_POST['uid'].'" and profil_id="'.$_POST['pid'].'"');
		$bdd->query('DELETE FROM  coachs_groupe WHERE coach_user_id="'.$_POST['uid'].'" and coach_profil_id="'.$_POST['pid'].'"');
		$bdd->query('DELETE FROM connexion WHERE user_id="'.$_POST['uid'].'" and profil_id="'.$_POST['pid'].'"');
		$bdd->query('DELETE FROM membres_groupe WHERE user_id="'.$_POST['uid'].'" and profil_id="'.$_POST['pid'].'"');
		$bdd->query('DELETE FROM participants WHERE user_id="'.$_POST['uid'].'" and profil_id="'.$_POST['pid'].'"');
		$bdd->query('DELETE FROM verif_connexion WHERE user_id="'.$_POST['uid'].'" and profil_id="'.$_POST['pid'].'"');
		$bdd->query('UPDATE evenements SET id_user_encadrant=NULL, id_profil_encadrant=NULL WHERE id_user_encadrant="'.$_POST['uid'].'" and id_profil_encadrant="'.$_POST['pid'].'"');
		echo ("Le profil a été refusé");
	} else {
		echo('Une erreur est survenue');
	}
?>