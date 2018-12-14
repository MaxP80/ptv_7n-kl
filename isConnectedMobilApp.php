<?php
if (isset($_POST['user_login']) && isset($_POST['user_password'])) {
	//On doit vérifier la validité des informations saisies
	$requestLogin = $bdd->prepare('SELECT user_id, profil_id,compte_valid, valid_profil  from connexion WHERE login=? and pwd=? and compte_valid=1');
	$requestLogin->execute(array($_POST['user_login'], $_POST['user_password']));
	if($requestLogin->rowCount() == 1) {
		$dataUser = $requestLogin->fetch();
		if ($dataUser['compte_valid'] == 1) {
			//On enregistre les paramètres de la session
			$data_user = $requestLogin->fetch();
			$_SESSION['isConnected'] = true;
			$_SESSION['user_id']= $dataUser['user_id'];
			$_SESSION['login']= $_POST['user_login'];
			$_SESSION['profil_id']= $dataUser['profil_id']; //le profil 0 correspond au profil "parent", c'est-à-dire qu'on voit tous les profils associé à l'utilisateur
			//on redirige vers la page d'acceuil
			$_SESSION['valid_profil'] = $dataUser['valid_profil'];
			$_SESSION['last_activity'] = time();
		} 
	}
} else if (isset($_GET['user_login']) && isset($_GET['user_password'])) {
	//On doit vérifier la validité des informations saisies
	$requestLogin = $bdd->prepare('SELECT user_id, profil_id,compte_valid, valid_profil  from connexion WHERE login=? and pwd=? and compte_valid=1');
	$requestLogin->execute(array($_GET['user_login'], $_GET['user_password']));
	if($requestLogin->rowCount() == 1) {
		$dataUser = $requestLogin->fetch();
		if ($dataUser['compte_valid'] == 1) {
			//On enregistre les paramètres de la session
			$data_user = $requestLogin->fetch();
			$_SESSION['isConnected'] = true;
			$_SESSION['user_id']= $dataUser['user_id'];
			$_SESSION['login']= $_GET['user_login'];
			$_SESSION['profil_id']= $dataUser['profil_id']; //le profil 0 correspond au profil "parent", c'est-à-dire qu'on voit tous les profils associé à l'utilisateur
			//on redirige vers la page d'acceuil
			$_SESSION['valid_profil'] = $dataUser['valid_profil'];
			$_SESSION['last_activity'] = time();
		} 
	}
}
?>