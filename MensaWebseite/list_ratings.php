<?php
$pdo = new PDO('sqlite:' . __DIR__ . '/database.db');
$stmt = $pdo->query("SELECT * FROM mensa_ratings");
$ratings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Bewertungen</title>
</head>
<body>
  <h1>Alle Bewertungen</h1>
  <table border="1">
    <tr>
      <th>ID</th>
      <th>Gericht</th>
      <th>Bewertung</th>
      <th>Kommentar</th>
      <th>Erstellt am</th>
    </tr>
    <?php foreach ($ratings as $r): ?>
      <tr>
        <td><?= $r['id'] ?></td>
        <td><?= htmlspecialchars($r['dish_name']) ?></td>
        <td><?= $r['rating'] ?></td>
        <td><?= htmlspecialchars($r['comment']) ?></td>
        <td><?= $r['created_at'] ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
</body>
</html>