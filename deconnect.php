<?php
header('Access-Control-Allow-Origin: *');
// On démarre la session
session_start();
include("DataBase.php");
include("isConnectedMobilApp.php");
$_SESSION['isConnected'] = false;
// On détruit les variables de notre session
session_unset();
//$_SESSION = array();
// On détruit notre session

session_destroy();
echo 'deconnect'
// On redirige le visiteur vers la page de connection
?>