<?php
$host = 'localhost';  
$dbname = 'dictionnaire'; 
$username = 'root';  
$password = ''; 
$jsonFilePath = 'dictionnaire.json'; // Path to your JSON file

try {
    // Connect to the database with PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Read the JSON file
    $jsonData = file_get_contents($jsonFilePath);

    // Decode the JSON into an associative array
    $data = json_decode($jsonData, true);

    // Check if decoding was successful
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('JSON decode error: ' . json_last_error_msg() . '. Problematic JSON: ' . substr($jsonData, 0, 200) . '...');
    }

    // Begin a transaction for batch insert
    $pdo->beginTransaction();

    // Prepare the insert statement for traductions
    $insertTraductionStmt = $pdo->prepare("
        INSERT INTO traduction (mot_bariba, phonetic, part_of_speech, definitions, example_bariba, example_francais) 
        VALUES (:word, :phonetic, :part_of_speech, :definition, :example_bariba, :example_francais)
    ");

    // Loop over the data and insert into the table traductions
    foreach ($data as $entry) {
        $insertTraductionStmt->execute([
            'word' => $entry['word'],
            'phonetic' => $entry['phonetic'],
            'part_of_speech' => $entry['part_of_speech'],
            'definition' => $entry['definition'],
            'example_bariba' => is_array($entry['example_bariba']) ? implode(', ', $entry['example_bariba']) : $entry['example_bariba'],
            'example_francais' => is_array($entry['example_francais']) ? implode(', ', $entry['example_francais']) : $entry['example_francais']
        ]);
    }

    echo "Data successfully imported into the traductions table!<br>";

    // Commit the transaction
    $pdo->commit();

    // Retrieve data from traductions for insertion into notes_vocales
    $stmt = $pdo->query("SELECT example_bariba FROM traduction");
    $translations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare the insert statement for notes_vocales
    $insertNotesVocalesStmt = $pdo->prepare("INSERT INTO notes_vocales (texte) VALUES (:texte)");

    // Begin another transaction for notes_vocales insertions
    $pdo->beginTransaction();

    // Insert data into the notes_vocales table
    foreach ($translations as $translation) {
        $texte = $translation['example_bariba'];
        $insertNotesVocalesStmt->execute(['texte' => $texte]);
    }

    // Commit the transaction for notes_vocales
    $pdo->commit();

    echo "Data successfully imported into the notes_vocales table!";
    
} catch (PDOException $e) {
    // Rollback the transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die('Database error: ' . $e->getMessage());
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>
