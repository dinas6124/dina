<?php
session_start();
require_once("base.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"] ;
    $password = $_POST["mot_de_passe"] ;

    $email = trim($email);
    $password = trim($password);

    $sql = "SELECT * FROM user WHERE email = :email";
    $stmt = $conn->prepare($sql);

    // Liaison des paramètres
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Vérification du mot de passe
        if (password_verify($password, $user['pass_word'])) {
            // Authentification réussie, stocker les informations de l'utilisateur dans la session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: perso.php?connexion=ok");
            exit;
        } else {
            // Mot de passe incorrect
            header("Location: connexion.php?erreur=motdepasse");
            exit;
        }
    } else {
        // Email non trouvé
        header("Location: connexion.php?erreur=email");
        exit;
    }

    // Fermeture de la déclaration
    $stmt->closeCursor();
}

// Fermeture de la connexion
$conn = null; // Fermer la connexion PDO
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <style>
        
        body {
            margin: 0;
            background-color: #f8f4e3;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: sans-serif;
            height: 100vh;
        }


        .connexion {
            background-color: #fffdf6;
            padding: 40px;
            border-radius: 25px;
            text-align: center;
            border: 2px solid #d6c5a4;
            box-shadow: 0 8px 20px black;
        }


        h1 {
            font-family:'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
            font-size: 35px;
            color: #4E4E4E;
            margin-bottom: 30px;
        }


        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }


        input {
            padding: 12px;
            border-radius: 10px;
            border: 1px solid gray;
            width: 90%;
            font-size: 16px;
            margin: 16px;
            font-family: inherit;
        }

        button {
            padding: 12px;
            border: none;
            border-radius: 10px;
            background-color: #a8c4a2;
            color: white;
            font-weight: bold;
            font-size: 17px;
        }


        button:hover {
            background-color: #d68c6a;
        }
p {
    font-size: 23px;
    margin-bottom: 10px;
    margin-top: 20px;
    font-weight: bold;
    color: #3b2f2f;
}
.revenir a {
    position: absolute;
    top: 20px;
    left: 20px;
    background-color: #d6b690;
    font-weight: bold;
    padding: 8px 12px;
    border-radius: 8px;
    color: white;
    text-decoration: none;
        }
a{
    color: #d68c6a;
    text-decoration: none;
        font-weight: bold;
}

    </style>
</head>
<body> 
<p class="revenir"><a href="accueil.html">Revenir à l'accueil</a></p>

    <div class="connexion">
        <h1>Connexion</h1>
        <form action="connexion.php" method="POST">
            <input type="email" name="email" placeholder="E-mail" required>
            <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
            <br>
            <button type="submit">Se connecter</button>
        </form>
        <p>Vous n'avez pas encore un compte ? <a href="inscription.php">Inscrivez-vous</a></p>
        
        <?php if (isset($_GET['erreur'])): ?>
            <?php if ($_GET['erreur'] == 'motdepasse'): ?>
                <p style="color:red;">Mot de passe incorrect.</p>
            <?php elseif ($_GET['erreur'] == 'email'): ?>
                <p style="color:red;">Email non trouvé.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>