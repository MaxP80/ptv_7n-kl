<?php
header('Access-Control-Allow-Origin: *');
if (isset($_GET['login']) && isset($_GET['key'])) {
	$login = $_GET['login'];
	$key = $_GET['key'];
}
else {
	header('location: index.php');
	exit();
}

include('DataBase.php');
$request = $bdd->prepare('SELECT c.user_id, c.login, c.compte_valid , v.code from connexion as c, verif_connexion as v WHERE c.user_id=v.user_id and c.compte_valid=0 and c.login=? ');
$request->execute(array($login));
if ($request->rowCount() == 1) {
	//Dans ce cas, on doit valider la création du compte
	$update = $bdd->prepare('UPDATE connexion SET compte_valid = 1 WHERE login=?');
	$update->execute(array($login));
	$delete = $bdd->prepare('DELETE FROM verif_connexion WHERE verif_connexion.user_id = (SELECT user_id from connexion WHERE login = ?)');
	$delete->execute(array($login));
	echo '<body onLoad="alert(\'Votre compte a été validé avec succés\')">';
			//On le redirige vers la page d'accueil
	echo '<meta http-equiv="refresh" content="0;URL=index.php">';
}
else {
	//Le compte associé au login n'est pas en cours de validification ou n'existe pas!
			echo '<body onLoad="alert(\'Echec de validation du compte\')">';
			//On le redirige vers la page d'accueil
			echo '<meta http-equiv="refresh" content="0;URL=index.php">';

}
//On retourne vers la page de connexion
//header('location: index.php');
//exit();
?>