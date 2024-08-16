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

// Vérification si le texte a été passé en paramètre
if (isset($_GET['text'])) {
    $text = $_GET['text'];

    // Requête pour récupérer l'URL de l'audio associé
    try {
        $stmt = $pdo->prepare("SELECT audio FROM notes_vocales WHERE texte = :text");
        $stmt->execute(['text' => $text]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && !empty($result['audio'])) {
            // Si un audio est trouvé, renvoyer l'URL de l'audio
            echo json_encode(['audioUrl' => $result['audio']]);
        } else {
            // Si aucun audio n'est trouvé, renvoyer un message d'erreur
            echo json_encode(['message' => "Aucun audio trouvé pour ce texte."]);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => "Erreur lors de la récupération de l'audio : " . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => "Aucun texte fourni."]);
}
?>
