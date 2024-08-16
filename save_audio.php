<?php
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification si tous les champs nécessaires sont présents
    if (isset($_POST['text']) && isset($_FILES['audio'])) {
        $texte = $_POST['text'];
        $audio = file_get_contents($_FILES['audio']['tmp_name']);
        $date_enregistrement = date('Y-m-d H:i:s');

        try {
            // Mise à jour du texte et enregistrement de l'audio dans la base de données
            $stmt = $pdo->prepare("UPDATE notes_vocales SET audio = :audio, date_enregistrement = :date_enregistrement WHERE texte = :texte");
            $stmt->execute([
                'audio' => $audio, 
                'date_enregistrement' => $date_enregistrement, 
                'texte' => $texte
            ]);

            if ($stmt->rowCount() > 0) {
                $response = ['message' => 'Enregistrement audio réussi.'];
            } else {
                $response = ['message' => 'Aucun enregistrement trouvé pour ce texte.'];
            }
        } catch (PDOException $e) {
            $response = ['error' => 'Erreur lors de l\'enregistrement audio : ' . $e->getMessage()];
        }
    } else {
        $response = ['error' => 'Données manquantes.'];
    }

    // Retour de la réponse au format JSON
    echo json_encode($response);
}
?>
