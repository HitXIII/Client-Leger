<?php
include_once("db.php");

// Créer le panier s'il n'existe pas
function CreateCart(){
    if (!isset($_SESSION['panier'])){
        $_SESSION['panier'] = array();
        $_SESSION['panier']['libelle'] = array();
        $_SESSION['panier']['quantity'] = array();
        $_SESSION['panier']['prix'] = array();
        $_SESSION['panier']['verrou'] = false;
    }
    return true;
}

// Récupérer le panier
function GetCart() {
    return isset($_SESSION['panier']) ? $_SESSION['panier'] : array();
}

// Ajouter un article au panier
function AddToCart($id) {
    CreateCart();
    if (isset($_SESSION['panier'][$id])) {
        $_SESSION['panier'][$id]++;
    } else {
        $_SESSION['panier'][$id] = 1;
    }
}

// Supprimer un article du panier
function RemoveFromCart($id) {
    CreateCart();
    if (isset($_SESSION['panier'][$id])) {
        unset($_SESSION['panier'][$id]);
    }
}

// Obtenir le nombre total d'articles dans le panier
function GetCartCount() {
    CreateCart();
    $count = 0;
    foreach ($_SESSION['panier'] as $key => $value) {
        $count += $value;
    }
    return $count;
}

// Vérifier si l'utilisateur existe par login
function CheckIfUserExist($login) {
    global $db_connec;
    $sql = "SELECT * FROM `customer` WHERE login = ?";
    $stmt = $db_connec->prepare($sql);
    $stmt->bind_param('s', $login);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->num_rows > 0;
}

// Connecter l'utilisateur
function LoginUser($username, $password) {
    global $db_connec;

    $password = hash('sha256', $password);
    $sql = "SELECT * FROM `customer` WHERE login = ? AND mdp = ?";
    $stmt = $db_connec->prepare($sql);
    $stmt->bind_param('ss', $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['id_customer'] = $user['id_customer'];
        $_SESSION['login'] = $user['login'];
        $_SESSION['admin'] = $user['admin'];
        return true;
    } else {
        return false;
    }
}

// Créer un nouvel utilisateur
function CreateNewUser($login, $password, $email, $name) {
    if (!CheckIfUserExist($login)) {
        global $db_connec;

        $password = hash('sha256', $password);
        $sql = "INSERT INTO `customer` (login, mdp, mail, nom, prenom, admin) VALUES (?, ?, ?, ?, 'Test', 0)";
        $stmt = $db_connec->prepare($sql);
        $stmt->bind_param('ssss', $login, $password, $email, $name);
        if ($stmt->execute()) {
            LoginUser($login, $password);
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

// Ajouter un article au panier
function AddItemToCart($libelle, $quantity, $prix){
    if (CreateCart() && !IsLocked()) {
        $positionProduit = array_search($libelle, $_SESSION['panier']['libelle']);

        if ($positionProduit !== false) {
            $_SESSION['panier']['quantity'][$positionProduit] += $quantity;
        } else {
            array_push($_SESSION['panier']['libelle'], $libelle);
            array_push($_SESSION['panier']['quantity'], $quantity);
            array_push($_SESSION['panier']['prix'], $prix);
        }
    } else {
        echo "Un problème est survenu, veuillez contacter l'administrateur du site.";
    }
}

// Supprimer un article du panier
function DeleteItemFromCart($libelle){
    if (CreateCart() && !IsLocked()) {
        $tmp = array();
        $tmp['libelle'] = array();
        $tmp['quantity'] = array();
        $tmp['prix'] = array();
        $tmp['verrou'] = $_SESSION['panier']['verrou'];

        for ($i = 0; $i < count($_SESSION['panier']['libelle']); $i++) {
            if ($_SESSION['panier']['libelle'][$i] !== $libelle) {
                array_push($tmp['libelle'], $_SESSION['panier']['libelle'][$i]);
                array_push($tmp['quantity'], $_SESSION['panier']['quantity'][$i]);
                array_push($tmp['prix'], $_SESSION['panier']['prix'][$i]);
            }
        }
        $_SESSION['panier'] = $tmp;
        unset($tmp);
    } else {
        echo "Un problème est survenu, veuillez contacter l'administrateur du site.";
    }
}

// Modifier la quantité d'un article dans le panier
function EditQtqOfItem($libelle, $quantity){
    if (CreateCart() && !IsLocked()) {
        if ($quantity > 0) {
            $positionProduit = array_search($libelle, $_SESSION['panier']['libelle']);

            if ($positionProduit !== false) {
                $_SESSION['panier']['quantity'][$positionProduit] = $quantity;
            }
        } else {
            DeleteItemFromCart($libelle);
        }
    } else {
        echo "Un problème est survenu, veuillez contacter l'administrateur du site.";
    }
}

// Calculer le montant total du panier
function MontantGlobal(){
    CreateCart(); // Assurer que le panier est créé
    $total = 0;
    for ($i = 0; $i < count($_SESSION['panier']['libelle']); $i++) {
        $total += $_SESSION['panier']['quantity'][$i] * $_SESSION['panier']['prix'][$i];
    }
    return $total;
}

// Vérifier si le panier est verrouillé
function IsLocked(){
    return isset($_SESSION['panier']) && $_SESSION['panier']['verrou'];
}

// Compter le nombre d'articles dans le panier
function CountItems() {
    return isset($_SESSION['panier']) ? count($_SESSION['panier']['libelle']) : 0;
}

// Supprimer le panier
function DeleteCart(){
    unset($_SESSION['panier']);
}
?>
