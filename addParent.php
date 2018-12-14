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


	if (isset($_POST['login']) && isset($_POST['pwd']) && isset($_POST['pwd_confirm'])) {
		//Dans ce cas, les champs ont été remplis
		$login_mail = $_POST['login'];
		$pwd = $_POST['pwd'];
		$pwd2 = $_POST['pwd_confirm'];

		if ($pwd === $pwd2) {
			//Le mot de passe a été saisi deux fois de la même façon. On envoie le mail pour finaliser l'inscription
			$request = $bdd->prepare('SELECT login, compte_valid from connexion WHERE login=?');
			$request->execute(array($login_mail));
			//On regarde si le mail n'est pas utilisé pour un compte valide!
			if ($request->rowCount() == 0 /*aucun compte avec ce login*/) { 
				//On regarde si un profil avec ces informations existent déjà. Si non, on regarde quel est le prochain id de disponible pour un profil.
				$requestAlreadyExist = $bdd->prepare('SELECT profil_id from user WHERE prenom=? and nom=? and user_id=?');
				$requestAlreadyExist->execute(array($_POST['prenom'],$_POST['nom'], $_SESSION['user_id']));
				if ($requestAlreadyExist->rowCount() != 0) 
				{
					//Un profil existe déjà avec ces informations!
					exit ("Un profil avec ces mêmes informations existe déjà");
				}
				else {
					/*
					//On regarde si un profil parent n'a pas déjà été créé
					$requestProfilParent = $bdd->prepare('SELECT count(profil_id) as nbProfilParent from user WHERE user_id=? and profil_parent=1');
					$requestProfilParent->execute(array($_SESSION['user_id']));
					if ($requestProfilParent->fetch()['nbProfilParent'] > 0) {
						exit ('Un profil parent est déjà associé à ce compte');
					}
					else {*/
					//On va regarde quel id de profil est libre pour ce profil parent!
						$requestLibreId = $bdd->prepare('SELECT max(profil_id) as maxId from user WHERE user_id=?');
						$requestLibreId->execute(array($_SESSION['user_id']));
						$newId = $requestLibreId->fetch()['maxId']+1;
						if (!empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['bday']) && !empty($_POST['bmounth']) && !empty($_POST['byear'])) {
							//Le formulaire est complet!
							$addProfil = $bdd->prepare('INSERT INTO user (user_id, profil_id, prenom, nom, birthday, profil_parent, profil_adherent) VALUES (?,?,?,?,?,?,?)');
							$request = $addProfil->execute(array($_SESSION['user_id'], $newId, $_POST["prenom"], $_POST['nom'], $_POST['byear'].'-'.$_POST['bmounth'].'-'.$_POST['bday'],1,0));
							//On envoie un mail
							//On créer un nouveau compte (nouvealle ligne dans bdd)
							$addCompte = $bdd->prepare('INSERT INTO `connexion` (user_id, profil_id, `login`, `pwd`) VALUES (?,?,?,?);');
							$addCompte->execute(array($_SESSION['user_id'], $newId, $login_mail, $pwd));
							//On ajoute aussi le code de validité pour le mail
							$addCodeVerif = $bdd->prepare('INSERT INTO verif_connexion (user_id, profil_id, code) VALUES (?,?,?)');
							$key = randomKey();
							$addCodeVerif->execute(array($_SESSION['user_id'], $newId,$key));

							$sujet = "[Val'Acro] Création de votre compte";
							$message_txt = "Bonjour, \nVous avez demandé la création d'un compte sur Val'Acro. Veuillez cliquer sur le lien suivant pour finaliser votre inscription :\n \nSi vous n'avez pas fait cette demande, ignorez ce mail.";
							$message_html = "<html><head></head><body>
							<p>Bonjour, </p>
							<p>Vous avez demandé la création d'un compte sur Val'Acro. Veuillez cliquer sur le lien suivant pour finaliser votre inscription :</p>
							<p> <a href='localhost/ValAcro/confirmation_creation_compte.php?login=".$login_mail."&key=".$key."'>Confirmer mon compte</a> </p>
							<p>Si vous n'avez pas fait cette demande, ignorez ce mail.</p>
							</body></html>";
							include('send_mail.php');
							exit("Votre profil a été créé avec succés");
						}
						else {
							exit ("Formulaire incomplet");
						}
					/*}*/
				}
			}
			else if (($request->fetch()['compte_valid'] == 0 /*il y a un compte avec ce login mais il n'est pas valide*/)){
					//On regarde si un profil avec ces informations existent déjà. Si non, on regarde quel est le prochain id de disponible pour un profil.
				$requestAlreadyExist = $bdd->prepare('SELECT profil_id from user WHERE prenom=? and nom=? and user_id=?');
				$requestAlreadyExist->execute(array($_POST['prenom'],$_POST['nom'], $_SESSION['user_id']));
				if ($requestAlreadyExist->rowCount() != 0) 
				{
					//Un profil existe déjà avec ces informations!
					exit ("Un profil avec ces mêmes informations existe déjà");
				}
				else {
					/*
					//On regarde si un profil parent n'a pas déjà été créé
					$requestProfilParent = $bdd->prepare('SELECT count(profil_id) as nbProfilParent from user WHERE user_id=? and profil_parent=1');
					$requestProfilParent->execute(array($_SESSION['user_id']));
					if ($requestProfilParent->fetch()['nbProfilParent'] > 0) {
						exit ('Un profil parent est déjà associé à ce compte');
					}
					else {*/
					//On va regarde quel id de profil est libre pour ce profil parent!
						$requestLibreId = $bdd->prepare('SELECT max(profil_id) as maxId from user WHERE user_id=?');
						$requestLibreId->execute(array($_SESSION['user_id']));
						$newId = $requestLibreId->fetch()['maxId']+1;
						if (!empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['bday']) && !empty($_POST['bmounth']) && !empty($_POST['byear'])) {
							//Le formulaire est complet!
							$addProfil = $bdd->prepare('INSERT INTO user (user_id, profil_id, prenom, nom, birthday, profil_parent, profil_adherent) VALUES (?,?,?,?,?,?,?)');
							$request = $addProfil->execute(array($_SESSION['user_id'], $newId, $_POST["prenom"], $_POST['nom'], $_POST['byear'].'-'.$_POST['bmounth'].'-'.$_POST['bday'],1,0));
							//on update le compte associé
							//On ajoute un nouveau code de vérification pour le mail
							$changePWD = $bdd->prepare('UPDATE connexion SET pwd=? WHERE login=? and user_id=? and profil_id=?');
							$changePWD->execute(array($pwd,$login_mail, $_SESSION['user_id'], $newId));
							$key = randomKey();
							$addCodeVerif = $bdd->prepare('UPDATE verif_connexion SET code=? WHERE user_id =(?,?,?)');
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
							echo "Success";
							exit("Votre profil a été créé avec succés");
						}
						else {
							exit ("Formulaire incomplet");
						}
					/*}*/
				}
			}
			else {
				//login déjà utilisé pour un compte valide
				exit ("Login déjà utilisé");
				//On le redirige vers la page d'accueil
			}
		}
		else {
			//pwd et confirmation différente
			//login déjà utilisé pour un compte valide
			exit ("Mot de passe non conformes");
		}
	}
	else{
		//Champs non remplis
		//login déjà utilisé pour un compte valide
		exit ("Formulaire non valide");
	}
?>