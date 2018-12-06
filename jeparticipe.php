<?php
session_start();
include("DataBase.php");

$profil_id = $_SESSION['profil_id'];
if (isset($_POST['profil_id'])) {
	$profil_id = $_POST['profil_id'];
}
//La variable profil_id n'est aps obligatoire
if (isset($_POST['id_evenement'])) {

	//La première étape est de ragarder si le profil sélectionné peut participer à l'événement
	

	//Il y a deux cas de figure, soit il y participe déjà et on ne fait rien
	//Soit il n'y participe pas encore et on l'ajoute dans la table participants
	$request = $bdd->prepare ('SELECT * from participants WHERE id_evenement_participant=? and user_id=? and profil_id=?');
	$request->execute(array($_POST['id_evenement'],$_SESSION['user_id'],$profil_id));
	if ($request->rowCount() == 0) {
		//Il n'y participe pas pour l'instant
		$update = $bdd->prepare('INSERT INTO participants (id_evenement_participant, user_id, profil_id) VALUES (?,?,?)');
		$update->execute(array($_POST["id_evenement"], $_SESSION['user_id'] ,$profil_id));
		echo 'Success';
	} else {
		echo 'Failed';
	}//Sinon on ne fait rien
} else {
echo 'Failed isset';}//Sinon, on ne fait rien
?>