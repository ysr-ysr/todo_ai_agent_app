<?php
session_start();
// Supprimer toutes les données de session
$_SESSION = [];
// Détruire la session
session_destroy();
// Rediriger vers la page de connexion
header("Location: login.php");
exit();
