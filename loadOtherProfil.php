<?php
//On sait que l'utilisateur est connecté
session_start();
include('DataBase.php');
$request = $bdd->prepare('SELECT prenom, nom, photo, profil_id from user WHERE user_id=? and profil_id <> ? and profil_parent<>1');
$request->execute(array($_SESSION['user_id'], $_SESSION['profil_id']));
$res = ($request->rowCount() > 0)? $request->fetchAll() : null;
echo json_encode($res);
?>