<?php
// **************************************************************
// save_rating.php
// **************************************************************
// Dieses Skript nimmt die vom Formular (index.php) übermittelten
// Bewertungsdaten entgegen und speichert sie in der Datenbank.
// **************************************************************

// 1) Aufbau der Datenbankverbindung
// Wir nutzen wieder PDO für die SQLite-Datenbank, die in demselben
// Verzeichnis wie dieses Skript liegt.
$pdo = new PDO('sqlite:' . __DIR__ . '/database.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 2) Abholen der POST-Daten
// Hier werden die vom Nutzer eingegebenen Daten (Gericht-ID, Bewertung, Kommentar) aus $_POST ausgelesen.
// Mit dem Null-Coalescing-Operator (??) wird sichergestellt, dass bei nicht vorhandenen Indizes
// ein definierter Wert (null oder '') zurückgegeben wird.
$dishId  = $_POST['dish_id'] ?? null;
$rating  = $_POST['rating']   ?? null;
$comment = $_POST['comment']  ?? '';

// 3) Einfacher Plausibilitätscheck
// Ohne dishId oder rating macht eine Bewertung keinen Sinn.
// Falls diese Werte fehlen, bricht das Skript ab.
// 'die' bricht die Verarbeitung des PHP-Codes an dieser Stelle sofort ab. 
if (!$dishId || !$rating) {
    die("Ungültige Eingabe!");
}

// 4) Speicherung der Bewertung in der Tabelle 'ratings'
// Hier wird das SQL-INSERT vorbereitet und die entsprechenden Platzhalter
// für die Nutzereingaben angelegt (:dish_id, :rating, :comment).
// Dies verhindert SQL-Injections, weil eingegeben Kommentare etc als Wert gesehen werden und nicht als SQL-Befehl.
$sql = "INSERT INTO ratings (dish_id, rating, comment)
        VALUES (:dish_id, :rating, :comment)";
$stmt = $pdo->prepare($sql);

// 5) Übergabe der Parameterwerte in execute()
// Indem wir ein Array mit den Werten zu den Platzhaltern liefern, wird
// ein sicherer INSERT durchgeführt (PDO schützt vor SQL-Injection).
$stmt->execute([
    ':dish_id' => $dishId,
    ':rating'  => $rating,
    ':comment' => $comment
]);

// 6) Erfolgsbestätigung
// Hier geben wir dem Nutzer eine einfache Rückmeldung und
// verlinken zurück zur Nutzerseite, damit er weiter bewerten kann.
echo "Bewertung erfolgreich gespeichert!<br>";
echo "<a href='index.php'>Zurück zur Nutzerseite</a>";
