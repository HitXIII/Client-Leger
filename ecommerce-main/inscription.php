<?php
    // Paramètres de connexion à la base de données
    $host = 'localhost';
    $dbname = 'projetleboncoin-bdd';
    $username = 'postgres';
    $password = 'root';

    // Chaîne de connexion
    $dsn = "pgsql:host=$host;port=5432;dbname=$dbname";

    // Tentative de connexion à la base de données
    try {
        $pdo = new PDO($dsn, $username, $password);
    } catch (PDOException $e) {
        echo 'Connexion échouée : ' . $e->getMessage();
        exit;
    }

    // Si le formulaire d'inscription a été soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupération des données du formulaire
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $email = $_POST['email'];
        $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);

        // Préparation de la requête SQL
        $stmt = $pdo->prepare('INSERT INTO Utilisateur (nom, prenom, email, mot_de_passe) VALUES (:nom, :prenom, :email, :mot_de_passe)');

        // Liaison des paramètres de la requête avec les données de l'utilisateur
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':mot_de_passe', $mot_de_passe);

        // Exécution de la requête
        if ($stmt->execute()) {
            echo 'Nouvel utilisateur créé : ' . $nom . ' ' . $prenom . ' (' . $email . ')';
        } else {
            echo 'Une erreur est survenue lors de la création de l\'utilisateur.';
        }
    }
?>

<div id="inscription-form" style="display:none;">
    <form method="post" action="./includes/inscription.php">
        <div>
            <label for="nom">Nom</label>
            <input type="text" name="nom" required>
        </div>
        <div>
            <label for="prenom">Prénom</label>
            <input type="text" name="prenom" required>
        </div>
        <div>
            <label for="email">Email</label>
            <input type="email" name="email" required>
        </div>
        <div>
            <label for="mot_de_passe">Mot de passe</label>
            <input type="password" name="mot_de_passe" required>
        </div>
        <button type="submit">S'inscrire</button>
    </form>
</div>
