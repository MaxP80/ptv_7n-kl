<?php
	header('Access-Control-Allow-Origin: *');
	session_start();
	include("DataBase.php");
	include("isConnectedMobilApp.php");
	require ('PHPMailer/PHPMailerAutoload.php');
	require ('connectToMail.php');

	if (isset($_POST['login']) && isset($_POST['pwd'])){
		$request = $bdd->query('SELECT * from connexion WHERE login="'.$_POST['login'].'" and pwd="'.$_POST['pwd'].'"');
		if ($request->rowCount() == 1){
			$uid_pid = $request->fetch();
			$statut_profil = $bdd->query('SELECT COUNT(profil_id) as nbProfils from user WHERE profil_id="'.$uid_pid['profil_id'].'" and user_id="'.$uid_pid['user_id'].'"');
			$nbProfil = $statut_profil->fetch()['nbProfils'];
			if (($nbProfil == 1) && ($_SESSION['user_id'] != $uid_pid['user_id'])){
				$login_mail = $_POST['login'];
				$requestDataDemandeur = $bdd->query('SELECT prenom, nom from user WHERE profil_id="'.$_SESSION['profil_id'].'" and user_id="'.$_SESSION['user_id'].'"');
				$dataDemander = $requestDataDemandeur->fetch();
				$sujet = "[Val'Acro] Demande de liaison de votre compte";
				$message_txt = "Bonjour, \nUne demande de votre compte a été faite par ".$dataDemander['prenom']." ".$dataDemander['nom'].". Veuillez cliquer sur le lien suivant pour finaliser la procèdure :\n localhost/JSValAcro/confirmLinkProfil.php?login=".$_POST['login']."&uid=".$_SESSION['user_id']."\nSi vous n'avez pas fait cette demande, ignorez ce mail.";
				$message_html = "<html><head></head><body>
				<p>Bonjour, </p>
				<p>Une demande de votre compte a été faite par ".$dataDemander['prenom']." ".$dataDemander['nom'].". Veuillez cliquer sur le lien suivant pour finaliser la procèdure :</p>
				<p> <a href='localhost/JSValAcro/confirmLinkProfil.php?login=".$_POST['login']."&uid=".$_SESSION['user_id']."&pid=".$_SESSION['profil_id']."'>Lier mon compte à ce compte</a> </p>
				<p>Si vous n'avez pas fait cette demande, ignorez ce mail. Dans le cas, où les demandes persistent, veuillez contacter l'administrateur.</p>
				</body></html>";
				include('send_mail.php');

				exit("Une demande de lien à ce profil a été envoyé");
			} else {
				//Le profil est déjà lié à d'autres profils ou est déjà lié à celui-ci. Sachant que si deux profils sont liés, il y a au moins l'un des deux qui est parent! (Par contre on peut lier n'importe quel type de profil, enfant ou parent)
				exit ("Impossible de lier ce profil à votre compte");
			}
		} else {
			exit("Profil non trouvé");
		}
	} else {
		exit("Formulaire incomplet");
	}
?>