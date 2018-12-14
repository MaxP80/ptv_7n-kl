<?php
header('Access-Control-Allow-Origin: *');
//On sait que l'utilisateur est connecté
session_start();
include('DataBase.php');
include("isConnectedMobilApp.php");
$request = $bdd->prepare('SELECT prenom, nom, photo, profil_parent, profil_adherent, in_comite, is_coach,birthday, phone_number, profil_id, certificat_medical from user WHERE user_id=? and profil_id = ?');
$request->execute(array($_SESSION['user_id'], $_SESSION['profil_id']));
$res = (($request->rowCount() > 0)? $request->fetch() : null);

//Si le profil est validé, on va regarder à quelle groupe appartient le profil (s'il est adhérent)
if (($res != null) && ($res['profil_adherent'] == 1) && ($_SESSION['valid_profil'] == 1)) {
	//Dans ce cas, on va récupérer le nom du groupe auqel appartient le profil de la session en cours (profil validé signifie qu'un admin lui a associé un groupe)
	$request = $bdd->prepare('SELECT u.prenom as prenom, u.nom as nom, u.photo as photo, u.profil_parent as profil_parent, u.profil_adherent as profil_adherent, u.in_comite as in_comite, u.is_coach as is_coach,gd.nom as nom_groupe,  DAY(u.birthday) as day, MONTH(u.birthday) as month, YEAR(u.birthday) as year, u.phone_number as phone_number, u.profil_id as profil_id, u.certificat_medical as certificat_medical from user as u, membres_groupe as mg, groupe_description as gd WHERE u.user_id=mg.user_id and u.profil_id=mg.profil_id and mg.id_groupe=gd.id_groupe and u.user_id=? and u.profil_id=?');
	$request->execute(array($_SESSION['user_id'], $_SESSION['profil_id']));
	$res = $request->fetch();
} //Sinon, on ne modifie pas la requête!
echo json_encode($res);
?>