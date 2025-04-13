<?php
session_start();
require_once("base.php");


// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION["user_id"])) {
    echo "Utilisateur non connecté.";
    header("Location: connexion.php");
    exit;
} 

$user_name = $_SESSION ['username'];
if(isset($_POST['logout'])){
    session_unset();
    session_destroy();
    header("Location: connexion.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["photo"])) {
    $description =$_POST["description"] ?? '';
    $visibilite = $_POST["visibilite"] ?? 'publique';
    $utilisateur_id = $_SESSION["user_id"];

    // Dossier de stockage
    $dossier = "../uploads/";
    if (!is_dir($dossier)) {
        mkdir($dossier, 0777, true);
    } 

    // Nom unique pour l'image
    $nom_temp = $_FILES["photo"]["tmp_name"];
    $nom_orig = basename($_FILES["photo"]["name"]);
    $extension = pathinfo($nom_orig, PATHINFO_EXTENSION);
    $nom_fichier = uniqid() . "." . $extension;
    $chemin_final = $dossier . $nom_fichier;

    // Déplacer l’image
    if (move_uploaded_file($nom_temp, $chemin_final)) {
        // Enregistrer les infos en base
        $sql = "INSERT INTO images (utilisateur_id, image, description, visibilite)
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$utilisateur_id, $nom_fichier, $description, $visibilite]);

        header("Location: perso.php?upload=ok");
        exit;
    } else {
        header("Location: depot.php?upload=fail");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Déposer une image</title>
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


.box{ 
    background-color: #fffdf6;
    padding: 40px;
    border-radius: 25px;
    text-align: center;
    border: 2px solid #d6c5a4;
    box-shadow: 0 8px 20px black;
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
    margin-top: 10px;
}

button:hover {
    background-color: #d68c6a;
}

header {
    background-color:rgb(216, 191, 161); 
    padding: 20px 0; 
    position: fixed;
    top: 0;
    width: 100%;
}

.head  {
    display: flex;
    font-size: 18px;
}

.head a {
    
    padding: 20px;
    color:white;
    font-weight: bold;
}

.close {
    position: absolute;
    right: 20px;
    top: 20px;
    color: white;
    font-weight: bold;
    text-decoration: none;
    font-size: 20px;
} 

p {
    font-size: 23px;
    margin-bottom: 10px;
    margin-top: 20px;
    font-weight: bold;
    color: #3b2f2f;
}

label[for="visibilite"] {
    font-weight: bold;
}

/* Pour agrandir le menu déroulant */
#visibilite {
    width: 200px; 
    padding: 8px;
    font-size: 14px;
    border-radius: 5px;
   
} 
 header p {
   text-align: right;
   color: #3b2f2f;

}

    </style>
</head>
<body>
    <header>
        <div class="head">
            <a href="perso.php">Mes images</a>
            <a href="public.php">Explorer</a>
        </div>
       <form method="post" action=""><button type="submit"  name="logout" class="close" >Déconnexion</button></form>
    </header>

    <div class="box">
        <p>Publiez votre image</p>
        
        <form action="depot.php" method="POST" enctype="multipart/form-data">
            <!-- Sélection du fichier -->
            <input type="file" name="photo" required>

            <!-- Nouvelle zone de description -->
            <div>
                <label for="description">Description de l'image :</label><br>
                <textarea name="description" id="description" rows="4" cols="40" placeholder="Entrez une brève description..."></textarea>
            </div>

            <!-- Visibilité de l’image -->
            <div class="visible">
                <label for="visibilite">Visibilité :</label><br>
                <select name="visibilite" id="visibilite">
                    <option value="privee">Privée</option>
                    <option value="publique">Publique</option>
                </select>
            </div>

            <button type="submit">Déposer l'image</button>
        </form>

        <?php if (isset($_GET['upload'])): ?>
            <?php if ($_GET['upload'] == 'ok'): ?>
                <p style="color:green;">Image téléchargée avec succès !</p>
            <?php elseif ($_GET['upload'] == 'fail'): ?>
                <p style="color:red;">Échec du téléchargement de l'image.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>