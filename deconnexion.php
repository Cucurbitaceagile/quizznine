<?php
session_start();

// Déconnexion de l'utilisateur
if(isset($_SESSION['username'])) {
    unset($_SESSION['username']);
}

// Redirection vers la page d'accueil ou une autre page après la déconnexion
header("Location: authentification.php"); // Assurez-vous de rediriger vers la bonne page
exit;
?>