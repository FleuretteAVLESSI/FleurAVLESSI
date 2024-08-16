<?php
require_once 'db_connection.php';
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>

    <meta charset="UTF-8">

    <title>Dictionnaire Bariba-Français</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <style>
        body {
    font-family: Arial, sans-serif;
    background-image: url('dictionnaire.jpg');
    margin: 10px;
    padding: 10px;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    }
    .container {
    background-color: #ffffff7f;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 700px;
    }
    h1 {
    color: #333;
    text-align: center;
    }
    form {
    display: flex;
    flex-direction: column;
    gap: 05px;
    }
    input[type="text"],
    select,
    button {
    padding: 05px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
    }
    button {
    background-color:rgba(33, 180, 104, 0.938);
    color: white;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s;
    }
    button:hover {
    background-color: #2ab300;
    }
    .result {
    margin-top: 20px;
    background-color: #f9f9f9;
    padding: 15px;
    border-radius: 5px;
    border: 1px solid #ddd;
    }
    .result p {
    margin: 0;
    padding: 5px 0;
    }
    .not-found {
    text-align: center;
    color: red;
    margin-top: 20px;
    }
    .titre{
    color: #0056b3;
    }
    button i {
    margin-right: 5px; /* Ajouter un espace entre l'icône et le texte */
    font-size: 16px;   /* Ajuster la taille de l'icône */
}

button i.bi {
    color: #fff;  /* Exemple pour changer la couleur de l'icône */
}
    </style>
    <div class="container">
        <h1>Dictionnaire Bariba-Français</h1>
        <form method="POST">
            <input type="text" name="query" placeholder="Entrez un mot...">
            <select name="direction">
                <option value="bariba">Bariba vers Français</option>
                <option value="francais">Français vers Bariba</option>
            </select>
            <button type="submit">Rechercher</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $query = trim($_POST['query']);
            $direction = $_POST['direction'];

            if ($direction == 'bariba') {
                $stmt = $pdo->prepare("SELECT * FROM traduction WHERE mot_bariba = :query");
                $stmt->execute(['query' => $query]);
            } else {
                // Recherche dans 'exemple_français' à la place de 'mot_français'
                $stmt = $pdo->prepare("SELECT * FROM traduction WHERE example_francais LIKE :query");
                $stmt->execute(['query' => '%' . $query . '%']);
            }

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $found = false;

            foreach ($results as $entry) {
                if ($direction == 'bariba') {
                    $found = true;
                    echo "<div class='result'>";
                    echo "<p><strong>Définition :</strong> " . htmlspecialchars($entry['definitions']) . "</p>";
                    echo "</div>";
                    break;
                } elseif ($direction == 'francais') {
                    // Vérifiez si 'exemple_français' contient le mot recherché
                    if (isset($entry['example_francais']) && strpos($entry['example_francais'], $query) !== false) {
                        $found = true;
                        echo "<div class='result'>";
                        //echo "<p><strong>Mot :</strong> " . htmlspecialchars($entry['mot_bariba']) . "</p>";
                       // echo "<p><strong>Phonétique :</strong> " . htmlspecialchars($entry['phonetic']) . "</p>";
                       // echo "<p><strong>Partie du discours :</strong> " . htmlspecialchars($entry['part_of_speech']) . "</p>";
                        echo "<p><strong>Définition :</strong> " . htmlspecialchars($entry['mot_bariba']) . "</p>";
                       // echo "<p><strong>Exemple en Bariba :</strong> " . htmlspecialchars($entry['example_bariba']) . "</p>";
                        //echo "<p><strong>Exemple en Français :</strong> " . htmlspecialchars($entry['example_francais']) . "</p>";
                        echo "</div>";
                        break;
                    }
                }
            }

            if (!$found) {
                echo "<p class='not-found'>Aucune traduction trouvée pour '$query'.</p>";
            }
        }
        ?>

<<div class="container">
    <h1>Enregistrement et Écoute Audio</h1>
    
    <div>
        <h2>Enregistrement Audio</h2>
        <div class="audio-section">
            <textarea id="text-to-read" rows="3" readonly></textarea>
            <button id="next-text-recording" class="btn-right">Suivant</button>
        </div>
        <div class="button-group">
            <button id="start-recording"><i class="bi bi-mic"></i>Parler</button>
            <button id="stop-recording" disabled><i class="bi bi-stop-fill"></i>Arrêter</button>
            <button id="validate-recording" disabled><i class="bi bi-check"></i>Valider</button>
        </div>
        <p id="confirmation-message"></p>
    </div>

    <div>
        <h2>Écoute Audio</h2>
        <div class="audio-section">
            <textarea id="text-to-listen" rows="3" readonly></textarea>
            <button id="next-text-listening" class="btn-right"><i class="bi bi-arrow-right"></i>Suivant</button>
        </div>
        <button id="listen-audio">Écouter</button>
        <audio id="audio-playback" controls></audio>
    </div>
</div>



    <!-- Inclusion des scripts JavaScript -->
    <script src="script.js"></script>
</body>
</html>
