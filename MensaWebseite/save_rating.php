<?php
// save_rating.php

$pdo = new PDO('sqlite:' . __DIR__ . '/database.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// POST-Daten holen
$dishId  = $_POST['dish_id'] ?? null;
$rating  = $_POST['rating']   ?? null;
$comment = $_POST['comment']  ?? '';

// Fehler abfangen
if (!$dishId || !$rating) {
    die("Ungültige Eingabe!");
}

// Bewertung speichern
$sql = "INSERT INTO ratings (dish_id, rating, comment)
        VALUES (:dish_id, :rating, :comment)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':dish_id' => $dishId,
    ':rating'  => $rating,
    ':comment' => $comment
]);

echo "Bewertung erfolgreich gespeichert!<br>";
echo "<a href='index.php'>Zurück zur Nutzerseite</a>";