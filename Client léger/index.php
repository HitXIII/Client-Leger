<?php
require_once("pages/db.php");
include("pages/functions.php");
session_start();
$id_session = session_id();

if (!$db_connec) {
    die("Connection failed: " . mysqli_connect_error());
}

// Traitement du formulaire POST pour ajouter au panier
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_Coque'] )) {
    if (!isset($_SESSION['id_customer'])) {
        die("Vous devez être connecté pour ajouter un article au panier.");
    }

    $id_Coque = $_POST['id_Coque'];
    $id_customer = $_SESSION['id_customer'];

    // Vérifier si l'article existe déjà dans le panier
    $query = $db_connec->prepare("SELECT * FROM cart WHERE id_Coque = ? AND id_customer = ?");
    if ($query === false) {
        die('Erreur de préparation de la requête : ' . $db_connec->error);
    }
    $query->bind_param("ii", $id_Coque, $id_customer);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        // Si l'article existe déjà, mettre à jour la quantité
        $query = $db_connec->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id_Coque = ? AND id_customer = ?");
        if ($query === false) {
            die('Erreur de préparation de la requête : ' . $db_connec->error);
        }
        $query->bind_param("ii", $id_Coque, $id_customer);
    } else {
        // Sinon, insérer un nouvel enregistrement
        $query = $db_connec->prepare("INSERT INTO cart (id_customer, id_Coque, quantity) VALUES (?, ?, 1)");
        if ($query === false) {
            die('Erreur de préparation de la requête : ' . $db_connec->error);
        }
        $query->bind_param("ii", $id_customer, $id_Coque);
    }

    if ($query->execute()) {
        echo "<script>alert('Article ajouté au panier avec succès.');</script>";
    } else {
        echo "Erreur lors de l'ajout de l'article au panier: " . $query->error;
    }
}

// Récupérer les articles disponibles
$requestSqlForGetItems = "SELECT * FROM `coque` JOIN `motif` ON coque.Id_motif = motif.Id_motif JOIN `modele` ON coque.Id_modele = modele.Id_modele";
$result = mysqli_query($db_connec, $requestSqlForGetItems);

if (!$result) {
    die("Query failed: " . mysqli_error($db_connec));
}

// Connexion utilisateur
if (isset($_POST["username"])) {
    if (LoginUser($_POST['username'], $_POST['password'])) {
        $_SESSION['login'] = $_POST['username'];
        $req = "SELECT * FROM customer WHERE login='" . mysqli_real_escape_string($db_connec, $_SESSION['login']) . "'";
        $res = mysqli_query($db_connec, $req);

        if ($res && mysqli_num_rows($res) > 0) {
            $ligne = mysqli_fetch_assoc($res);
            $_SESSION["admin"] = $ligne["admin"];
            $login = $_SESSION['login'];

            $req = "INSERT INTO connexions (login, datedeb) VALUES ('$login', NOW())";
            mysqli_query($db_connec, $req);

            $req = "SELECT MAX(id) as maxi FROM connexions";
            $res = mysqli_query($db_connec, $req);

            if ($res) {
                $ligne = mysqli_fetch_assoc($res);
                $_SESSION["id"] = $ligne["maxi"];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="assets/css/main.css?=1584529395">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Baloo+Bhai">
    <title>Motion Case | Main</title>
</head>
<body>
    <nav>
        <div class="navbar-container">
            <div class="logo">
                <a href="index.php">
                    <h1>Motion Case</h1>
                </a>
            </div>
            <div class="search">
                <form method="post">
                    <i class="fas fa-search"></i>
                    <input name="searchBar" type="text" placeholder="Search...">
                </form>
            </div>
            
            <?php if (isset($_SESSION['login'])) : ?>
                <a href="deconnect.php" class="btn-nav">Déconnexion</a>
                <?php if (isset($_SESSION["admin"]) && $_SESSION["admin"] == 1) : ?>
                <?php endif; ?>
                <a href="#" class="btn-nav"><?php echo $_SESSION["login"]; ?></a>
            <?php else : ?>
                <a href="pages/login.php" class="btn-nav">Connexion</a>
            <?php endif; ?>
            <a href="pages/cart.php" class="btn-nav"><i class="fas fa-shopping-cart"></i></a>
        </div>
    </nav>
    <div class="main-container">
        <?php
        if (isset($_POST["login"])) {
            CreateNewUser($_POST["login"], $_POST["password"], $_POST["email"], $_POST["name"]);
        }

        if (isset($_POST["searchBar"])) {
            $request = mysqli_real_escape_string($db_connec, $_POST["searchBar"]);
            $sql = "SELECT * FROM `coque` JOIN `motif` ON coque.Id_motif = motif.Id_motif JOIN `modele` ON coque.Id_modele = modele.Id_modele WHERE motif.motif LIKE '%$request%'";
            $result = mysqli_query($db_connec, $sql);

            if (!$result || mysqli_num_rows($result) == 0) {
                echo '<div class="titleError">Aucun résultat pour votre recherche</div>';
            }
        }
        

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) { ?>
                <div class="item">
                    <div class="item-img">
                        <a href="pages/item.php?id=<?= $row['Id_Coque'] ?>">
                            <img src="assets/images/items/<?= $row['Id_Coque'] ?>/principal.jpg" alt="Image de <?= htmlspecialchars($row['motif']) ?>">
                        </a>
                    </div>
                    <div class="item-info">
                        <h2><?= htmlspecialchars($row['motif']) ?></h2>
                        <p><?= htmlspecialchars($row['modele']) ?></p>
                        <p>Prix: <?= htmlspecialchars($row['Prix']) ?>€</p>
                    </div>
                    <form method="post" action="pages/cart.php">
                        <input type="hidden" name="id_Coque" value="<?= $row['Id_Coque'] ?>">
                        <input type="hidden" name="libelle" value="<?= htmlspecialchars($row['motif']) ?>">
                        <input type="hidden" name="prix" value="<?= htmlspecialchars($row['Prix']) ?>">
                        <input type="hidden" name="quantity" value="1">
                        <input type="submit" value="Ajouter au panier">
                    </form>
                </div>
        <?php }
        } else {
            echo "Aucun article trouvé.";
        }
        ?>
    </div>
    <script src="assets/js/main.js"></script>
</body>
</html>
