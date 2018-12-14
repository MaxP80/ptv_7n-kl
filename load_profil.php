<?php
header('Access-Control-Allow-Origin: *');
//On sait que l'utilisateur est connecté
session_start();
include('DataBase.php');
include("isConnectedMobilApp.php");
$request = $bdd->prepare('SELECT prenom, nom, photo, profil_id from user WHERE user_id=?');
$request->execute(array($_SESSION['user_id']));
$res = ($request->rowCount() > 0)? $request->fetchAll() : null;
echo json_encode($res);
?>