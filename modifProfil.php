<?php
	header('Access-Control-Allow-Origin: *');
	include("DataBase.php");
	if (isset($_POST['uid']) && isset($_POST['pid']) && isset($_POST['isAdh']) && isset($_POST['groupeMembre']) && isset($_POST['isParent']) && isset($_POST['isCoach']) && isset($_POST['groupeEntrainé']) && isset($_POST['isComite']) && isset($_POST['isAdmin'])) {
		$request = $bdd->query('SELECT nom from groupe_description');
		$data = $request->fetchAll();

		$groupesEntrainés = decodeGroupe($_POST['groupeEntrainé'], $data);
		$groupesMembres = decodeGroupe($_POST['groupeMembre'], $data);
		
		if (($_POST['isAdh'] == "Oui") && (sizeof($groupesMembres)<1)) {
			echo ("Vous devez sélectionné le groupe auquel appartient l'utilisateur");
		} else if (($_POST['isCoach'] == "Oui") && (sizeof($groupesEntrainés)<1)){
			echo ("Vous devez choisir au moins un groupe entrainé par cet utilisateur");
		} else{
			//On supprime les infos précédentes pouvant intérférer avec ces nouvelles informations
			$bdd->query('DELETE FROM membres_groupe WHERE user_id='.$_POST['uid'].' and profil_id='.$_POST['pid'].'');
			$bdd->query('DELETE FROM coachs_groupe WHERE coach_user_id='.$_POST['uid'].' and coach_profil_id='.$_POST['pid'].'');
			//On enregistre les différentes informations dans les différentes tables
			if ($_POST['isAdh'] == "Oui"){
				//On enregistre le profil en tant que membre d'un groupe
				for ($i = 0; $i < sizeof($groupesMembres); $i++){
					$bdd->query('INSERT INTO membres_groupe (id_groupe, user_id, profil_id) VALUES ((SELECT id_groupe from groupe_description WHERE nom ="'.$groupesMembres[$i].'"),'.$_POST['uid'].','.$_POST['pid'].')');
				}
			}
			if ($_POST['isCoach'] == "Oui"){
				//On enregistre le profil en tant que coach d'un groupe
				for ($i = 0; $i < sizeof($groupesEntrainés); $i++){
					$bdd->query('INSERT INTO coachs_groupe (id_groupe, coach_user_id, coach_profil_id) VALUES ((SELECT id_groupe from groupe_description WHERE nom ="'.$groupesEntrainés[$i].'"),'.$_POST['uid'].','.$_POST['pid'].')');
				}
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
			echo ('Les modifications ont réussies');
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

	function inArrayGroupeEntrainé($data, $array){
		$i = 0;
		$res = false;
		while (($i < sizeof($array)) && ($res == false)){
			if ($array[$i] == $data) {
				$res = true;
			}
			$i++;
		}
		return $res;
	}

	function printTable ($array){
		for ($i = 0; $i < sizeof($array); $i++){
			echo ($array[$i]);
		}
	}

	function decodeGroupe ($groupeEntrainé, $groupes){
		//echo ($groupeEntrainé);
		$groupesEntrainés = explode("||", $groupeEntrainé);
		//printTable($groupesEntrainés);
		$res = array();
		for ($i = 0; $i < sizeof($groupesEntrainés); $i++){
			//echo ('   On teste '.$groupesEntrainés[$i]);
			if (!(inArrayGroupeEntrainé($groupesEntrainés[$i], $res)) && inArray($groupesEntrainés[$i],$groupes)){
				array_push($res, $groupesEntrainés[$i]);
			}
		}
		//echo ('Final table ...');
		//printTable($res);
		return $res;
	}
?>