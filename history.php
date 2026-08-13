<?php
require_once "db.php";
$stmt = $pdo->query("SELECT * FROM explanations ORDER BY created_at DESC LIMIT 20");
$rows = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><title>History</title><link rel="stylesheet" href="style.css"></head>
<body>
<header><h1>History</h1><a href="index.php">Back</a></header>
<main>
<?php foreach ($rows as $row): ?>
    <div class="card">
        <p><strong>Language:</strong> <?= htmlspecialchars($row["language"]) ?></p>
        <pre><?= htmlspecialchars(substr($row["code"], 0, 200)) ?></pre>
        <p><?= htmlspecialchars($row["explanation"]) ?></p>
        <small><?= $row["created_at"] ?></small>
    </div>
<?php endforeach; ?>
</main>
</body>
</html>