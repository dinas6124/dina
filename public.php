<?php
require_once("base.php");

$sql = "SELECT images.*, user.username 
        FROM images 
        JOIN user ON images.utilisateur_id = user.id 
        WHERE images.visibilite = 'publique' 
        ORDER BY images.date_pub DESC";

$stmt = $conn->prepare($sql);
$stmt->execute();
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Images Publiques</title>
    <style>
        body {
        background-color: #F8F4E3;
        font-family: 'Poppins', sans-serif;
        color: #4E4E4E;
        text-align: center;
        }

        h1 {
            color: #4E4E4E;
            margin-bottom: 30px;
        }

        .image-boite {
        column-count: 4 ;
        column-gap: 20px;
        padding: 20px;
        margin-left: 100px; 
        margin-right: 100px; 
        }

        .image-box {
        background-color: rgb(235, 245, 255);
        border: 1px solid;
        border-radius: 10px;
        padding: 10px;
        box-shadow: 0 8px 32px black;
        margin-bottom: 20px;
        margin-right: 20px;
        display: inline-block;
        width: 100%;
        break-inside: avoid;
            
        }

        .image-header {
        font-weight: bold;
        background-color:rgb(233, 217, 217);
        padding: 6px;
        border-radius: 10px;
        }

       

        img {
        width: 100%;
        height: auto;
        max-height: 500px;
        border-radius: 6px;
        }
        
        .menu {
        position: absolute;
        top: 20px;
        left: 20px;
        flex-direction: column;
        margin-top: 20px;
        
}

    .lien {
        border: none;
        color: #4E4E4E;
        font-size: 18px;
        text-align: left;
        font-weight: bold;
        padding: 5px;
    }
    </style>
</head>
<body>
    <h1>Images Publiques</h1>
    <div class="menu">
       <a href="accueil.html" class="lien"> Accueil</a>
       <a href="depot.php" class="lien"> Publier</a>
    </div>
    

    <div class="image-boite">
        <?php foreach ($images as $image): ?>
            <div class="image-box">
                <div class="image-header">
                    <?php echo $image['username']; ?>
                </div>

                <a href="image.php?id=<?php echo $image['id']; ?>">
                    <img src="../uploads/<?php echo $image['image']; ?>" alt="Image">
                </a>

            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>