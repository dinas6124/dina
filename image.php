<?php
require_once("base.php");

if (!isset($_GET['id'])) {
    echo "Image introuvable.";
    exit;
}

$image_id = $_GET['id'];

$sql = "SELECT images.*, user.username 
        FROM images 
        JOIN user ON images.utilisateur_id = user.id 
        WHERE images.id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$image_id]);
$image = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$image) {
    echo "Image introuvable.";
    exit;
}

// On récupère les annotations
$sql_ann = "SELECT * FROM annotations WHERE image_id = ?";
$stmt_ann = $conn->prepare($sql_ann);
$stmt_ann->execute([$image_id]);
$annotations = $stmt_ann->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail de l'image</title>
    <style>
body {
        font-family:'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        text-align: center;
        background-color: #f8f4e3;
    }
       
        .image-contenu {
            position: relative;
            display: inline-block;
            margin-top: 30px;
           
        }

        .image-contenu img {
            max-height: 700px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.3);
        }

        .annotation-text {
            position: absolute;
            background: rgba(255, 255, 255, 0.8);
            padding: 3px 6px;
            font-size: 14px;
            font-weight: bold;
            color: #000;
            border-radius: 4px;
        }

        .description{
            color: #4E4E4E ;
            font-size: 20px;
        }
        h1{
            color: #4E4E4E ;
        }
    </style>
</head>
<body>
    <h1>Image de <?php echo $image['username']; ?></h1>

    <div class="image-contenu">
        <img src="../uploads/<?php echo $image['image']; ?>" alt="Image" id="mainImage">

        <?php foreach ($annotations as $ann): ?>
            
            <div class="annotation-text" style="left:<?php echo $ann['x']; ?>px; top:<?php echo $ann['y'] + $ann['height']; ?>px;">
                <?php echo $ann['texte']; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="description">
        <p><strong>Description :</strong> <?php echo $image['description']; ?></p>
        <p><strong>Date :</strong> <?php echo $image['date_pub']; ?></p>
    </div>
</body>
</html>
