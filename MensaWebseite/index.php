<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Mensa Rating - Nutzerseite</title>
</head>
<body>
  <h1>Heute zur Bewertung freigegebene Gerichte</h1>

  <?php
  // --------------------------------------------------------------
  // 1) Datenbankverbindung mittels PDO aufbauen
  // --------------------------------------------------------------
  // Hier verbinden wir uns mit der SQLite-Datenbank, die in
  // demselben Verzeichnis (__DIR__) liegt wie diese Datei.
  // Der Modus ERRMODE_EXCEPTION wirft bei Fehlern Ausnahmeobjekte,
  // was das Debugging einfacher macht.
  $pdo = new PDO('sqlite:' . __DIR__ . '/database.db');
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // --------------------------------------------------------------
  // 2) Featured-Gerichte laden
  // --------------------------------------------------------------
  // In der Tabelle 'dishes' gibt es eine Spalte 'featured',
  // die anzeigt, ob ein Gericht gerade zur Bewertung freigegeben ist (1)
  // oder nicht (0). Wir holen alle Gerichte mit featured = 1 ab.
  $sql = "SELECT dish_id, name FROM dishes WHERE featured = 1";
  $stmt = $pdo->query($sql);
  $featuredDishes = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // --------------------------------------------------------------
  // 3) Prüfen, ob es überhaupt Gerichte zum Bewerten gibt
  // --------------------------------------------------------------
  // Wenn das Array $featuredDishes leer ist (also keine Datensätze),
  // wird ein Hinweis ausgegeben, dass aktuell keine Gerichte da sind.
  if (!$featuredDishes) {
      echo "<p>Aktuell sind keine Gerichte zur Bewertung freigeschaltet.</p>";
  } else {
      // Wenn es Featured-Gerichte gibt, zeigen wir das Formular an.
  ?>

    <!--
      4) Bewertungsformular
      Dieses Formular ermöglicht es den Nutzern, eines der freigeschalteten
      Gerichte (featured = 1) auszuwählen, eine Bewertung (Zahl zwischen 1 und 10)
      sowie einen optionalen Kommentar abzugeben.
      Der Absende-Button schickt die Daten an save_rating.php (POST).
    -->
    <form action="save_rating.php" method="post">
      <!-- Dropdown-Liste: Alle verfügbaren Featured-Gerichte -->
      <label for="dish">Gericht auswählen:</label><br>
      <select name="dish_id" id="dish" required>
        <?php foreach ($featuredDishes as $dish): ?>
          <!--
            dish_id wird als Value mitgesendet,
            die Namen der Gerichte werden als sichtbarer Text (Option-Text) ausgegeben.
            htmlspecialchars() sorgt dafür, dass Sonderzeichen sicher ausgegeben werden.
          -->
          <option value="<?= $dish['dish_id'] ?>">
            <?= htmlspecialchars($dish['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <br><br>

      <!-- Bewertungsfeld für die Nutzer (1-10) -->
      <label for="rating">Bewertung (1-10):</label><br>
      <input type="number" id="rating" name="rating" min="1" max="10" required><br><br>

      <!-- Kommentar ist optional: Textarea für Freitext -->
      <label for="comment">Kommentar (optional):</label><br>
      <textarea id="comment" name="comment" rows="4" cols="50"></textarea><br><br>

      <!-- Absende-Button für das Formular -->
      <button type="submit">Bewerten</button>
    </form>

  <?php
  } // Ende if(!$featuredDishes)
  ?>
</body>
</html>
