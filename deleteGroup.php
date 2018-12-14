<?php
	include("DataBase.php");
	if (isset($_POST['id'])){
		$bdd->query('DELETE FROM entrainement WHERE id_groupe_entrainement="'.$_POST['id'].'"');
		$bdd->query('DELETE FROM participants WHERE id_evenement_participant IN (SELECT id_evenement from evenements WHERE id_groupe = "'.$_POST['id'].'")');
		$bdd->query ('DELETE FROM evenements WHERE id_groupe="'.$_POST['id'].'"');
		$bdd->query('DELETE FROM membres_groupe WHERE id_groupe="'.$_POST['id'].'"');
		$requestCoachAffecte = $bdd->query('SELECT coach_user_id, coach_profil_id FROM coachs_groupe WHERE id_groupe="'.$_POST['id'].'"');
		for ($i = 0; $i < $requestCoachAffecte->rowCount(); $i++){
			//echo ("Test update coach");
			$dataCoach = $requestCoachAffecte->fetch();
			//On regarde s'il est encore coach d'un autre groupe
			$request = $bdd->query('SELECT COUNT(id_groupe) as otherGroup from coachs_groupe WHERE coach_user_id="'.$dataCoach['coach_user_id'].'" and coach_profil_id="'.$dataCoach['coach_profil_id'].'" and id_groupe<>"'.$_POST['id'].'"');
			if (($request->fetch()['otherGroup']) == 0){
				$bdd->query('UPDATE user SET is_coach = 0 WHERE user_id="'.$dataCoach['coach_user_id'].'" and profil_id="'.$dataCoach['coach_profil_id'].'"');
			}
		}
		$bdd->query('DELETE FROM coachs_groupe WHERE id_groupe="'.$_POST['id'].'"');
		//On supprime en dernier les données dans la table groupe_description à cause des clès étrangères!
		$bdd->query('DELETE FROM groupe_description WHERE id_groupe="'.$_POST['id'].'"');
		exit ("Le groupe a été supprimé");
	} else {
		exit ("Une erreur est survenue");
	}
?>