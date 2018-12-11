<?php
session_start();
include("DataBase.php");

$profil_id = $_SESSION['profil_id'];
if (isset($_POST['profil_id'])) {
	$profil_id = $_POST['profil_id'];
}
//La variable profil_id n'est aps obligatoire
if (isset($_POST['id_evenement'])) {
	//Il y a deux cas de figure, soit il y participe déjà et on le supprime de la table
	//Soit il n'y participe pas et on ne fait rien
	$request = $bdd->prepare ('SELECT * from participants WHERE id_evenement_participant=? and user_id=? and profil_id=?');
	$request->execute(array($_POST['id_evenement'],$_SESSION['user_id'],$profil_id));
	if ($request->rowCount() == 1) {
		//Il y participe pour l'instant
		$update = $bdd->prepare('DELETE FROM participants WHERE id_evenement_participant=? and user_id=? and profil_id=?');
		$update->execute(array($_POST["id_evenement"], $_SESSION['user_id'] ,$profil_id));
		echo 'Success';
	} else {
		echo 'Failed';
	}//Sinon on ne fait rien
} else {
echo 'Failed isset';}//Sinon, on ne fait rien
?>