<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'projetleboncoin-bdd';
$username = 'postgres';
$password = 'root';

$dsn = "pgsql:host=$host;port=5432;dbname=$dbname";
$options = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    error_log('Erreur de connexion à la base de données : ' . $e->getMessage());
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
// Vérifier si l'ID de l'annonce est présent dans les paramètres de requête
if (!isset($_POST['id_annonce'])) {
    
    die('ID de l\'annonce manquant.');
}

$id_annonce = $_POST['id_annonce'];

// Récupérer l'annonce à modifier à partir de son ID
$stmt = $pdo->prepare('SELECT * FROM annonces WHERE id_annonces = :id_annonce');
$stmt->bindParam(':id_annonce', $id_annonce);
$stmt->execute();
$annonce = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$annonce) {
    die('Annonce introuvable ou vous n\'êtes pas autorisé à la modifier.');
}

// Récupérer les données du formulaire de modification
$titre = $_POST['titre'];
$description = $_POST['description'];
$categorie = $_POST['categorie'];
$prix = $_POST['prix'];
$ville = $_POST['ville'];

// Mettre à jour l'annonce dans la base de données
$stmt = $pdo->prepare('UPDATE annonces SET titre = :titre, description = :description, categorie = :categorie, prix = :prix, ville = :ville WHERE id_annonces = :id_annonce');
$stmt->bindParam(':titre', $titre);
$stmt->bindParam(':description', $description);
$stmt->bindParam(':categorie', $categorie);
$stmt->bindParam(':prix', $prix);
$stmt->bindParam(':ville', $ville);
$stmt->bindParam(':id_annonce', $id_annonce);
$stmt->execute();


//Permet d'avoir la bonne redirection
session_start();
if(isset($_SESSION['id_utilisateur'])){
    $id_utilisateur = $_SESSION['id_utilisateur'];
    $stmt = $pdo->prepare('SELECT prenom, nom FROM utilisateur WHERE id_utilisateur = ?');
    $stmt->execute([$id_utilisateur]);
    $utilisateur = $stmt->fetch();

    // Rediriger vers la page de l'annonce modifiée
    header('Location: ../index.php?message=Bienvenue%2C+' . $utilisateur['prenom'] . '+' . $utilisateur['nom'] . '+%21');
    exit;
} else {
    // Si l'utilisateur n'est pas connecté, rediriger vers la page de connexion
    header('Location: connexion.php');
    exit;
}

}
?> 