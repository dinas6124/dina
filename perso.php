<?php
session_start();
require_once("base.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: connexion.php");
    exit;
}

$user_name = $_SESSION['username'];
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: connexion.php");
    exit;
}

$utilisateur_id = $_SESSION["user_id"];

$sql = "SELECT * FROM images WHERE utilisateur_id = ? ORDER BY date_pub DESC";
$stmt = $conn->prepare($sql);
$stmt->execute([$utilisateur_id]);
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes images</title>
    <style>
       body {
      background-color: #F8F4E3;
       font-family: 'Poppins', sans-serif;
       color: #4E4E4E;
    text-align: center;
   
}

h1, h2 {
        color: #4E4E4E;
        margin-bottom: 30px;

    }

.image-boite {
    column-count: 4 ;
    column-gap: 20px;
    padding: 20px;
    margin-left: 70px; 
    margin-right: 70px; 
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

.close {
    background-color: #A8C4A2;
    color: white;
    padding: 5px 15px;
    border: none;
    border-radius: 5px;
}

.close:hover {
    background-color: rgb(184, 6, 6);
}

.menu {
    position: absolute;
    top: 20px;
    left: 20px;
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
.btn{
    font-size: 1rem;
    padding: 12px 24px;
    color:white ;
    text-decoration: none;
    text-align: right;
    font-weight: bold;
    background-color: #A8C4A2;
    border-radius: 10px;
    position: absolute;  
    top: 20px; 
    right: 20px; 
}


button:hover {
     background-color: #D68C6A;
}

       
    </style>
</head>
<body>
    <h2>Bienvenue, <?php echo $user_name; ?>!</h2>
    <h1>Mes images</h1>
    <div class="menu">
       <a href="public.php" class="lien"> Explorer</a>
       <a href="depot.php" class="lien"> Publier</a>
    </div>
    <form method="post" action=""><button type="submit"  name="logout" class="btn" >Déconnexion</button></form>

    <div class="image-boite">
        <?php foreach ($images as $image): ?>
            <div class="image-box">
                <div class="image-header">
                    <?php echo $user_name; ?> - <?php echo $image['visibilite']; ?>
                </div>

                <a href="annoter.php?id=<?php echo $image['id']; ?>">
                    <img src="../uploads/<?php echo $image['image']; ?>" alt="Image">
                </a>

                
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>

