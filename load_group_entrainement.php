<?php
	include ("DataBase.php");
	$res = null;
	if (isset($_POST['id'])){
		$request = $bdd->query('SELECT day, heure_debut, heure_fin from entrainement WHERE id_groupe_entrainement= "'.$_POST['id'].'"');
		$res = $request->fetchAll();
	}
	echo json_encode($res);
?>