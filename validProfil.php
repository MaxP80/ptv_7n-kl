<?php 
	include("DataBase.php");
	if (isset($_POST['uid']) && isset($_POST['pid']) && isset($_POST['isAdh']) && isset($_POST['groupeMembre']) && isset($_POST['isParent']) && isset($_POST['isCoach']) && isset($_POST['groupeEntrainé']) && isset($_POST['isComite']) && isset($_POST['isAdmin'])) {
		$request = $bdd->query('SELECT nom from groupe_description');
		$data = $request->fetchAll();
		if (($_POST['isAdh'] == "Oui") && !(inArray($_POST['groupeMembre'],$data))) {
			echo ("Vous devez sélectionné le groupe auquel appartient l'utilisateur");
		} else if (($_POST['isCoach'] == "Oui") && !(inArray($_POST['groupeEntrainé'], $data))){
			echo ("Vous devez choisir le groupe entrainé par cet utilisateur");
		} else{
			//On enregistre les différentes informations dans les différentes tables
			if ($_POST['isAdh'] == "Oui"){
				//On enregistre le profil en tant que membre d'un groupe
				$bdd->query('INSERT INTO membres_groupe (id_groupe, user_id, profil_id) VALUES ((SELECT id_groupe from groupe_description WHERE nom ="'.$_POST['groupeMembre'].'"),'.$_POST['uid'].','.$_POST['pid'].')');
			}
			if ($_POST['isCoach'] == "Oui"){
				//On enregistre le profil en tant que coach d'un groupe
				$bdd->query('INSERT INTO coachs_groupe (id_groupe, coach_user_id, coach_profil_id) VALUES ((SELECT id_groupe from groupe_description WHERE nom ="'.$_POST['groupeEntrainé'].'"),'.$_POST['uid'].','.$_POST['pid'].')');
			}
			//On update le statut du profil
			$updateStatus = $bdd->prepare('UPDATE user SET is_coach=?, profil_parent=?, profil_adherent=?, in_comite=?, is_admin=?  WHERE user_id=? and profil_id=?');
			$updateStatus->execute(array(
				($_POST['isCoach'] == "Oui")? 1:0,
				($_POST['isParent'] == "Oui")? 1:0,
				($_POST['isAdh'] == "Oui")? 1:0,
				($_POST['isComite'] == "Oui")? 1:0,
				($_POST['isAdmin'] == "Oui")? 1:0,
				$_POST['uid'],
				$_POST['pid']
			));
			//On update le profil en le passant en vali
			$bdd->query('UPDATE connexion SET valid_profil=1 WHERE user_id="'.$_POST['uid'].'" and profil_id="'.$_POST['pid'].'"');
			echo ("Le profil a été validé");
		}
	} else {
		echo('Une erreur est survenue');
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