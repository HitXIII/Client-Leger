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


    // Vérifier si l'ID de l'annonce est présent dans les paramètres de requête
    if (!isset($_GET['id_annonce'])) {
        die('ID de l\'annonce manquant.');
    }

    $id_annonce = $_GET['id_annonce'];

    // Récupérer l'annonce à supprimer à partir de son ID
    $stmt = $pdo->prepare('SELECT * FROM annonces WHERE id_annonces = :id_annonce');
    $stmt->bindParam(':id_annonce', $id_annonce);
    $stmt->execute();
    $annonce = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$annonce) {
        die('Annonce introuvable ou vous n\'êtes pas autorisé à la supprimer.');
    }

    // Supprimer l'annonce de la base de données
    $stmt = $pdo->prepare('DELETE FROM annonces WHERE id_annonces = :id_annonce');
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

?>
