<?php
session_start();
require_once("base.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: connexion.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "Image non trouvée.";
    exit;
}

$image_id = $_GET['id'];

$sql = "SELECT * FROM images WHERE id = ? AND utilisateur_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$image_id, $_SESSION["user_id"]]);
$image = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$image) {
    echo "Image introuvable ou vous n'y avez pas accès.";
    exit;
}

// Ajouter une annotation
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] === "add") {
    $x = $_POST["x"];
    $y = $_POST["y"];
    $width = $_POST["width"];
    $height = $_POST["height"];
    $texte = $_POST["texte"];

    $sql = "INSERT INTO annotations (image_id, utilisateur_id, x, y, width, height, texte) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$image_id, $_SESSION["user_id"], $x, $y, $width, $height, $texte]);
    exit;
}

// Supprimer une annotation
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] === "delete") {
    $id = $_POST["id"];
    $sql = "DELETE FROM annotations WHERE id = ? AND utilisateur_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id, $_SESSION["user_id"]]);
    echo json_encode(['success' => true]); // Répondre avec un JSON
    exit;
}

// Récupérer les annotations
$sql = "SELECT * FROM annotations WHERE image_id = ? AND utilisateur_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$image_id, $_SESSION["user_id"]]);
$annotations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Annoter une image</title>
    <style> 
     body {
        font-family:'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        text-align: center;
        background-color: #f8f4e3;
    }
        .container {
            position: relative;
            display: inline-block;
        }
        #img {
            width: 600px;
            cursor: crosshair;
            max-height: 800px;
        }
        .annotation {
            position: absolute;
            border: 2px solid  #4E4E4E ;
            background-color: rgba(177, 165, 165, 0.9);
            font-size: 1em; 
            padding: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.5);
            transition: all 0.3s ease; /* Transition pour un effet d'agrandissement */
        }
        .delete-btn {
            font-size: 10px;
            background: red;
            color: white;
            border: none;
            cursor: pointer;
            float: right;
            padding: 2px 5px;
            border-radius: 3px;
        }
        h2{
        color: #4E4E4E;
        }
        .retour{
            position: absolute;
            left: 0px;
            top: 0px;
            font-size: 25px;
            font-weight: bold;
            
        }
        .description{
            color: #4E4E4E ;
            font-size: 20px;
        }
    </style>
</head>
<body>
    <h2>Annoter l'image</h2>
    <div class="container" id="container">
        <img id="img" src="../uploads/<?= $image['image'] ?>" alt="Image">
        <?php foreach ($annotations as $a): ?>
            <div class="annotation" style="left: <?= $a['x'] ?>px; top: <?= $a['y'] ?>px; width:
                        <?= $a['width'] ?>px; height: <?= $a['height'] ?>px;">
                <form method="POST" class="delete-form" onsubmit="return confirm('Supprimer cette annotation ?');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $a['id'] ?>">
                    <button type="submit" class="delete-btn">×</button>
                </form>
                <?= $a['texte'] ?>
            </div>
        <?php endforeach; ?>
        <div class="description">
        <p><strong>Description :</strong> <?php echo $image['description']; ?></p>
        <p><strong>Date :</strong> <?php echo $image['date_pub']; ?></p>
       </div>
    </div>

    <script>
        const img = document.getElementById("img");
        const container = document.getElementById("container");

        let startX, startY, rect;

        img.addEventListener("mousedown", function(e) {
            const rectImg = img.getBoundingClientRect();
            startX = e.clientX - rectImg.left;
            startY = e.clientY - rectImg.top;

            rect = document.createElement("div");
            rect.style.position = "absolute";
            rect.style.border = "2px dashed blue";
            rect.style.left = startX + "px";
            rect.style.top = startY + "px";
            container.appendChild(rect);

            function onMouseMove(eMove) {
                const currX = eMove.clientX - rectImg.left;
                const currY = eMove.clientY - rectImg.top;

                const width = Math.abs(currX - startX);
                const height = Math.abs(currY - startY);
                rect.style.width = width + "px";
                rect.style.height = height + "px";
                rect.style.left = Math.min(startX, currX) + "px";
                rect.style.top = Math.min(startY, currY) + "px";
            }

            function onMouseUp(eUp) {
                document.removeEventListener("mousemove", onMouseMove);
                document.removeEventListener("mouseup", onMouseUp);

                const endX = eUp.clientX - rectImg.left;
                const endY = eUp.clientY - rectImg.top;
                const x = Math.min(startX, endX);
                const y = Math.min(startY, endY);
                const width = Math.abs(endX - startX);
                const height = Math.abs(endY - startY);

                container.removeChild(rect);

                const texte = prompt("Entrez votre annotation :");
                if (!texte) return;

                const formData = new FormData();
                formData.append("action", "add");
                formData.append("x", Math.round(x));
                formData.append("y", Math.round(y));
                formData.append("width", Math.round(width));
                formData.append("height", Math.round(height));
                formData.append("texte", texte);

                fetch("", {
                    method: "POST",
                    body: formData
                }).then(() => location.reload());
            }

            document.addEventListener("mousemove", onMouseMove);
            document.addEventListener("mouseup", onMouseUp);
        });

        // Gestion de la suppression d'annotation
        const deleteForms = document.querySelectorAll('.delete-form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(event) {
                event.preventDefault(); // Empêche la redirection
                const formData = new FormData(form);

                fetch("", {
                    method: "POST",
                    body: formData
                }).then(response => response.json()).then(data => {
                    if (data.success) {
                        // Supprime l'annotation du DOM
                        form.parentElement.remove();
                    } else {
                        alert("Erreur lors de la suppression de l'annotation.");
                    }
                });
            });
        });
    </script>
    <p class="retour"><a href="perso.php">← Retour à mes images</a></p>
</body>
</html>