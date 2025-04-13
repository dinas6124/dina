create database bdd_image;
use bdd_image;

CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    pass_word VARCHAR(200) NOT NULL,
    date_inscription date
);

CREATE TABLE images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    image VARCHAR(250) NOT NULL,
    description TEXT,
    visibilite ENUM('publique', 'privee') DEFAULT 'privee',
    date_pub DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES user(id) ON DELETE CASCADE
);

CREATE TABLE annotations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_id INT NOT NULL,
    utilisateur_id INT,
    position_x INT NOT NULL,
    position_y INT NOT NULL,
    largeur INT NOT NULL,
    hauteur INT NOT NULL,
    texte TEXT,
    FOREIGN KEY (image_id) REFERENCES images(id) ON DELETE CASCADE,
    FOREIGN KEY (utilisateur_id) REFERENCES user(id) ON DELETE CASCADE,
);


