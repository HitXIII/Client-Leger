<?php
session_start();
include_once("pages/functions.php");
require("pages/db.php");

if (!$db_connec) {
    die("Échec de la connexion : " . mysqli_connect_error());
}

if (isset($_SESSION['login']) && isset($_SESSION['connexion_id'])) {
    $connexion_id = $_SESSION['connexion_id'];

    // Mise à jour de la déconnexion
    $req = "UPDATE connexions SET datefin = NOW() WHERE id = ?";
    $stmt = $db_connec->prepare($req);
    $stmt->bind_param('i', $connexion_id);
    $stmt->execute();
    $stmt->close();
}

// Détruire la session
session_destroy();

// Rediriger vers la page de connexion
header('Location: pages/login.php');
exit();
?>
