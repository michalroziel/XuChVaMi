<?php
// 1. Verbindung zur SQLite-Datenbank herstellen
//    __DIR__ liefert das aktuelle Verzeichnis der PHP-Datei
//    Wir gehen davon aus, dass database.db im selben Ordner liegt.
//    Sonst Pfad entsprechend anpassen.
$pdo = new PDO('sqlite:' . __DIR__ . '/database.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 2. Werte aus dem POST-Array lesen
$dishName = $_POST['dish_name'] ?? null;  // ?? null bedeutet: falls nicht gesetzt, nimm null
$rating   = $_POST['rating'] ?? null;
$comment  = $_POST['comment'] ?? null;

// 3. Überprüfe, ob alles da ist (basic validation)
if ($dishName === null || $rating === null) {
    die("Fehlende Eingaben!");
}

// 4. SQL-Insert vorbereiten
$sql = "INSERT INTO mensa_ratings (dish_name, rating, comment)
        VALUES (:dish_name, :rating, :comment)";
$stmt = $pdo->prepare($sql);

// 5. Ausführen
$stmt->execute([
    ':dish_name' => $dishName,
    ':rating'    => $rating,
    ':comment'   => $comment
]);

// 6. Erfolgsmeldung oder Weiterleitung
echo "Bewertung erfolgreich gespeichert!<br>";
echo "<a href='index.html'>Zurück zum Formular</a>";