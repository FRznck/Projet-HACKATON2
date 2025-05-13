<?php
session_start();
echo "Le compte ".$_SESSION["email_professionnel"]." a été déconnecté...";
session_destroy();
header("refresh:3;url=connexion-recruteur.php");
?>