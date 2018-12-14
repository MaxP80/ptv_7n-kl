<?php
	include("DataBase.php");
	$res = null;
	if (isset($_POST['id'])){
		$trameRequest = ('SELECT id_groupe, nom from groupe_description WHERE id_groupe = "'.$_POST['id'].'"');
		$trameRequest = ('SELECT myTable.id_groupe as groupID, myTable.nom as groupeName, GROUP_CONCAT(OtherTable.identity SEPARATOR "||") as identity from ('.$trameRequest.') as myTable LEFT JOIN (SELECT CONCAT(nom, " ", prenom) as identity, coachs_groupe.id_groupe from user, coachs_groupe WHERE user.user_id = coachs_groupe.coach_user_id and user.profil_id = coachs_groupe.coach_profil_id) as OtherTable ON myTable.id_groupe = OtherTable.id_groupe GROUP BY myTable.id_groupe ');
		//echo ($trameRequest);
		$request = $bdd->query($trameRequest);
		$res= $request->fetch(); //il n'y a qu'une seule ligne en réponse
	}
	echo json_encode($res);
?>