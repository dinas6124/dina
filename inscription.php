<?php
session_start();
require_once("base.php");

$errorMessage = ''; // Initialisation de la variable d'erreur

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"] ; 
    $email = $_POST["email"] ;
    $password = $_POST["pass_word"] ;
  
    $email = trim($email);
    $username = trim($username);
    $password = password_hash($password, PASSWORD_DEFAULT);

    // Vérification si l'email ou le nom d'utilisateur existe déjà
    $checkSql = "SELECT * FROM user WHERE email = :email OR username = :username";
    $checkStmt = $conn->prepare($checkSql);
    
    $checkStmt->bindParam(':email', $email);
    $checkStmt->bindParam(':username', $username);
    $checkStmt->execute();
    $result = $checkStmt->fetchAll(PDO::FETCH_ASSOC);
 
    if (count($result) > 0) {
        // L'email ou le nom d'utilisateur existe déjà
        foreach ($result as $existingUser ) {
            if ($existingUser ['email'] === $email) {
                $errorMessage = "L'email est déjà utilisé.";
                break;
            } elseif ($existingUser ['username'] === $username) {
                $errorMessage = "Le nom d'utilisateur est déjà pris.";
                break;
            }
        }
    } else {
        // Insertion dans la BDD
        $sql = "INSERT INTO user (username, email, pass_word, date_inscription) VALUES (:username, :email, :pass_word, CURRENT_TIMESTAMP)";
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':pass_word', $password);

        // Exécution de la requête
        if ($stmt->execute()) {
            header("Location: perso.php?inscription=ok");
            exit;
        } else {
            $errorMessage = "Erreur inconnue lors de l'inscription.";
        }
    }

    // Fermeture de la déclaration
    $checkStmt->closeCursor();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <script src="script.js"></script> <!-- Lien vers le fichier JavaScript -->
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


        .inscription {
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
    
    <div class="inscription">
        <h1>Inscription</h1>
        <form id="inscription" action="inscription.php" method="POST">
            <input type="text" name="username" placeholder="Nom d'utilisateur" required>
            <input type="email" name="email" placeholder="E-mail" required>
            <input type="password" name="pass_word" placeholder="Mot de passe" required>
            <br>
            <button type="submit">S'inscrire</button>
        </form>
        <p>Vous avez déjà un compte ? <a href="connexion.php">Connectez-vous</a></p>
        
        <?php if (!empty($errorMessage)): ?>
            <p style="color:red;"><?php echo $errorMessage; ?></p> 
        <?php endif; ?>
    </div>
</body>
</html>