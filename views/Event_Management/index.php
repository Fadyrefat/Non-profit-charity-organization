<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Event Department</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f9f9f9;
      padding: 40px;
      text-align: center;
    }
    h1 {
      font-size: 2.2em;
      margin-bottom: 30px;
      color: #2c3e50;
    }
    .btn {
      display: inline-block;
      padding: 12px 20px;
      margin: 10px 0;
      font-size: 1.1em;
      color: #fff;
      background-color: #3498db;
      border-radius: 8px;
      text-decoration: none;
      transition: background-color 0.3s;
    }
    .btn:hover {
      background-color: #2980b9;
    }
    .event-card {
      background: #fff;
      padding: 20px;
      margin: 15px auto;
      border-radius: 10px;
      max-width: 600px;
      text-align: left;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .event-title {
      font-size: 1.4em;
      color: #2c3e50;
      margin-bottom: 10px;
    }
    .event-info {
      color: #555;
      margin-bottom: 8px;
    }
  </style>
</head>
<body>
  <h1>Event Department</h1>
  <a href="index.php?action=createEvent" class="btn">➕ Create New Event</a>

  <?php foreach ($events as $e): ?>
    <div class="event-card">
      <div class="event-title"><?= htmlspecialchars($e['title']) ?> (<?= htmlspecialchars($e['type']) ?>)</div>
      <div class="event-info">📍 <?= htmlspecialchars($e['location']) ?></div>
      <div class="event-info">🕒 <?= htmlspecialchars($e['start_datetime']) ?></div>
      <a href="index.php?action=showEvent&id=<?= $e['id'] ?>" class="btn">View Details</a>
    </div>
  <?php endforeach; ?>

  <p><a href="index.php?action=home" class="btn">⬅ Back Home</a></p>
</body>
</html>
