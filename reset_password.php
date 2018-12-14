<?php 
header('Access-Control-Allow-Origin: *');
//Connection à la base de donnée
include('DataBase.php');
require 'PHPMailer/PHPMailerAutoload.php';
//Données pour se connecter au compte mail
require 'connectToMail.php';
$resultat = ""; //stocke le résultat de l'opération!
if (isset($_POST['reset_email'])) {
	$login_mail = $_POST["reset_email"]; //adreese mail du destinataire
	if ($bdd->query('SELECT * from connexion WHERE compte_valid=1 and login="'.$login_mail.'"')->rowCount() == 1) {
		//On regarde si le compte est activé ou non
		//Déclaration du nouveau mot de passe
		function randomPassword() {
		    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		    $pass = array(); //remember to declare $pass as an array
		    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		    for ($i = 0; $i < 8; $i++) {
		        $n = rand(0, $alphaLength);
		        $pass[] = $alphabet[$n];
		    }
		    return implode($pass); //turn the array into a string thanks to implode function
		}
		$newPWD = randomPassword();

		//Déclaration du message au format texte et au format html (selon ce que les webmails supportent)
		$message_txt = 'Bonjour,\nVotre mot de passe a été réinitialisé.\n Votre nouveau mot de passe est : "'.$newPWD.'"\nCe mot de passe est généré automatiquement, veuillez ne pas répondre.';
		$message_html ='<html><head></head><body><p>Bonjour, </p><p>Votre mot de passe a été réinitialisé.</p><p>Votre nouveau mot de passe est : <b>"'.$newPWD.'"</b></p><p>Ce mot de passe est généré <b>automatiquement</b>, veuillez <b>ne pas répondre</b>.</p></body></html>';
		//Sujet
		$sujet = "[Val'Acro] Réinitialisation du mot de passe";
		//envoie du mail
		include('send_mail.php');

		//On enregistre le nouveau mot de passe dans la bdd
		$updatePWD = $bdd->prepare('UPDATE connexion SET pwd=? WHERE login=?');
		$updatePWD->execute(array($newPWD, $login_mail));
		$resultat="Success";
	}
	else {
		$resultat="Failed";
	}
echo ($resultat);
}
?>