<?php
header('Access-Control-Allow-Origin: *');
	session_start();
	include("DataBase.php");
	include("isConnectedMobilApp.php");

	//On va update les champs qui on été remplies.
	//On peut remarquer que les champs noms et prenoms et bday ont toujours une valeur et seront réécrit à chaque update
	if (!empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['bday']) && !empty($_POST['bmonth']) && !empty($_POST['byear']) && (isset($_POST['isParent'])) && isset($_POST['isAdherent']) && isset($_POST['profil_id'])) {
		$requestAlreadyExist = $bdd->prepare('SELECT profil_id from user WHERE prenom=? and nom=? and user_id=? and profil_id<>?');
		$requestAlreadyExist->execute(array($_POST['prenom'],$_POST['nom'], $_SESSION['user_id'], $_POST['profil_id']));

		$requestOldStatus = $bdd->prepare('SELECT profil_parent, profil_adherent from user WHERE user_id=? and profil_id=?');
		$requestOldStatus->execute(array($_SESSION['user_id'], $_POST['profil_id']));
		$OldStatus = $requestOldStatus->fetch();
		$statuChanged = ( ($OldStatus['profil_parent'] == $_POST['isParent']) && ($OldStatus['profil_adherent'] == $_POST['isAdherent']) )? 'false'/*Le statut du profil n'a pas changé*/ : 'true';

		if ($requestAlreadyExist->rowCount() != 0) 
		{
			//Un profil existe déjà avec ces informations!
			exit ("Un autre profil avec ces mêmes informations existe déjà");
		}
		else {
			//Le nom et prenom qui va être modifié ne pose aucun problème

			if (($_POST['isParent'] == 'true')) {
				/*$requestProfilParent = $bdd->prepare('SELECT count(profil_id) as nbProfilParent from user WHERE user_id=? and profil_id<>? and profil_parent=1');
				$requestProfilParent->execute(array($_SESSION['user_id'], $_POST['profil_id']));
				if ($requestProfilParent->fetch()['nbProfilParent'] > 0) {
					exit ('Un profil parent est déjà associé à ce compte');
				} else {*/
					//On le passe en profil parent
					$update = $bdd->prepare('UPDATE user SET profil_parent=1 WHERE profil_id=? and user_id=?');
					$update->execute(array($_POST['profil_id'], $_SESSION['user_id']));
				/*}*/
			} else {
				$update = $bdd->prepare('UPDATE user SET profil_parent=0 WHERE profil_id=? and user_id=?');
				$update->execute(array($_POST['profil_id'], $_SESSION['user_id']));
			}



			if ((!empty($_FILES["certificat"])) && ($_POST['isAdherent'] == 'true')){
				//On a fournit un certificat médical
				if ($_FILES['certificat']['size'] < 1048576) {
					if(move_uploaded_file($_FILES["certificat"]["tmp_name"], "upload/".$_SESSION['user_id']."_".$_POST['profil_id'].".pdf")){
						$update = $bdd->prepare('UPDATE user SET certificat_medical=?, profil_adherent=1 WHERE profil_id=? and user_id=?');
						$update->execute(array("upload/".$_SESSION['user_id']."_".$_POST['profil_id'].".pdf", $_POST['profil_id'], $_SESSION['user_id']));
					}
					else {
						exit("Une erreur est survenue lors de l'upload du certificat médical");
					}
				} else {
					exit("Le fichier que vous tentez d'uploadé est trop gros!");
				}
			} else {
				//Il n'y a pas de certificat médiacl mais il faut vérifier qu'il ne s'agit pas d'un nouvel adhérent!
				if (($_POST['isAdherent'] == 'true')) {
					$request = $bdd->prepare('SELECT profil_adherent from user WHERE profil_id=? and user_id = ?');
					$request->execute(array($_POST['profil_id'], $_SESSION['user_id']));
					$data = $request->fetch();
					if (($data['profil_adherent'] == true)) {
						//Il n'y a pas de problème car il est déjà adhérent donc à déjà uplaodé un certificat médical
					}
					else {
						exit("Vous devez uploadé un certificat médical en tant que nouvel adhérent");
					}
				} else {
					$update = $bdd->prepare('UPDATE user SET profil_adherent=0 WHERE profil_id=? and user_id=?');
					$update->execute(array($_POST['profil_id'], $_SESSION['user_id']));
					//Et on le supprime des tables liés au groupes en tant qu'adh s'il était précédement adh
					$bdd->query('DELETE FROM membres_groupe WHERE user_id="'.$_SESSION['user_id'].'" and profil_id="'.$_POST['profil_id'].'"');
					$bdd->query('DELETE FROM participants WHERE user_id="'.$_SESSION['user_id'].'" and profil_id="'.$_POST['profil_id'].'"');
				}
			}




			if (!empty($_FILES["photo"])){
				//On a fournit un certificat médical
				if ($_FILES['photo']['size'] < 1048576) {
					if(move_uploaded_file($_FILES["photo"]["tmp_name"], "upload/photo_".$_SESSION['user_id']."_".$_POST['profil_id'].".pdf")){
						$update = $bdd->prepare('UPDATE user SET photo=? WHERE profil_id=? and user_id=?');
						$update->execute(array("upload/photo_".$_SESSION['user_id']."_".$_POST['profil_id'].".pdf", $_POST['profil_id'], $_SESSION['user_id']));
					}
					else {
						exit("Une erreur est survenue lors de l'upload du certificat médical");
					}
				} else {
					exit("Le fichier que vous tentez d'uploadé est trop gros!");
				}
			}
			if (isset($_POST["phone_number"])) {
				$updateProfil = $bdd->prepare('UPDATE user SET phone_number=? WHERE profil_id=? and user_id=?');
				$updateProfil->execute(array($_POST["phone_number"],$_POST["profil_id"], $_SESSION['user_id']));
			}
			//On n'a plus qu'à update le nom, prenom et date de naissance
			$updateProfil = $bdd->prepare('UPDATE user SET prenom = ?, nom = ?, birthday = ? WHERE profil_id=? and user_id=?');
			$updateProfil->execute(array($_POST["prenom"], $_POST["nom"], $_POST["byear"].'-'.$_POST["bmonth"].'-'.$_POST["bday"],$_POST["profil_id"], $_SESSION['user_id']));


			$request = $bdd->query('SELECT is_admin from user WHERE user_id="'.$_SESSION['user_id'].'" and profil_id="'.$_SESSION['profil_id'].'"');
			$isAdmin = $request->fetch()['is_admin'];

			if (($statuChanged == 'true') && ($isAdmin == '0')) {
				//Le statut a changé et doit être validé par un admin (sauf si l'utilisateur est un admin)
				$updateConnexion = $bdd->prepare('UPDATE connexion SET valid_profil=? WHERE profil_id=? and user_id=?');
				$updateConnexion->execute(array(0, $_POST['profil_id'], $_SESSION['user_id']));
			if ($_POST['profil_id'] == $_SESSION['profil_id']) {$_SESSION['valid_profil'] = 0; /*on a modifié notre propre profil*/}
				exit("Votre profil a été modifié et doit être de nouveau validé par un administrateur");
			} //Sinon on ne fait rien

			exit ("Votre profil a été modifié avec succés");
			//Profil de vient non valide quand modif certificat médical (NON) 
		}
	} else {
		exit("Modification impossible: certaines informations sont manquantes");
	}
?>