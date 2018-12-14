<?php
header('Access-Control-Allow-Origin: *');
include ("DataBase.php");
if (isset($_POST['adherent']) && isset($_POST['groupeMembre']) && isset($_POST['parent']) && isset($_POST['coach']) && isset($_POST['comite']) && isset($_POST['admin']) && isset($_POST['searchName'])) {
	//Dans ce cas, on a des critères de sélection pour choisir les profils

	//On récupère les différents noms de groupe
	$request = $bdd->query('SELECT nom from groupe_description');
	$data = $request->fetchAll();

	$trameRequest = 'SELECT u.user_id as user_id, u.profil_id as profil_id, u.nom as nom, u.prenom as prenom, u.photo as photo, u.is_coach as is_coach, u.in_comite as in_comite, u.is_admin as is_admin, u.profil_parent as profil_parent, u.profil_adherent as profil_adherent from user as u, connexion as c WHERE c.compte_valid=1 and c.valid_profil=1 and u.user_id=c.user_id and u.profil_id=c.profil_id ';
	//echo ($trameRequest);
	if ($_POST['adherent'] == "true"){
		$trameRequest = $trameRequest .'and u.profil_adherent = 1 ';
		//echo ($trameRequest);
	}
	if ($_POST['parent'] == "true"){
		//echo ($_POST['parent']);
		$trameRequest = $trameRequest .'and u.profil_parent = 1 ';
		//echo ($trameRequest);
	}
	if ($_POST['coach'] == "true"){
		$trameRequest = $trameRequest .'and u.is_coach = 1 ';
		//echo ($trameRequest);
	}
	if ($_POST['comite'] == "true"){
		$trameRequest = $trameRequest .'and u.in_comite = 1 ';
		//echo ($trameRequest);
	}
	if ($_POST['admin'] == "true"){
		$trameRequest = $trameRequest .'and u.is_admin = 1 ';
		//echo ($trameRequest);
	}
	//echo ($trameRequest);
	//Dans $trameRequest, on a la requête permettant de choisir les personnes ayant répondu aux critères liés aux status. Il faut maintenant qu'elles répondent à la recherche et au groupe (on réutilise le résultat de cette requête comme nouvelle table!)
	if (($_POST['adherent'] == "true") && (inArray($_POST['groupeMembre'],$data))){
		//echo ('new trame groupe');
		//On a une contrainte sur le groupe
		$trameRequest = 'SELECT myTable.user_id as user_id, myTable.profil_id as profil_id, myTable.nom as nom, prenom, photo, myTable.is_coach as is_coach, myTable.in_comite as in_comite, myTable.is_admin as is_admin, myTable.profil_parent as profil_parent, myTable.profil_adherent from ('.$trameRequest.') as myTable, membres_groupe as mg, groupe_description as gd WHERE myTable.profil_id=mg.profil_id and myTable.user_id=mg.user_id and mg.id_groupe=gd.id_groupe and gd.nom="'.$_POST['groupeMembre'].'"';
	}
	if ($_POST['searchName'] != ''){
		//On a saisi du texte dans la barre de recherche
		$trameRequest = 'SELECT DISTINCT myTable.user_id as user_id, myTable.profil_id as profil_id, myTable.nom as nom, prenom, photo, myTable.is_coach as is_coach, myTable.in_comite as in_comite, myTable.is_admin as is_admin, myTable.profil_parent as profil_parent, myTable.profil_adherent from ('.$trameRequest.') as myTable WHERE myTable.nom LIKE "%'.$_POST['searchName'].'%" OR myTable.prenom LIKE "%'.$_POST['searchName'].'%"';
	}
	//Il ne reste plus qu'à faire les lefts join pour récupérer le nom du groupe coaché par un profil ainsi que le groupe auquel il appartient

	//$trameRequest = 'SELECT myTable.user_id as user_id, myTable.profil_id as profil_id, myTable.nom as nom, myTable.prenom as prenom,GROUP_CONCAT(tableGroupeAdh.nom SEPARATOR "||") as groupeMembre, GROUP_CONCAT(tableCoach.nom SEPARATOR "||") as groupeCoaché, photo, myTable.is_coach as is_coach, myTable.in_comite as in_comite, myTable.is_admin as is_admin, myTable.profil_parent as profil_parent, myTable.profil_adherent from ('.$trameRequest.') as myTable LEFT JOIN (SELECT nom, user_id, profil_id FROM groupe_description as gd, membres_groupe as mg WHERE mg.id_groupe = gd.id_groupe) as tableGroupeAdh ON tableGroupeAdh.user_id = myTable.user_id and tableGroupeAdh.profil_id = myTable.profil_id LEFT JOIN (SELECT nom, coach_user_id as user_id, coach_profil_id as profil_id FROM coachs_groupe as cg, groupe_description as gd WHERE cg.id_groupe = gd.id_groupe) as tableCoach ON tableCoach.user_id = myTable.user_id and tableCoach.profil_id = myTable.profil_id GROUP BY  myTable.user_id,  myTable.profil_id';
	$trameRequest = 'SELECT myTable.user_id as user_id, myTable.profil_id as profil_id, myTable.nom as nom, myTable.prenom as prenom,GROUP_CONCAT(tableGroupeAdh.nom SEPARATOR "||") as groupeMembre, photo, myTable.is_coach as is_coach, myTable.in_comite as in_comite, myTable.is_admin as is_admin, myTable.profil_parent as profil_parent, myTable.profil_adherent from ('.$trameRequest.') as myTable LEFT JOIN (SELECT nom, user_id, profil_id FROM groupe_description as gd, membres_groupe as mg WHERE mg.id_groupe = gd.id_groupe) as tableGroupeAdh ON tableGroupeAdh.user_id = myTable.user_id and tableGroupeAdh.profil_id = myTable.profil_id GROUP BY  myTable.user_id,  myTable.profil_id';
	$trameRequest = 'SELECT myTable.user_id as user_id, myTable.profil_id as profil_id, myTable.nom as nom, myTable.prenom as prenom, myTable.groupeMembre as groupeMembre, GROUP_CONCAT(tableCoach.nom SEPARATOR "||") as groupeCoaché ,photo, myTable.is_coach as is_coach, myTable.in_comite as in_comite, myTable.is_admin as is_admin, myTable.profil_parent as profil_parent, myTable.profil_adherent from ('.$trameRequest.') as myTable LEFT JOIN (SELECT nom, coach_user_id as user_id, coach_profil_id as profil_id FROM coachs_groupe as cg, groupe_description as gd WHERE cg.id_groupe = gd.id_groupe) as tableCoach ON tableCoach.user_id = myTable.user_id and tableCoach.profil_id = myTable.profil_id GROUP BY  myTable.user_id,  myTable.profil_id';
	//echo ($trameRequest);
	$request = $bdd->query($trameRequest);
	echo json_encode($request->fetchAll());
} else {
	$trameRequest = 'SELECT u.user_id, u.profil_id, u.nom, u.prenom, u.photo, u.is_coach as is_coach, u.in_comite as in_comite, u.is_admin as is_admin, u.profil_parent as profil_parent, u.profil_adherent as profil_adherent from user as u, connexion as c WHERE c.compte_valid=1 and c.valid_profil=1 and u.user_id=c.user_id and u.profil_id=c.profil_id';
	//$trameRequest = 'SELECT myTable.user_id as user_id, myTable.profil_id as profil_id, myTable.nom as nom, myTable.prenom as prenom, GROUP_CONCAT(tableGroupeAdh.nom SEPARATOR "||") as groupeMembre, GROUP_CONCAT(tableCoach.nom SEPARATOR "||") as groupeCoaché, photo, myTable.is_coach as is_coach, myTable.in_comite as in_comite, myTable.is_admin as is_admin, myTable.profil_parent as profil_parent, myTable.profil_adherent as profil_adherent from ('.$trameRequest.') as myTable LEFT JOIN (SELECT nom, user_id, profil_id FROM groupe_description as gd, membres_groupe as mg WHERE mg.id_groupe = gd.id_groupe) as tableGroupeAdh ON tableGroupeAdh.user_id = myTable.user_id and tableGroupeAdh.profil_id = myTable.profil_id LEFT JOIN (SELECT nom, coach_user_id as user_id, coach_profil_id as profil_id FROM coachs_groupe as cg, groupe_description as gd WHERE cg.id_groupe = gd.id_groupe) as tableCoach ON tableCoach.user_id = myTable.user_id and tableCoach.profil_id = myTable.profil_id GROUP BY  myTable.user_id,  myTable.profil_id';
	$trameRequest = 'SELECT myTable.user_id as user_id, myTable.profil_id as profil_id, myTable.nom as nom, myTable.prenom as prenom,GROUP_CONCAT(tableGroupeAdh.nom SEPARATOR "||") as groupeMembre, photo, myTable.is_coach as is_coach, myTable.in_comite as in_comite, myTable.is_admin as is_admin, myTable.profil_parent as profil_parent, myTable.profil_adherent from ('.$trameRequest.') as myTable LEFT JOIN (SELECT nom, user_id, profil_id FROM groupe_description as gd, membres_groupe as mg WHERE mg.id_groupe = gd.id_groupe) as tableGroupeAdh ON tableGroupeAdh.user_id = myTable.user_id and tableGroupeAdh.profil_id = myTable.profil_id GROUP BY  myTable.user_id,  myTable.profil_id';
	$trameRequest = 'SELECT myTable.user_id as user_id, myTable.profil_id as profil_id, myTable.nom as nom, myTable.prenom as prenom, myTable.groupeMembre as groupeMembre, GROUP_CONCAT(tableCoach.nom SEPARATOR "||") as groupeCoaché ,photo, myTable.is_coach as is_coach, myTable.in_comite as in_comite, myTable.is_admin as is_admin, myTable.profil_parent as profil_parent, myTable.profil_adherent from ('.$trameRequest.') as myTable LEFT JOIN (SELECT nom, coach_user_id as user_id, coach_profil_id as profil_id FROM coachs_groupe as cg, groupe_description as gd WHERE cg.id_groupe = gd.id_groupe) as tableCoach ON tableCoach.user_id = myTable.user_id and tableCoach.profil_id = myTable.profil_id GROUP BY  myTable.user_id,  myTable.profil_id';
	$request = $bdd->query($trameRequest);
	//echo ($trameRequest);
	echo json_encode($request->fetchAll());
}

	function inArray($data, $array){
		$i = 0;
		$res = false;
		while (($i < sizeof($array)) && ($res == false)){
			if ($array[$i]['nom'] == $data) {
				$res = true;
			}
			$i++;
		}
		return $res;
	}
?>