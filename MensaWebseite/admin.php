<?php
// ***************************************
// admin.php
// ***************************************
//
// Dieses Skript stellt eine Administrationsoberfläche bereit,
// um neue Gerichte in die Datenbank einzutragen und bestimmte
// Gerichte für eine Bewertung (Featured-Gerichte) freizuschalten.
//
// ***************************************

// === Datenbankverbindung aufbauen ===
// Erstellt ein PDO-Objekt für eine SQLite-Datenbank im selben Verzeichnis.
// setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION) sorgt dafür,
// dass PDO bei Fehlern eine Exception wirft, statt nur Warnungen auszugeben.
$pdo = new PDO('sqlite:' . __DIR__ . '/database.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ***************************************
// === 1) Verarbeitung: Gericht hinzufügen ===
// Wird nur ausgeführt, wenn in der URL der GET-Parameter ?action=addDish gesetzt ist.
if (isset($_GET['action']) && $_GET['action'] === 'addDish') {
    // Liest die Formulardaten aus POST-Variablen aus (mit Null-Coalescing-Operator bei nicht vorhandenem Index).
    $name      = $_POST['name']      ?? '';
    $kalorien  = $_POST['kalorien']  ?? '';
    $allergene = $_POST['allergene'] ?? '';
    $preis     = $_POST['preis']     ?? '';

    // Überprüfung der Pflichtfelder: Name und Preis dürfen nicht leer sein.
    if (!empty($name) && !empty($preis)) {
        // SQL-Insert-Anweisung zum Hinzufügen eines neuen Gerichts in die Tabelle 'dishes'.
        // featured = 0 bedeutet, dass das Gericht nicht automatisch in der Bewertung erscheint.
        $sql = "INSERT INTO dishes (name, kalorien, allergene, preis, featured)
                VALUES (:name, :kalorien, :allergene, :preis, 0)";
        $stmt = $pdo->prepare($sql);

        // Binden der Parameter mit execute(), um SQL-Injections zu vermeiden.
        $stmt->execute([
            ':name'      => $name,
            ':kalorien'  => $kalorien,
            ':allergene' => $allergene,
            ':preis'     => $preis
        ]);

        // Erfolgsausgabe nach dem Einfügen des Datensatzes.
        echo "Gericht erfolgreich hinzugefügt!<br>";
    } else {
        // Fehlermeldung, falls Name oder Preis nicht ausgefüllt wurden.
        echo "Name und Preis sind Pflichtfelder!<br>";
    }
}

// ***************************************
// === 2) Verarbeitung: Featured-Dishes setzen ===
// Wird nur ausgeführt, wenn ?action=setFeatured in der URL steht.
if (isset($_GET['action']) && $_GET['action'] === 'setFeatured') {
    // 1. Setzt erst einmal ALLE Gerichte auf 'featured = 0',
    // damit später nur die ausgewählten angehakt sein können.
    $pdo->exec("UPDATE dishes SET featured = 0");

    // 2. Überprüft, ob im POST-Array Gerichte ausgewählt wurden.
    // Wenn ja, setzt das Skript für jedes ausgewählte Gericht 'featured = 1'.
    if (!empty($_POST['selectedDishes'])) {
        foreach ($_POST['selectedDishes'] as $dishId) {
            $sql = "UPDATE dishes SET featured = 1 WHERE dish_id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $dishId]);
        }
        // Meldung über erfolgreiche Aktualisierung der Featured-Gerichte.
        echo "Featured-Gerichte aktualisiert!<br>";
    } else {
        // Meldung, wenn keine Gerichte angehakt waren.
        echo "Keine Gerichte ausgewählt!<br>";
    }
}

// ***************************************
// === Anzeige-Teil (HTML) ===
// Hier folgt das HTML-Gerüst der Admin-Seite.
// Es enthält zwei Hauptbereiche:
// 1) Formular zum Hinzufügen neuer Gerichte
// 2) Formular zum Setzen/Aktualisieren der Featured-Gerichte
// ***************************************
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Admin-Seite</title>
</head>
<body>
    <h1>Admin - Mensa Projekt</h1>

    <!-- Formularbereich 1: Neues Gericht hinzufügen -->
    <h2>Neues Gericht hinzufügen</h2>
    <form action="admin.php?action=addDish" method="post">
        <label for="name">Gericht:</label><br>
        <input type="text" id="name" name="name" required><br><br>

        <label for="kalorien">Kalorien:</label><br>
        <input type="number" id="kalorien" name="kalorien"><br><br>

        <label for="allergene">Allergene:</label><br>
        <input type="text" id="allergene" name="allergene"><br><br>

        <label for="preis">Preis:</label><br>
        <input type="number" step="0.01" id="preis" name="preis" required><br><br>

        <button type="submit">Gericht hinzufügen</button>
    </form>

    <hr>

    <!-- Formularbereich 2: Gerichte als "Featured" markieren -->
    <h2>Gerichte zur Bewertung freischalten</h2>
    <form action="admin.php?action=setFeatured" method="post">
        <?php
        // Liest alle Gerichte aus der Datenbank (ID, Name, Featured-Status).
        $allDishes = $pdo->query("SELECT dish_id, name, featured FROM dishes")->fetchAll(PDO::FETCH_ASSOC);

        // Wenn keine Gerichte existieren, wird eine entsprechende Nachricht ausgegeben.
        if (!$allDishes) {
            echo "<p>Noch keine Gerichte in der Datenbank!</p>";
        } else {
            // Info-Text: maximal drei Gerichte können als Featured ausgewählt werden (keine Prüflogik hier).
            echo "<p>Wähle bis zu drei Gerichte, die Nutzer bewerten sollen:</p>";

            // Für jedes Gericht: Checkbox zum Auswählen,
            // 'checked' wird angezeigt, wenn featured = 1.
            foreach ($allDishes as $dish) {
                $checked = ($dish['featured'] == 1) ? 'checked' : '';
                echo '<label>';
                echo '<input type="checkbox" name="selectedDishes[]" value="' . $dish['dish_id'] . '" ' . $checked . '>';
                echo htmlspecialchars($dish['name']);
                echo '</label><br>';
            }

            // Button zum Absenden der Auswahl.
            echo '<br><button type="submit">Auswahl aktualisieren</button>';
        }
        ?>
    </form>
</body>
</html>
