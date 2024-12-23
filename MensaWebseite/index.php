<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Mensa Rating - Nutzerseite</title>
</head>
<body>
  <h1>Heute zur Bewertung freigegebene Gerichte</h1>

  <?php
 

  // Verbindungsaufbau (ggf. in separate Datei auslagern):
  $pdo = new PDO('sqlite:' . __DIR__ . '/database.db');
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Gerichte laden, die auf "featured" = 1 stehen
  // (siehe admin.php unten, wo wir featured auf 1 oder 0 setzen)
  $sql = "SELECT dish_id, name FROM dishes WHERE featured = 1";
  $stmt = $pdo->query($sql);
  $featuredDishes = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Wenn es keine featured Gerichte gibt:
  if (!$featuredDishes) {
      echo "<p>Aktuell sind keine Gerichte zur Bewertung freigeschaltet.</p>";
  } else {
  ?>

    <form action="save_rating.php" method="post">
      <label for="dish">Gericht auswählen:</label><br>
      <select name="dish_id" id="dish" required>
        <?php foreach ($featuredDishes as $dish): ?>
          <option value="<?= $dish['dish_id'] ?>">
            <?= htmlspecialchars($dish['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <br><br>

      <label for="rating">Bewertung (1-10):</label><br>
      <input type="number" id="rating" name="rating" min="1" max="10" required><br><br>

      <label for="comment">Kommentar (optional):</label><br>
      <textarea id="comment" name="comment" rows="4" cols="50"></textarea><br><br>

      <button type="submit">Bewerten</button>
    </form>

  <?php
  } // Ende if(!$featuredDishes)
  ?>
</body>
</html>