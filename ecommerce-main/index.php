<?php
// Début de session
session_start();

if (isset($_SESSION['id_utilisateur'])) {
    echo "<script>console.log('id_utilisateur: " . $_SESSION['id_utilisateur'] . "');</script>";
}

// Vérification si l'utilisateur est connecté
if (isset($_SESSION['id_utilisateur'])) {
    $bouton_deconnexion = '<form method="post" action="./includes/deconnexion.php"><button type="submit">Déconnexion</button></form>';
} else {
    $bouton_deconnexion = '';
}

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
?>


<!DOCTYPE html>
<html>

<head>
    <title>Mon site ecommerce</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css">
</head>

<header>
<nav>
  <ul>
    <li><a href="index.php" style="color: black;">Accueil</a></li>
    <li>
      <?php 
        if(isset($_SESSION['id_utilisateur'])){
          $id_utilisateur = $_SESSION['id_utilisateur'];
          $stmt = $pdo->prepare('SELECT prenom, nom FROM utilisateur WHERE id_utilisateur = ?');
          $stmt->execute([$id_utilisateur]);
          $utilisateur = $stmt->fetch();
  
          // Rediriger vers la page de l'annonce modifiée
          echo '<a href="./index.php?message=Bienvenue%2C+' . $utilisateur['prenom'] . '+' . $utilisateur['nom'] . '+%21" style="color: black;">Mon Compte</a>';
        } else {
          // Si l'utilisateur n'est pas connecté, rediriger vers la page de connexion
          echo '<a href="./connexion.php" style="color: black;">Mon Compte</a>';
        }
      ?>
    </li>
  </ul>
</nav>



</header>

<body>
    <h1>Mon site web</h1>
    <div id="buttons">
        <?php
        if (!isset($_SESSION['id_utilisateur'])) {
            echo '<button id="inscription">Inscription</button>
                  <button id="connexion">Connexion</button>';
        }
        echo $bouton_deconnexion;
        ?>
    </div>

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

    <div id="connexion-form" style="display:none;">
        <form method="post" action="./includes/connexion.php">
            <div>
                <label for="email">Email</label>
                <input type="text" name="email" required>
            </div>
            <div>
                <label for="mot_de_passe">Mot de passe : </label>
                <input type="password" name="mot_de_passe" required>
            </div>
            <button type="submit">Se connecter</button>
        </form>
    </div>

    <?php
    if (isset($_GET['message'])) {
        $message = $_GET['message'];
        $alert_class = 'alert alert-success';

        if (isset($_SESSION['id_utilisateur'])) {
            $alert_class = 'alert alert-success';
        } else {
            $alert_class = 'alert alert-danger';
        }
        ?>
        <div class="<?php echo $alert_class; ?>"><?php echo $message; ?></div>

        <?php
        if (isset($_SESSION['id_utilisateur'])) {
            ?>
            <button id="btn-create" onclick="toggleForm()">Créer une annonce</button>
            <form id="create-form" method="post" action="./includes/creer_annonce.php" enctype="multipart/form-data"
                style="display: none;">
                <label for="titre">Titre de l'annonce*</label>
                <input type="text" id="titre" name="titre" required><br><br>
                <label for="prix">Prix*</label>
                <input type="number" id="prix" name="prix" required><br><br>

                <label for="description">Description*</label>
                <textarea id="description" name="description" required></textarea><br><br>

                <!-- <label for="photo">Photo*</label>
                <input type="file" id="photo" name="photo" accept="image/*" required><br><br> -->

                <label for="categorie">Catégorie</label>
                <select id="categorie" name="categorie">
                    <option value="immobilier">Immobilier</option>
                    <option value="automobile">Automobile</option>
                    <option value="informatique">Informatique</option>
                    <option value="meuble">Meuble</option>
                    <option value="divers">Divers</option>
                </select><br><br>

                <label for="ville">Ville</label>
                <input type="text" id="ville" name="ville"><br><br>

                <input type="submit" value="Créer l'annonce">
            </form>
            <script>
                function toggleForm() {
                    var form = document.getElementById('create-form');
                    var button = document.getElementById('btn-create');
                    if (form.style.display == 'none') {
                        form.style.display = 'block';
                        button.textContent = 'Annuler';
                    } else {
                        form.style.display = 'none';
                        button.textContent = 'Créer une annonce';
                    }
                }
            </script>

            <h2>Mes Annonces<h2>

                    <?php include('./includes/mes_annonces.php'); ?>


                <?php
        }
    }
    ?>



            <script src="./js/index.js"></script>
</body>

</html>