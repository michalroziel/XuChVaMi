<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Tabellenübersicht</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Tabellenübersicht der SQLite-Datenbank</h1>
    <?php
    try {
        // Verbindung zur SQLite-Datenbank herstellen
        $pdo = new PDO('sqlite:' . __DIR__ . '/database.db');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Alle Tabellen aus der Datenbank abrufen
        $sql = "SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'";
        $stmt = $pdo->query($sql);
        $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$tables) {
            echo "<p>Keine Tabellen in der Datenbank gefunden.</p>";
        } else {
            foreach ($tables as $table) {
                $tableName = $table['name'];
                echo "<h2>Tabelle: $tableName</h2>";

                if ($tableName === "ratings") {
                    // Spezielle Abfrage für die Tabelle "ratings", um den Namen des Gerichts zu holen
                    $dataStmt = $pdo->query("
                        SELECT r.rating_id, r.dish_id, d.name AS dish_name, r.rating, r.comment, r.created_at
                        FROM ratings r
                        JOIN dishes d ON r.dish_id = d.dish_id
                    ");
                } else {
                    // Normale Abfrage für andere Tabellen
                    $dataStmt = $pdo->query("SELECT * FROM $tableName");
                }

                $rows = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

                if (!$rows) {
                    echo "<p>Die Tabelle $tableName ist leer.</p>";
                } else {
                    // Spaltennamen abrufen
                    $columns = array_keys($rows[0]);

                    echo "<table>";
                    echo "<thead><tr>";
                    foreach ($columns as $column) {
                        echo "<th>" . htmlspecialchars($column) . "</th>";
                    }
                    echo "</tr></thead>";
                    echo "<tbody>";
                    foreach ($rows as $row) {
                        echo "<tr>";
                        foreach ($row as $value) {
                            echo "<td>" . htmlspecialchars($value) . "</td>";
                        }
                        echo "</tr>";
                    }
                    echo "</tbody></table>";
                }
            }
        }
    } catch (PDOException $e) {
        echo "<p>Fehler: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    ?>
</body>
</html>
