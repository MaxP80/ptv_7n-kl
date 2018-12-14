<?php 
header('Access-Control-Allow-Origin: *'); 
include('DataBase.php');
//On teste si l'utilisatur a bien saisit un login et un mot de passe
//

if (isset($_POST['login']) && isset($_POST['password'])) {
	//On doit vérifier la validité des informations saisies
	$requestLogin = $bdd->prepare('SELECT user_id, profil_id,compte_valid, valid_profil  from connexion WHERE login=? and pwd=? and compte_valid=1');
	$requestLogin->execute(array($_POST['login'], $_POST['password']));
	if($requestLogin->rowCount() == 1) {
		$dataUser = $requestLogin->fetch();
		if ($dataUser['compte_valid'] == 1) {
			//on démarre la session
			session_start();
			//On enregistre les paramètres de la session
			$data_user = $requestLogin->fetch();
			$_SESSION['isConnected'] = true;
			$_SESSION['user_id']= $dataUser['user_id'];
			$_SESSION['login']= $_POST['login'];
			$_SESSION['profil_id']= $dataUser['profil_id']; //le profil 0 correspond au profil "parent", c'est-à-dire qu'on voit tous les profils associé à l'utilisateur
			//on redirige vers la page d'acceuil
			$_SESSION['valid_profil'] = $dataUser['valid_profil'];
			$_SESSION['last_activity'] = time();
			//On évite de recharger sur plusieurs pages différentes les mêmes informations! Ces deux variables seront initialisés lorsque l'utilisateur sélectionnera sont profil...
			if ($dataUser['profil_id'] != 0){
				//On regarde si le profil a été validé par l'admin!
				if ($dataUser['valid_profil'] == 1){
					//Profil_validé
					echo ("home.html");
				} else {
					//Profil en attente
					echo "no_valid_profil.html";
				}
			} else {
				//Le profil n'a pas été initialisé, on le renvoie vers cette page!
				echo "set_profil.html";
			}
		} else {
			echo "Compte Non Valid";
		}
	}
	else {
	// Le visiteur n'a pas été reconnu comme étant membre de notre site. On utilise alors un petit javascript lui signalant ce fait
		echo "Failed";
	}
}
else {
	//Les variables ne sont pas déclarées
	echo "Failed";
}
?>