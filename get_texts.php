<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'dictionnaire';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
    exit;
}

// Récupération des textes depuis la table 'notes_vocales'
try {
    $stmt = $pdo->query("SELECT texte FROM notes_vocales");
    $texts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retour des textes au format JSON
    echo json_encode($texts);
} catch (PDOException $e) {
    echo json_encode(['error' => "Erreur lors de la récupération des textes : " . $e->getMessage()]);
}
?>
