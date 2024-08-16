<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'dictionnaire';
$username = 'root';
$password = ''; 

try {
    $pdo = new PDO('mysql:host=localhost;dbname=dictionnaire', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
