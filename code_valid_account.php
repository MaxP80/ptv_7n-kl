<?php
header('Access-Control-Allow-Origin: *');
	include('DataBase.php');
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

	if (isset($_POST['new_email']) && isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
		//Dans ce cas, les champs ont été remplis
		$login_mail = $_POST['new_email'];
		$pwd = $_POST['new_password'];
		$pwd2 = $_POST['confirm_password'];

		if ($pwd === $pwd2) {
			//Le mot de passe a été saisi deux fois de la même façon. On envoie le mail pour finaliser l'inscription
			$request = $bdd->prepare('SELECT login, compte_valid from connexion WHERE login=?');
			$request->execute(array($login_mail));
			//On regarde si le mail n'est pas utilisé pour un compte valide!
			if ($request->rowCount() == 0 /*aucun compte avec ce login*/) { 
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
				echo "Success";
			}
			else if (($request->rowCount()==1) && ($request->fetch()['compte_valid'] == 0 /*il y a un compte avec ce login mais il n'est pas valide*/)){
				//on update le compte associé
				//On ajoute un nouveau code de vérification pour le mail
/*
				$changePWD = $bdd->prepare('UPDATE connexion SET pwd=? WHERE login=?');
				$changePWD->execute(array($pwd,$login_mail));
				$key = randomKey();
				$addCodeVerif = $bdd->prepare('UPDATE verif_connexion SET code=? WHERE user_id =(SELECT user_id from connexion WHERE login=? and pwd=?) and profil_id=(SELECT user_id from connexion WHERE login=? and pwd=?)');
				$addCodeVerif->execute(array($key,$login_mail, $pwd,$login_mail, $pwd));
*/
				//On se ramène au cas précédent en enlevant le login du compte non valide (connexion) et supprimant la ligne permettant de vérifier le compte (verif_connexion)
				$changePWD = $bdd->prepare('UPDATE connexion SET pwd=?, login=? WHERE login=?');
				$changePWD->execute(array(null,null,$login_mail));

				//On refait le cas précédent
				$addCompte = $bdd->prepare('INSERT INTO `connexion` (`login`, `pwd`) VALUES (?,?);');
				$addCompte->execute(array($login_mail, $pwd));
				//On ajoute aussi le code de validité pour le mail
				$addCodeVerif = $bdd->prepare('INSERT INTO verif_connexion (user_id, profil_id, code) VALUES ((SELECT user_id from connexion WHERE login=? and pwd=?),(SELECT profil_id from connexion WHERE login=? and pwd=?),?)');
				$key = randomKey();
				$addCodeVerif->execute(array($login_mail, $pwd,$login_mail, $pwd,$key));

				/*Envoie du mail*/
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
			}
			else {
				//login déjà utilisé pour un compte valide
				echo "Failed_login";
				//On le redirige vers la page d'accueil
			}
		}
		else {
			//pwd et confirmation différente
			//login déjà utilisé pour un compte valide
			echo "Failed_password";
		}
	}
	else{
		//Champs non remplis
		//login déjà utilisé pour un compte valide
		echo "Failed";
	}
?>