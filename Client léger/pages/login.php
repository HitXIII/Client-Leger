<?php
session_start();
include_once("functions.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require("db.php");

    if (isset($_POST["username"]) && isset($_POST["password"])) {
        // Connexion de l'utilisateur
        $username = mysqli_real_escape_string($db_connec, $_POST["username"]);
        $password = hash('sha256', $_POST["password"]);

        $sql = "SELECT * FROM `customer` WHERE login = ? AND mdp = ?";
        $stmt = $db_connec->prepare($sql);
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $_SESSION["login"] = $user["login"];
            $_SESSION["id_customer"] = $user["id_customer"]; // Utilisation de id_customer ici
            $_SESSION["admin"] = $user["admin"];
            $_SESSION["id"] = session_id();

            // Enregistrement de la connexion
            $login = $_SESSION['login'];
            $req = "INSERT INTO connexions (login, datedeb) VALUES (?, NOW())";
            $stmt = $db_connec->prepare($req);
            $stmt->bind_param("s", $login);
            $stmt->execute();
            $connexion_id = $stmt->insert_id;
            $_SESSION["connexion_id"] = $connexion_id;
            $stmt->close();

            header("Location: ../index.php");
            exit();
        } else {
            header("Location: login.php?erreur=1");
            exit();
        }
    } elseif (isset($_POST["login"]) && isset($_POST["password"]) && isset($_POST["email"]) && isset($_POST["name"])) {
        // Inscription de l'utilisateur
        $login = mysqli_real_escape_string($db_connec, $_POST["login"]);
        $password = hash('sha256', $_POST["password"]);
        $email = mysqli_real_escape_string($db_connec, $_POST["email"]);
        $name = mysqli_real_escape_string($db_connec, $_POST["name"]);

        // Vérifiez si l'utilisateur existe déjà
        $sql = "SELECT * FROM `customer` WHERE login = ?";
        $stmt = $db_connec->prepare($sql);
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            header("Location: login.php?erreur=3"); // L'utilisateur existe déjà
            exit();
        } else {
            // Crée un nouvel utilisateur
            $sql = "INSERT INTO `customer` (login, mdp, mail, nom, prenom, admin) VALUES (?, ?, ?, ?, '', 0)";
            $stmt = $db_connec->prepare($sql);
            $stmt->bind_param("ssss", $login, $password, $email, $name);

            if ($stmt->execute()) {
                // Connexion automatique après inscription
                $_SESSION["login"] = $login;
                $_SESSION["id_customer"] = $stmt->insert_id; // Utilisation de id_customer ici
                $_SESSION["id"] = session_id();

                // Enregistrement de l'inscription
                $req = "INSERT INTO connexions (login, datedeb) VALUES (?, NOW())";
                $stmt = $db_connec->prepare($req);
                $stmt->bind_param("s", $login);
                $stmt->execute();
                $connexion_id = $stmt->insert_id;
                $_SESSION["connexion_id"] = $connexion_id;
                $stmt->close();

                header("Location: ../index.php");
                exit();
            } else {
                header("Location: login.php?erreur=2"); // Erreur lors de la création de l'utilisateur
                exit();
            }
        }
        $stmt->close();
    } else {
        header("Location: login.php?erreur=1"); // Champs manquants
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../assets/css/login.css">
    <title>Motion Case | Login</title>
</head>
<body>
    <div class="container" id="container">
        <div class="form-container sign-up-container">
            <form action="login.php" method="post">
                <h1>Create Account</h1>
                <input type="text" name="login" placeholder="Login" required />
                <input type="text" name="name" placeholder="Name" required />
                <input type="email" name="email" placeholder="Email" required />
                <input type="password" name="password" placeholder="Password" required />
                <button>Sign Up</button>
            </form>
        </div>
        <div class="form-container sign-in-container">
            <form action="login.php" method="post">
                <h1>Sign in</h1>
                <input type="text" name="username" placeholder="Username" required />
                <input type="password" name="password" placeholder="Password" required />
                <?php if(isset($_GET["erreur"])) echo "<p>Erreur de login ou de mot de passe</p>"; ?>
                <a href="#">Forgot your password?</a>
                <button>Sign In</button>
            </form>
        </div>
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Welcome Back!</h1>
                    <p>To keep connected with us please login with your personal info</p>
                    <button class="ghost" id="signIn">Sign In</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Hello, Friend!</h1>
                    <p>Enter your personal details and start journey with us</p>
                    <button class="ghost" id="signUp">Sign Up</button>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/js/login.js"></script>
</body>
</html>
