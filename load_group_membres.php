<?php
	session_start();
	include ("DataBase.php");
	include ("isConnectedMobilApp.php");
	$res = null;
	//echo 'OUH';
	if (isset($_SESSION['user_id']) && isset($_SESSION['profil_id']) && isset($_POST['id'])){
		$isAllowedRequest = $bdd->query('SELECT in_comite, is_admin, (CASE WHEN "'.$_POST['id'].'" IN (SELECT id_groupe from coachs_groupe WHERE coach_user_id="'.$_SESSION['user_id'].'" and coach_profil_id="'.$_SESSION['profil_id'].'") THEN "true" ELSE "FALSE" END) as coach_du_group FROM user WHERE user_id="'.$_SESSION['user_id'].'" and profil_id="'.$_SESSION['profil_id'].'"');
		$isAllowed = $isAllowedRequest->fetch();
		if (($isAllowed['in_comite'] == 'true') || ($isAllowed['is_admin'] == 'true') || ($isAllowed['coach_du_group'] == 'true')){
			$request = $bdd->query('SELECT user.user_id, user.profil_id, user.nom, user.prenom, user.photo from user, membres_groupe WHERE user.user_id = membres_groupe.user_id and user.profil_id=membres_groupe.profil_id and membres_groupe.id_groupe = "'.$_POST['id'].'"');
			$res = $request->fetchAll();
		}
	}
	echo json_encode($res);
?>