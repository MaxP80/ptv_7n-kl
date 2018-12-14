<?php
	include("DataBase.php");
	if (isset($_POST['id_groupe']) && isset($_POST['nom']) && isset($_POST['entrainements'])){
		if ($_POST['id_groupe'] == 'NaN'){
			//On regarde si le nom du groupe n'est pas déjà utilisé
			$nameAlreadyUsedRequest = $bdd->query('SELECT id_groupe from groupe_description WHERE nom="'.$_POST['nom'].'"');
			$nameAlreadyUsed = ($nameAlreadyUsedRequest->RowCount() > 0)? true : false;
			if ($nameAlreadyUsed == false) {
				//Dans ce cas, on a créé un nouveau groupe
				$request = $bdd->query('SELECT MAX(id_groupe) as maxID from groupe_description');
				$newID = ($request->fetch()['maxID'])+1;
				$idGroupe = $newID;
				$bdd->query('INSERT INTO groupe_description (id_groupe, nom) VALUES ('.$idGroupe.', "'.$_POST['nom'].'")');
				//Code en commun dans les deux cas!
				$entrainementsInfo = decodeEntrainements($_POST['entrainements']);
				for ($i = 0; $i<sizeof($entrainementsInfo); $i++){
					//echo ('taile :'.sizeof($entrainementsInfo[$i]).' et égal à 3 '.(sizeof($entrainementsInfo[$i]) == 3).'||');
					if ((sizeof($entrainementsInfo[$i]) == 3)==1){
						$day = $entrainementsInfo[$i][0];
						$hDebut = $entrainementsInfo[$i][1];
						$hFin = $entrainementsInfo[$i][2];
						$trameRequest = 'INSERT INTO entrainement (id_groupe_entrainement, day, heure_debut, heure_fin) VALUES ('.$idGroupe.', "'.$day.'","'.$hDebut.'","'.$hFin.'")';
						//echo($trameRequest);
						$bdd->query($trameRequest);
						//echo ('Ajouté');
					}
				}
				echo ("Le groupe ".$_POST['nom']." a été créé");
			} else {
				echo ('Un groupe possède déjà ce nom');
			}
		} else {
			$nameAlreadyUsedRequest = $bdd->query('SELECT id_groupe from groupe_description WHERE id_groupe<>"'.$_POST['id_groupe'].'" and nom="'.$_POST['nom'].'"');
			$nameAlreadyUsed = ($nameAlreadyUsedRequest->RowCount() > 0)? true : false;
			if ($nameAlreadyUsed == false) {
				//on a modifié un groupe existant. On va donc supprimer les informations précédentes vis-à-vis des entrainements et rentrer les nouveaux horraires
				$idGroupe = $_POST['id_groupe'];
				$bdd->query('DELETE FROM entrainement WHERE id_groupe_entrainement = '.$_POST['id_groupe'].'');
				$bdd->query('UPDATE groupe_description SET nom="'.$_POST['nom'].'" WHERE id_groupe='.$_POST['id_groupe'].'');
				//Code en commun dans les deux cas!
				$entrainementsInfo = decodeEntrainements($_POST['entrainements']);
				for ($i = 0; $i<sizeof($entrainementsInfo); $i++){
					//echo ('taile :'.sizeof($entrainementsInfo[$i]).' et égal à 3 '.(sizeof($entrainementsInfo[$i]) == 3).'||');
					if ((sizeof($entrainementsInfo[$i]) == 3)==1){
						$day = $entrainementsInfo[$i][0];
						$hDebut = $entrainementsInfo[$i][1];
						$hFin = $entrainementsInfo[$i][2];
						$trameRequest = 'INSERT INTO entrainement (id_groupe_entrainement, day, heure_debut, heure_fin) VALUES ('.$_POST['id_groupe'].', "'.$day.'","'.$hDebut.'","'.$hFin.'")';
						//echo($trameRequest);
						$bdd->query($trameRequest);
						//echo ('Ajouté');
					}
				}
				echo('Les mofifications ont été faites');	
			} else {
				echo ('Un groupe possède déjà ce nom');
			}

		}
	}


	function decodeEntrainements($string){
		$entrainementInfo = explode("||", $string);
		$array = array();
		for ($i = 0; $i<sizeof($entrainementInfo); $i++){
			array_push($array, explode(";",$entrainementInfo[$i]));
		}
		return $array;
	}
?>