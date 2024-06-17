<?php
session_start();
include('db.php');
include('functions.php');

if (!isset($_SESSION['id_customer'])) {
    die("Vous devez être connecté pour ajouter un article au panier.");
}

if (isset($_GET['action']) && $_GET['action'] == 'suppression' && isset($_GET['l'])) {
    $id_customer = $_SESSION['id_customer'];
    $libelle = rawurldecode($_GET['l']);

    // Supprimer l'article de la base de données
    $stmt = $db_connec->prepare('DELETE FROM cart WHERE id_customer = ? AND libelle = ?');
    if ($stmt === false) {
        die('Erreur de préparation de la requête : ' . $db_connec->error);
    }
    $stmt->bind_param("is", $id_customer, $libelle);
    $stmt->execute();
    $stmt->close();

    // Rediriger vers la page du panier après suppression
    header("Location: cart.php");
    exit();
}

// Reste du code pour ajouter des articles au panier
if (isset($_POST['id_Coque'])) {
    $id_Coque = $_POST['id_Coque'];
    $libelle = $_POST['libelle'];
    $prix = $_POST['prix'];
    $quantity = 1; // Quantité par défaut
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
        $query = $db_connec->prepare("INSERT INTO cart (id_customer, id_Coque, quantity, libelle, prix) VALUES (?, ?, ?, ?, ?)");
        if ($query === false) {
            die('Erreur de préparation de la requête : ' . $db_connec->error);
        }
        $query->bind_param("iiisd", $id_customer, $id_Coque, $quantity, $libelle, $prix);
    }

    if ($query->execute()) {
        echo "Article ajouté au panier avec succès.";
    } else {
        echo "Erreur lors de l'ajout de l'article au panier: " . $query->error;
    }
} else {
    echo "Aucun ID d'article soumis.";
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Votre panier</title>
    <link rel="stylesheet" href="../assets/css/cart.css">
</head>
<body>
    <div class="cart-container">
        <form method="post" action="">
            <table>
                <tr>
                    <th colspan="4">Votre panier</th>
                </tr>
                <tr>
                    <th>Libellé</th>
                    <th>Quantité</th>
                    <th>Prix Unitaire</th>
                    <th>Action</th>
                </tr>
                <?php
                $id_customer = $_SESSION['id_customer'];
                $stmt = $db_connec->prepare('SELECT * FROM cart WHERE id_customer = ?');
                $stmt->bind_param("i", $id_customer);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?= htmlspecialchars($row['libelle']) ?></td>
                            <td><input type='text' size='4' name='q[]' value='<?= htmlspecialchars($row['quantity']) ?>'/></td>
                            <td><?= htmlspecialchars($row['prix']) ?>€</td>
                            <td><a href='cart.php?action=suppression&l=<?= rawurlencode($row['libelle']) ?>'>Supprimer</a></td>
                        </tr>
                    <?php }
                    ?>
                    <tr><td colspan='2'></td>
                    <td colspan='2'>Total : <?= MontantGlobal($db_connec) ?>€</td></tr>
                    <tr><td colspan='4'>
                    <input type='submit' value='Rafraîchir'/>
                    <input type='hidden' name='action' value='refresh'/>
                    </td></tr>
                <?php } else { ?>
                    <tr><td colspan='4'>Votre panier est vide</td></tr>
                <?php } ?>
            </table>
            <div class="btn-container">
                <a href="../index.php" class="btn-return">Retour</a>
            </div>
        </form>
    </div>
</body>
</html>