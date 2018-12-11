<?php 
	session_start();
	include("DataBase.php");
	if (!empty($_FILES["certificat"])){
		//On est certains que le nom, le preom et la date sont données car on a déclarés les inputs en required! De plus, on sait que la personne possède un user_id
		if ($_FILES['certificat']['size'] < 1048576) {
			//Le fichier respecte bien la limitte de taille
		$newId = 1;
			if(move_uploaded_file($_FILES["certificat"]["tmp_name"], "upload/".$_SESSION['user_id']."_".$newId.".pdf")) {
			//Plus qu'ici a régler!
				//Le fichier a été umplaodé avec succés. Il faut à présent créer ce nouveau profil dans la base de donnée
				if (!empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['bday']) && !empty($_POST['bmounth']) && !empty($_POST['byear'])) {
					//Le formulaire est complet!
					$addProfil = $bdd->prepare('INSERT INTO user (user_id, profil_id, prenom, nom, birthday, profil_parent, profil_adherent, certificat_medical) VALUES (?,?,?,?,?,?,?,?)');
					$request = $addProfil->execute(array($_SESSION['user_id'], 1, $_POST["prenom"], $_POST['nom'], $_POST['byear'].'-'.$_POST['bmounth'].'-'.$_POST['bday'], (($_POST['isParent'] == 'true')? '1':'0'),(($_POST['isAdherent'] == 'true')? '1':'0'), "upload/".$_SESSION['user_id']."_".$newId.".pdf"));
					$update = $bdd->prepare('UPDATE connexion SET profil_id=? WHERE user_id=? and profil_id=?');
					$update->execute(array($newId, $_SESSION['user_id'], 0));
					$_SESSION['profil_id'] = $newId; 
					exit ("Votre profil a été créé avec succés");
				}
				else {
					exit ("Formulaire incomplet");
				}
			}
			else
			{
				exit ("Une erreur est survenue lors de l'upload du fichier, veuillez recommencer");
			}
		}
		else {
			exit("La taille du fichier est trpo gros");
		}
	}
	else
	{
		//Ce cas est déjà pris en compte dans la page html (permet d'éviter un échange entre le client et le serveur qui serait inutile)
		exit ("Vous devez fournir un certificat médical");
	}
?>