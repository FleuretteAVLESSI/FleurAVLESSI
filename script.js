// Récupération des éléments HTML
const textToReadElement = document.getElementById('text-to-read');
const textToListenElement = document.getElementById('text-to-listen');
const nextTextButtonRecording = document.getElementById('next-text-recording');
const nextTextButtonListening = document.getElementById('next-text-listening');
const startRecordingButton = document.getElementById('start-recording');
const stopRecordingButton = document.getElementById('stop-recording');
const validateButton = document.getElementById('validate-recording');
const audioPlayback = document.getElementById('audio-playback');
const confirmationMessage = document.getElementById('confirmation-message');
const listenButton = document.getElementById('listen-audio');

let texte = [];

// Fonction pour récupérer les textes
async function fetchTexts() {
    try {
        const response = await fetch('get_texts.php');
        texte = await response.json();
        console.log("Textes récupérés :", texte); // Vérification des textes récupérés
        displayTextForRecording();
        displayTextForListening();
    } catch (error) {
        console.error("Erreur lors de la récupération des textes :", error);
    }
}

// Fonction pour obtenir un texte aléatoire
function getRandomText() {
    const randomIndex = Math.floor(Math.random() * texte.length);
    return texte[randomIndex];
}

// Fonction pour afficher le texte pour l'enregistrement
function displayTextForRecording() {
    if (texte.length > 0) {
        const currentText = getRandomText();
        console.log("Texte pour enregistrement :", currentText.texte); // Vérification
        textToReadElement.value = currentText.texte;
    } else {
        textToReadElement.value = "Aucun texte disponible.";
    }
}

// Fonction pour afficher le texte pour l'écoute
function displayTextForListening() {
    if (texte.length > 0) {
        const currentText = getRandomText();
        console.log("Texte pour écoute :", currentText.texte); // Vérification
        textToListenElement.value = currentText.texte;
    } else {
        textToListenElement.value = "Aucun texte disponible.";
    }
}

// Event listener pour changer le texte pour l'enregistrement
nextTextButtonRecording.addEventListener('click', displayTextForRecording);

// Event listener pour changer le texte pour l'écoute
nextTextButtonListening.addEventListener('click', displayTextForListening);

// Gestion de l'enregistrement audio
let mediaRecorder;
let audioChunks = [];

startRecordingButton.addEventListener('click', async () => {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        mediaRecorder = new MediaRecorder(stream);
        mediaRecorder.start();

        mediaRecorder.addEventListener('dataavailable', event => {
            audioChunks.push(event.data);
        });

        mediaRecorder.addEventListener('stop', () => {
            const audioBlob = new Blob(audioChunks);
            const audioUrl = URL.createObjectURL(audioBlob);
            audioPlayback.src = audioUrl;
            validateButton.disabled = false;
        });

        startRecordingButton.disabled = true;
        stopRecordingButton.disabled = false;
        console.log("Enregistrement commencé."); // Log pour déboguer
    } catch (error) {
        console.error("Erreur lors du démarrage de l'enregistrement :", error);
    }
});

stopRecordingButton.addEventListener('click', () => {
    mediaRecorder.stop();
    startRecordingButton.disabled = false;
    stopRecordingButton.disabled = true;
    console.log("Enregistrement arrêté."); // Log pour déboguer
});

validateButton.addEventListener('click', async () => {
    const audioBlob = new Blob(audioChunks);
    const formData = new FormData();
    formData.append('audio', audioBlob);
    formData.append('text', textToReadElement.value);

    try {
        const response = await fetch('save_audio.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        confirmationMessage.textContent = result.message;
        console.log("Enregistrement audio validé :", result); // Log pour déboguer
    } catch (error) {
        console.error("Erreur lors de la validation de l'enregistrement audio :", error);
    }
});

// Gestion de l'écoute audio
listenButton.addEventListener('click', async () => {
    const currentText = textToListenElement.value;
    try {
        const response = await fetch(`get_audio.php?text=${encodeURIComponent(currentText)}`);
        const result = await response.json();

        if (result.audioUrl) {
            audioPlayback.src = result.audioUrl;
            console.log("Lecture de l'audio :", result.audioUrl); // Log pour déboguer
        } else {
            confirmationMessage.textContent = "Ce texte n'a pas encore d'audio. Vous pouvez faire un enregistrement audio de ce texte plus haut.";
            console.log("Pas d'audio disponible pour ce texte."); // Log pour déboguer
        }
    } catch (error) {
        console.error("Erreur lors de la récupération de l'audio :", error);
    }
});

// Fetch texts on page load
fetchTexts();
