<?php 
$option = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
$bdd = new PDO('mysql:host=localhost;dbname=valacro_database','root','', $option);
?>