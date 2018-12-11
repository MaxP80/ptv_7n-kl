<?php 
	session_start();
	//On sait à ce moment, que l'utilisateur est bien connecté§
	if ($_SESSION['profil_id'] != 0) {
		echo "Success";
	}
	else
		echo "Failed";
?>