<?php
    // Début de session
    session_start();

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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupération des données du formulaire
        $email = $_POST['email'];
        $mot_de_passe = $_POST['mot_de_passe'];

        // Recherche de l'utilisateur en fonction de son email
        $sql = "SELECT * FROM Utilisateur WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();
    
        // Vérification du mot de passe
        if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
           // Authentification réussie, création de la session
            $_SESSION['id_utilisateur'] = $user['id_utilisateur'];
            // Redirection vers la page d'accueil avec le message de bienvenue
            $message = "Bienvenue, " . $user['prenom'] . " " . $user['nom'] . " !";
            header('Location: ../index.php?message=' . urlencode($message));
            exit;
        } else {
            // Authentification échouée, affichage d'un message d'erreur
            $message = "Email ou mot de passe incorrect !";
            header('Location: ../index.php?message=' . urlencode($message));
            exit;
        }
    }
?>
