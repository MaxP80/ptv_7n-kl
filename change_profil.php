<?php
session_start();
include('DataBase.php');
if (($_SESSION['isConnected'] == true) && (isset($_POST['profil_id']))) {
	$request = $bdd->prepare('SELECT profil_id, profil_parent, valid_profil from user WHERE user_id=? and profil_id=?');
	$request->execute(array($_SESSION['user_id'],$_POST['profil_id']));
	if ($request->rowCount() == 1){
		//Tout va bien
		$data = $request->fetch();
		$_SESSION['profil_id'] = $data['profil_id'];
		$_SESSION['valid_profil'] = $data['valid_profil'];
		$_SESSION['profil_parent'] = $data['profil_parent'];
		echo 'Success';
	}
	else {
		echo 'Failed';
	}
}
else {
	echo 'Failed';
}
?>