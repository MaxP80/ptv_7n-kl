<?php
// On démarre la session
session_start();
$_SESSION['isConnected'] = false;
// On détruit les variables de notre session
session_unset();
//$_SESSION = array();
// On détruit notre session

session_destroy();
echo 'deconnect'
// On redirige le visiteur vers la page de connection
?>