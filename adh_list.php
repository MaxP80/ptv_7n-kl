<?php
include ("DataBase.php");
$request = $bdd->query('SELECT u.user_id, u.profil_id, u.nom, u.prenom, u.photo from user as u, connexion as c WHERE u.profil_adherent=1 and c.compte_valid=1 and c.valid_profil=1 and u.user_id=c.user_id and u.profil_id=c.profil_id');
echo json_encode($request->fetchAll());
?>