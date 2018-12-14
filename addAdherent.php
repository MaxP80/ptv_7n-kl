<?php 
	header('Access-Control-Allow-Origin: *');
	session_start();
	include("DataBase.php");
	include("isConnectedMobilApp.php");
	require ('PHPMailer/PHPMailerAutoload.php');
	require ('connectToMail.php');

	//Déclaration d'une clé de sécurité'
	function randomKey() {
	    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
	    $key = array(); //remember to declare $pass as an array
	    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	    for ($i = 0; $i < 8; $i++) {
	        $n = rand(0, $alphaLength);
	        $key[] = $alphabet[$n];
	    }
	    return implode($key); //turn the array into a string thanks to implode function
	}
if ((!empty($_POST['login'])) && (!empty($_POST['pwd'])) && (!empty($_POST['pwd_confirm']))) {
	$login_mail = $_POST['login'];
	$pwd = $_POST['pwd'];
	$pwd2 = $_POST['pwd_confirm'];

	if ($pwd === $pwd2) {
		//Le mot de passe a été saisi deux fois de la même façon. On envoie le mail pour finaliser l'inscription
		$request = $bdd->prepare('SELECT login, compte_valid from connexion WHERE login=?');
		$request->execute(array($login_mail));
		//On regarde si le mail n'est pas utilisé pour un compte valide!

		if ($request->rowCount() == 0 /*aucun compte avec ce login*/) { 
			/*
			//On créer un nouveau compte (nouvealle ligne dans bdd)
			$addCompte = $bdd->prepare('INSERT INTO `connexion` (`login`, `pwd`) VALUES (?,?);');
			$addCompte->execute(array($login_mail, $pwd));
			//On ajoute aussi le code de validité pour le mail
			$addCodeVerif = $bdd->prepare('INSERT INTO verif_connexion (user_id, profil_id, code) VALUES ((SELECT user_id from connexion WHERE login=? and pwd=?),(SELECT profil_id from connexion WHERE login=? and pwd=?),?)');
			$key = randomKey();
			$addCodeVerif->execute(array($login_mail, $pwd,$login_mail, $pwd,$key));

			$sujet = "[Val'Acro] Création de votre compte";
			$message_txt = "Bonjour, \nVous avez demandé la création d'un compte sur Val'Acro. Veuillez cliquer sur le lien suivant pour finaliser votre inscription :\n \nSi vous n'avez pas fait cette demande, ignorez ce mail.";
			$message_html = "<html><head></head><body>
			<p>Bonjour, </p>
			<p>Vous avez demandé la création d'un compte sur Val'Acro. Veuillez cliquer sur le lien suivant pour finaliser votre inscription :</p>
			<p> <a href='localhost/ValAcro/confirmation_creation_compte.php?login=".$login_mail."&key=".$key."'>Confirmer mon compte</a> </p>
			<p>Si vous n'avez pas fait cette demande, ignorez ce mail.</p>
			</body></html>";
			include('send_mail.php');
			echo "Success";*/

			
			if (!empty($_FILES["certificat"])){
				//On est certains que le nom, le prenom et la date sont données car on a déclarés les inputs en required! De plus, on sait que la personne possède un user_id
				if ($_FILES['certificat']['size'] < 1048576) {
					//Le fichier respecte bien la limitte de taille
					//On regarde alors si un profil avec ces informations existent déjà. Si non, on regarde quel est le prochain id de disponible pour un profil.
					$requestAlreadyExist = $bdd->prepare('SELECT profil_id from user WHERE prenom=? and nom=? and user_id=?');
					$requestAlreadyExist->execute(array($_POST['prenom'],$_POST['nom'], $_SESSION['user_id']));
					if ($requestAlreadyExist->rowCount() != 0) 
					{
						//Un profil existe déjà avec ces informations!
						exit ("Un profil avec ces mêmes informations existe déjà");
					}
					else {
						//On va regarde quel id de profil est libre

						//On regarde si ce nouvel adhérent c'est déclaré en tant que parent et si oui, si un tel profil n'est pas déjà associé à ce compte
						/*
						$requestProfilParent = $bdd->prepare('SELECT count(profil_id) as nbProfilParent from user WHERE user_id=? and profil_parent=1');
						$requestProfilParent->execute(array($_SESSION['user_id']));
						if (($_POST['isParent'] == 'true') &&($requestProfilParent->fetch()['nbProfilParent'] > 0)) {
							exit ('Un profil parent est déjà associé à ce compte');
						}
						else {*/
							$requestLibreId = $bdd->prepare('SELECT max(profil_id) as maxId from user WHERE user_id=?');
							$requestLibreId->execute(array($_SESSION['user_id']));
							$newId = $requestLibreId->fetch()['maxId']+1;
							//On va alors uplaodé son certificat médical avec un nom unique (donné par le couple user_id, profil_id)
							if(move_uploaded_file($_FILES["certificat"]["tmp_name"], "upload/".$_SESSION['user_id']."_".$newId.".pdf")) {
								//Le fichier a été umplaodé avec succés. Il faut à présent créer ce nouveau profil dans la base de donnée
								/*
								$bdd->query('INSERT INTO user (user_id, profil_id, prenom, nom, birthday, valid_profil, profil_parent, profil_adherent, certificat_medical) VALUES ("'.$_SESSION['user_id'].'","''","''","''","''","''","''","''")')
								*/
								if (!empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['bday']) && !empty($_POST['bmounth']) && !empty($_POST['byear'])) {
									//Le formulaire est complet!
									$addProfil = $bdd->prepare('INSERT INTO user (user_id, profil_id, prenom, nom, birthday, profil_parent, profil_adherent, certificat_medical) VALUES (?,?,?,?,?,?,?,?)');
									$request = $addProfil->execute(array($_SESSION['user_id'], $newId, $_POST["prenom"], $_POST['nom'], $_POST['byear'].'-'.$_POST['bmounth'].'-'.$_POST['bday'] ,(($_POST['isParent'] == 'true')? '1':'0'),(($_POST['isAdherent'] == 'true')? '1':'0'), "upload/".$_SESSION['user_id']."_".$newId.".pdf"));
									//On a joute le profil à la table de connexion
									//On créer un nouveau compte (nouvealle ligne dans bdd)
									$addCompte = $bdd->prepare('INSERT INTO `connexion` (user_id, profil_id,`login`, `pwd`) VALUES (?,?,?,?);');
									$addCompte->execute(array($_SESSION['user_id'],$newId,$login_mail, $pwd));
									//On ajoute aussi le code de validité pour le mail
									$addCodeVerif = $bdd->prepare('INSERT INTO verif_connexion (user_id, profil_id, code) VALUES (?,?,?)');
									$key = randomKey();
									$addCodeVerif->execute(array($_SESSION['user_id'], $newId, $key));

									$sujet = "[Val'Acro] Création de votre compte";
									$message_txt = "Bonjour, \nVous avez demandé la création d'un compte sur Val'Acro. Veuillez cliquer sur le lien suivant pour finaliser votre inscription :\n \nSi vous n'avez pas fait cette demande, ignorez ce mail.";
									$message_html = "<html><head></head><body>
									<p>Bonjour, </p>
									<p>Vous avez demandé la création d'un compte sur Val'Acro. Veuillez cliquer sur le lien suivant pour finaliser votre inscription :</p>
									<p> <a href='localhost/ValAcro/confirmation_creation_compte.php?login=".$login_mail."&key=".$key."'>Confirmer mon compte</a> </p>
									<p>Si vous n'avez pas fait cette demande, ignorez ce mail.</p>
									</body></html>";
									include('send_mail.php');
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
						/*}*/
					}
				}
			} 	else {
			//Ce cas est déjà pris en compte dans la page html (permet d'éviter un échange entre le client et le serveur qui serait inutile)
			exit ("Vous devez fournir un certificat médical");
			}
		}
		else if (($request->fetch()['compte_valid'] == 0 /*il y a un compte avec ce login mais il n'est pas valide*/)){
			if (!empty($_FILES["certificat"])){
				//On est certains que le nom, le prenom et la date sont données car on a déclarés les inputs en required! De plus, on sait que la personne possède un user_id
				if ($_FILES['certificat']['size'] < 1048576) {
					//Le fichier respecte bien la limitte de taille
					//On regarde alors si un profil avec ces informations existent déjà. Si non, on regarde quel est le prochain id de disponible pour un profil.
					$requestAlreadyExist = $bdd->prepare('SELECT profil_id from user WHERE prenom=? and nom=? and user_id=?');
					$requestAlreadyExist->execute(array($_POST['prenom'],$_POST['nom'], $_SESSION['user_id']));
					if ($requestAlreadyExist->rowCount() != 0) 
					{
						//Un profil existe déjà avec ces informations!
						exit ("Un profil avec ces mêmes informations existe déjà");
					}
					else {
						//On va regarde quel id de profil est libre

						//On regarde si ce nouvel adhérent c'est déclaré en tant que parent et si oui, si un tel profil n'est pas déjà associé à ce compte 
						/*
						$requestProfilParent = $bdd->prepare('SELECT count(profil_id) as nbProfilParent from user WHERE user_id=? and profil_parent=1');
						$requestProfilParent->execute(array($_SESSION['user_id']));
						if (($_POST['isParent'] == 'true') &&($requestProfilParent->fetch()['nbProfilParent'] > 0)) {
							exit ('Un profil parent est déjà associé à ce compte');
						}
						else {*/
							$requestLibreId = $bdd->prepare('SELECT max(profil_id) as maxId from user WHERE user_id=?');
							$requestLibreId->execute(array($_SESSION['user_id']));
							$newId = $requestLibreId->fetch()['maxId']+1;
							//On va alors uplaodé son certificat médical avec un nom unique (donné par le couple user_id, profil_id)
							if(move_uploaded_file($_FILES["certificat"]["tmp_name"], "upload/".$_SESSION['user_id']."_".$newId.".pdf")) {
								//Le fichier a été umplaodé avec succés. Il faut à présent créer ce nouveau profil dans la base de donnée
								/*
								$bdd->query('INSERT INTO user (user_id, profil_id, prenom, nom, birthday, valid_profil, profil_parent, profil_adherent, certificat_medical) VALUES ("'.$_SESSION['user_id'].'","''","''","''","''","''","''","''")')
								*/
								if (!empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['bday']) && !empty($_POST['bmounth']) && !empty($_POST['byear'])) {
									//Le formulaire est complet!
									$addProfil = $bdd->prepare('INSERT INTO user (user_id, profil_id, prenom, nom, birthday, profil_parent, profil_adherent, certificat_medical) VALUES (?,?,?,?,?,?,?,?)');
									$request = $addProfil->execute(array($_SESSION['user_id'], $newId, $_POST["prenom"], $_POST['nom'], $_POST['byear'].'-'.$_POST['bmounth'].'-'.$_POST['bday'],(($_POST['isParent'] == 'true')? '1':'0'),(($_POST['isAdherent'] == 'true')? '1':'0'), "upload/".$_SESSION['user_id']."_".$newId.".pdf"));
									//On s'occupe de la connexion
									//on update le compte associé
									//On ajoute un nouveau code de vérification pour le mail
									
									$changePWD = $bdd->prepare('UPDATE connexion SET pwd=? WHERE login=? and user_id=? and profil_id=?');
									$changePWD->execute(array($pwd,$login_mail, $_SESSION['user_id'], $newId));
									$key = randomKey();
									$addCodeVerif = $bdd->prepare('UPDATE verif_connexion SET code=? WHERE user_id =? and profil_id=?');
									$addCodeVerif->execute(array($key,$_SESSION['user_id'], $newId));

									$sujet = "[Val'Acro] Création de votre compte";
									$message_txt = "Bonjour, \nVous avez demandé la création d'un compte sur Val'Acro. Veuillez cliquer sur le lien suivant pour finaliser votre inscription :\n \nSi vous n'avez pas fait cette demande, ignorez ce mail.";
									$message_html = "<html><head></head><body>
									<p>Bonjour, </p>
									<p>Vous avez demandé la création d'un compte sur Val'Acro. Veuillez cliquer sur le lien suivant pour finaliser votre inscription :</p>
									<p>
									<a href='localhost/ValAcro/confirmation_creation_compte.php?login=".$login_mail."&key=".$key."'>Confirmer mon compte</a>
									</p>
									<p>Si vous n'avez pas fait cette demande, ignorez ce mail.</p>
									</body></html>";
									include('send_mail.php');
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
						/*}*/
					}
				}	
			} else {
			//Ce cas est déjà pris en compte dans la page html (permet d'éviter un échange entre le client et le serveur qui serait inutile)
				exit ("Vous devez fournir un certificat médical");
			}
		} else {
			//login déjà utilisé pour un compte valide
			exit ("Login déjà utilisé");
			//On le redirige vers la page d'accueil
		}
	} else {
		exit ("Mot de passe invalide");
	}
} else if ((empty($_POST['login'])) && (empty($_POST['pwd'])) && (empty($_POST['pwd_confirm'])) && !($_POST['isParent'] == 'true')) {
	//On ajoute un profil sans login et mot de passe Mais il ne s'agit en aucun cas d'un profil parent
			if (!empty($_FILES["certificat"])){
				//On est certains que le nom, le prenom et la date sont données car on a déclarés les inputs en required! De plus, on sait que la personne possède un user_id
				if ($_FILES['certificat']['size'] < 1048576) {
					//Le fichier respecte bien la limitte de taille
					//On regarde alors si un profil avec ces informations existent déjà. Si non, on regarde quel est le prochain id de disponible pour un profil.
					$requestAlreadyExist = $bdd->prepare('SELECT profil_id from user WHERE prenom=? and nom=? and user_id=?');
					$requestAlreadyExist->execute(array($_POST['prenom'],$_POST['nom'], $_SESSION['user_id']));
					if ($requestAlreadyExist->rowCount() != 0) 
					{
						//Un profil existe déjà avec ces informations!
						exit ("Un profil avec ces mêmes informations existe déjà");
					}
					else {
						//On va regarde quel id de profil est libre

						//On regarde si ce nouvel adhérent c'est déclaré en tant que parent et si oui, si un tel profil n'est pas déjà associé à ce compte
						/*
						$requestProfilParent = $bdd->prepare('SELECT count(profil_id) as nbProfilParent from user WHERE user_id=? and profil_parent=1');
						$requestProfilParent->execute(array($_SESSION['user_id']));
						if (($_POST['isParent'] == 'true') &&($requestProfilParent->fetch()['nbProfilParent'] > 0)) {
							exit ('Un profil parent est déjà associé à ce compte');
						}
						else {*/
							$requestLibreId = $bdd->prepare('SELECT max(profil_id) as maxId from user WHERE user_id=?');
							$requestLibreId->execute(array($_SESSION['user_id']));
							$newId = $requestLibreId->fetch()['maxId']+1;
							//On va alors uplaodé son certificat médical avec un nom unique (donné par le couple user_id, profil_id)
							if(move_uploaded_file($_FILES["certificat"]["tmp_name"], "upload/".$_SESSION['user_id']."_".$newId.".pdf")) {
								//Le fichier a été umplaodé avec succés. Il faut à présent créer ce nouveau profil dans la base de donnée
								/*
								$bdd->query('INSERT INTO user (user_id, profil_id, prenom, nom, birthday, valid_profil, profil_parent, profil_adherent, certificat_medical) VALUES ("'.$_SESSION['user_id'].'","''","''","''","''","''","''","''")')
								*/
								if (!empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['bday']) && !empty($_POST['bmounth']) && !empty($_POST['byear'])) {
									//Le formulaire est complet!
									$addProfil = $bdd->prepare('INSERT INTO user (user_id, profil_id, prenom, nom, birthday, profil_parent, profil_adherent, certificat_medical) VALUES (?,?,?,?,?,?,?,?)');
									$request = $addProfil->execute(array($_SESSION['user_id'], $newId, $_POST["prenom"], $_POST['nom'], $_POST['byear'].'-'.$_POST['bmounth'].'-'.$_POST['bday'] ,(($_POST['isParent'] == 'true')? '1':'0'),(($_POST['isAdherent'] == 'true')? '1':'0'), "upload/".$_SESSION['user_id']."_".$newId.".pdf"));
									//On a joute le profil à la table de connexion en tant que compte valid car e-mail pas besoin d'^tre validé
									//On créer un nouveau compte (nouvealle ligne dans bdd)
									$addCompte = $bdd->prepare('INSERT INTO `connexion` (user_id, profil_id,`login`, `pwd`, compte_valid) VALUES (?,?,?,?,1);');
									$addCompte->execute(array($_SESSION['user_id'],$newId,null, null));
									
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
						/*}*/
					}
				}
			} 	else {
			//Ce cas est déjà pris en compte dans la page html (permet d'éviter un échange entre le client et le serveur qui serait inutile)
			exit ("Vous devez fournir un certificat médical");
			}

} else {
	exit ("Identifiant/Login non valide");
}
?>