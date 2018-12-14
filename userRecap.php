<?php
header('Access-Control-Allow-Origin: *');
	include ("DataBase.php");
	$res = null;
	if (isset($_POST['uid']) && isset($_POST['pid'])) {
		$request = $bdd->query('SELECT user.prenom, user.nom, DAY(user.birthday) as bday, MONTH(user.birthday) as mday, YEAR(user.birthday) as yday, userGroupeInformation.nomGroupe, user.photo FROM user LEFT JOIN (SELECT nom as nomGroupe, user_id, profil_id from groupe_description, membres_groupe WHERE membres_groupe.id_groupe = groupe_description.id_groupe ) as userGroupeInformation ON user.user_id=userGroupeInformation.user_id and user.profil_id=userGroupeInformation.profil_id WHERE user.user_id="'.$_POST['uid'].'" and user.profil_id="'.$_POST['pid'].'"');
		$res = $request->fetch();
	} else {
		//On ne fait rien
	}
	echo json_encode($res);
?>