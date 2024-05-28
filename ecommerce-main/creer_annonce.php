<?php
// Vérification si l'utilisateur est connecté
session_start();
echo "<script>console.log('je suis rentré dans la création d\'annonce');</script>";
if (isset($_SESSION['id_utilisateur'])) {
    echo "<script>console.log('id_utilisateur: ".$_SESSION['id_utilisateur']."');</script>";
}

if (!isset($_SESSION['id_utilisateur'])) {
    header('Location: connexion.php');
    exit();
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification des champs obligatoires
    
    
        // Connexion à la base de données
        // Paramètres de connexion à la base de données
        $host = 'localhost';
        $dbname = 'projetleboncoin-bdd';
        $username = 'postgres';
        $password = 'root';

        // Chaîne de connexion
        $dsn = "pgsql:host=$host;port=5432;dbname=$dbname";

        $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );
    
        try {
            $pdo = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            error_log('Erreur de connexion à la base de données : ' . $e->getMessage());
            die('Erreur de connexion à la base de données : ' . $e->getMessage());
        }

        // Insertion de l'annonce dans la base de données
        $stmt = $pdo->prepare('INSERT INTO annonces (titre, description, categorie, prix, ville, date_publication, utilisateur_id) VALUES (:titre, :description, :categorie, :prix, :ville, NOW(), :utilisateur_id)');
        $stmt->execute(array(
            ':titre' => $_POST['titre'],
            ':description' => $_POST['description'],
            ':categorie' => $_POST['categorie'],
            ':prix' => $_POST['prix'],
            ':ville' => $_POST['ville'],
            ':utilisateur_id' => $_SESSION['id_utilisateur']
        ));
        $annonce_id = $pdo->lastInsertId();


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
