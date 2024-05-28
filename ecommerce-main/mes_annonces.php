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

// Récupération des annonces de l'utilisateur connecté
$utilisateur_id = $_SESSION['id_utilisateur'];
$stmt = $pdo->prepare('SELECT * FROM annonces WHERE utilisateur_id = :id_utilisateur');
$stmt->bindParam(':id_utilisateur', $utilisateur_id);
$stmt->execute();
$annonces = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Affichage des annonces dans des cases
echo '<div class="annonces">';
foreach ($annonces as $annonce) {
    echo '<div class="annonce">';
    echo '<h3>' . $annonce['titre'] . '</h3>';
    echo '<p>' . $annonce['description'] . '</p>';
    echo '<p>Catégorie : ' . $annonce['categorie'] . '</p>';
    echo '<p>Prix : ' . $annonce['prix'] . '€</p>';
    echo '<p>Ville : ' . $annonce['ville'] . '</p>';
    echo '<a href="./includes/modifier_annonce.php?id_annonce=' . $annonce['id_annonces'] . '" class="modifier-bouton">Modifier annonce</a>';
    echo '<a href="./includes/traitement_suppression.php?id_annonce=' . $annonce['id_annonces'] . '" class="modifier-bouton">Supprimer annonce</a>';
    echo '</div>';
}
echo '</div>';
?>