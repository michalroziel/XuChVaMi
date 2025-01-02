<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Tabellenübersicht</title>
    <style>
        /* Grundlegendes Styling für Tabellen */
        table {
            border-collapse: collapse; /* Doppelte Rahmen entfernen */
            width: 100%;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black; /* Rahmen für Tabelle, Überschriften und Zellen */
        }
        th, td {
            padding: 8px;           /* Innenabstand in den Tabellenzellen */
            text-align: left;       /* Text linksbündig ausrichten */
        }
        th {
            background-color: #f2f2f2; /* Leichte Hinterlegungsfarbe für Tabellenüberschriften */
        }
    </style>
</head>
<body>
    <h1>Tabellenübersicht der SQLite-Datenbank</h1>
    <?php
    try {
        // 1) Aufbau der Verbindung zur lokalen SQLite-Datenbank
        //    __DIR__ verweist auf das aktuelle Verzeichnis dieser Datei.
        $pdo = new PDO('sqlite:' . __DIR__ . '/database.db');
        // PDO wirft Exceptions bei Datenbankfehlern, was Fehlersuche erleichtert.
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 2) Alle Tabellennamen abfragen, die nicht mit 'sqlite_' beginnen.
        //    (sqlite_master ist ein Metadatentabellensatz, in dem Infos zu Tabellen stehen)
        $sql = "SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'";
        $stmt = $pdo->query($sql);
        $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 3) Wenn keine Tabellen gefunden wurden, entsprechende Meldung ausgeben
        if (!$tables) {
            echo "<p>Keine Tabellen in der Datenbank gefunden.</p>";
        } else {
            // 4) Für jede gefundene Tabelle eine Überschrift und tabellarische Auflistung erzeugen
            foreach ($tables as $table) {
                $tableName = $table['name'];
                echo "<h2>Tabelle: $tableName</h2>";

                // Sonderfall: Bei der Tabelle "ratings" holen wir zusätzlich den Gerichtsnamen (dish_name) aus der Tabelle "dishes"
                if ($tableName === "ratings") {
                    $dataStmt = $pdo->query("
                        SELECT r.rating_id, r.dish_id, d.name AS dish_name, r.rating, r.comment, r.created_at
                        FROM ratings r
                        JOIN dishes d ON r.dish_id = d.dish_id
                    ");
                } else {
                    // Für alle anderen Tabellen, einfach alle Inhalte auswählen
                    $dataStmt = $pdo->query("SELECT * FROM $tableName");
                }

                // 5) Alle Datensätze (Zeilen) der aktuellen Tabelle holen
                $rows = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

                // 6) Wenn keine Daten in der Tabelle vorhanden sind, Hinweis ausgeben
                if (!$rows) {
                    echo "<p>Die Tabelle $tableName ist leer.</p>";
                } else {
                    // 7) Spaltennamen (Keys) aus dem ersten Datensatz ableiten,
                    //    um dynamisch eine Tabellenüberschrift zu generieren
                    $columns = array_keys($rows[0]);

                    // HTML-Tabelle aufbauen
                    echo "<table>";
                    echo "<thead><tr>";
                    foreach ($columns as $column) {
                        // Spaltenüberschriften
                        echo "<th>" . htmlspecialchars($column) . "</th>";
                    }
                    echo "</tr></thead>";

                    echo "<tbody>";
                    // Jede Zeile der Tabelle durchlaufen und ausgeben
                    foreach ($rows as $row) {
                        echo "<tr>";
                        foreach ($row as $value) {
                            // Alle Zellen im HTML escapen, um XSS zu vermeiden
                            echo "<td>" . htmlspecialchars($value) . "</td>";
                        }
                        echo "</tr>";
                    }
                    echo "</tbody></table>";
                }
            }
        }
    } catch (PDOException $e) {
        // Ausnahmebehandlung: Falls ein Datenbankfehler auftritt,
        // geben wir hier eine Fehlermeldung aus (über htmlspecialchars).
        echo "<p>Fehler: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    ?>
</body>
</html>
