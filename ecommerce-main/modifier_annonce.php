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

// Récupérer l'annonce à modifier à partir de son ID
$stmt = $pdo->prepare('SELECT * FROM annonces WHERE id_annonces = :id_annonce');
$stmt->bindParam(':id_annonce', $id_annonce);
$stmt->execute();
$annonce = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$annonce) {
    die('Annonce introuvable ou vous n\'êtes pas autorisé à la modifier.');
}

// Afficher un formulaire de modification pour l'annonce
echo '<h1 style="color: blue; font-family: Arial;">Modification de l\'annonce ' . $annonce['titre'] . '</h1>';
echo '<form action="traitement_modification.php?id_annonce=' . $annonce['id_annonces'] . '" method="post">';
echo '<input type="hidden" name="id_annonce" value="' . $annonce['id_annonces'] . '">';
echo '<label style="color: red; font-family: Arial;">Titre :</label><br>';
echo '<input type="text" name="titre" value="' . $annonce['titre'] . '"><br>';
echo '<label style="color: red; font-family: Arial;">Description :</label><br>';
echo '<textarea name="description">' . $annonce['description'] . '</textarea><br>';
echo '<label style="color: red; font-family: Arial;">Catégorie :</label><br>';
echo '<input type="text" name="categorie" value="' . $annonce['categorie'] . '"><br>';
echo '<label style="color: red; font-family: Arial;">Prix :</label><br>';
echo '<input type="number" name="prix" value="' . $annonce['prix'] . '"><br>';
echo '<label style="color: red; font-family: Arial;">Ville :</label><br>';
echo '<input type="text" name="ville" value="' . $annonce['ville'] . '"><br>';
echo '<input type="submit" value="Modifier" style="background-color: blue; color: white; font-family: Arial;">';
echo '</form>';
?>