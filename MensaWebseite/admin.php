<?php
// admin.php

$pdo = new PDO('sqlite:' . __DIR__ . '/database.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// --- Verarbeitung: Gericht hinzufügen ---
if (isset($_GET['action']) && $_GET['action'] === 'addDish') {
    $name      = $_POST['name']      ?? '';
    $kalorien  = $_POST['kalorien']  ?? '';
    $allergene = $_POST['allergene'] ?? '';
    $preis     = $_POST['preis']     ?? '';

    if (!empty($name) && !empty($preis)) {
        $sql = "INSERT INTO dishes (name, kalorien, allergene, preis, featured)
                VALUES (:name, :kalorien, :allergene, :preis, 0)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name'      => $name,
            ':kalorien'  => $kalorien,
            ':allergene' => $allergene,
            ':preis'     => $preis
        ]);
        echo "Gericht erfolgreich hinzugefügt!<br>";
    } else {
        echo "Name und Preis sind Pflichtfelder!<br>";
    }
}

// --- Verarbeitung: Featured-Dishes setzen ---
if (isset($_GET['action']) && $_GET['action'] === 'setFeatured') {
    // Alle auf 0 setzen
    $pdo->exec("UPDATE dishes SET featured = 0");

    // Die angehakten Gerichte auf 1 setzen
    if (!empty($_POST['selectedDishes'])) {
        foreach ($_POST['selectedDishes'] as $dishId) {
            $sql = "UPDATE dishes SET featured = 1 WHERE dish_id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $dishId]);
        }
        echo "Featured-Gerichte aktualisiert!<br>";
    } else {
        echo "Keine Gerichte ausgewählt!<br>";
    }
}

// -----------------------------------------
// Anzeige-Teil (HTML):
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Admin-Seite</title>
</head>
<body>
    <h1>Admin - Mensa Projekt</h1>

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

    <h2>Gerichte zur Bewertung freischalten</h2>
    <form action="admin.php?action=setFeatured" method="post">
        <?php
        // Alle Gerichte laden
        $allDishes = $pdo->query("SELECT dish_id, name, featured FROM dishes")->fetchAll(PDO::FETCH_ASSOC);

        if (!$allDishes) {
            echo "<p>Noch keine Gerichte in der Datenbank!</p>";
        } else {
            echo "<p>Wähle bis zu drei Gerichte, die Nutzer bewerten sollen:</p>";
            foreach ($allDishes as $dish) {
                // Checkbox ist angehakt, wenn featured = 1
                $checked = ($dish['featured'] == 1) ? 'checked' : '';
                echo '<label>';
                echo '<input type="checkbox" name="selectedDishes[]" value="' . $dish['dish_id'] . '" ' . $checked . '>';
                echo htmlspecialchars($dish['name']);
                echo '</label><br>';
            }
            echo '<br><button type="submit">Auswahl aktualisieren</button>';
        }
        ?>
    </form>
</body>
</html>