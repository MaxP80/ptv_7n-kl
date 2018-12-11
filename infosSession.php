<?php
	session_start();
	include("DataBase.php");
	$request = $bdd->query('SELECT is_coach, profil_parent, profil_adherent, in_comite, is_admin from user WHERE user_id="'.$_SESSION['user_id'].'" and profil_id="'.$_SESSION['profil_id'].'"');
	$data = $request->fetch();
	echo ("is_coach"."=".$data["is_coach"].'||'."profil_parent"."=".$data["profil_parent"].'||'."profil_adherent"."=".$data["profil_adherent"].'||'."in_comite"."=".$data["in_comite"].'||'."is_admin"."=".$data["is_admin"]);
?>