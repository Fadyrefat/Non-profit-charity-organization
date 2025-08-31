<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Event</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f9f9f9;
      padding: 40px;
      text-align: center;
    }

    h1 {
      font-size: 2em;
      margin-bottom: 20px;
      color: #2c3e50;
    }

    form {
      background: #fff;
      max-width: 500px;
      margin: 0 auto;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
      text-align: left;
    }

    label {
      font-weight: bold;
      display: block;
      margin-top: 15px;
      color: #2c3e50;
    }

    input,
    select,
    textarea {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }

    button {
      margin-top: 20px;
      padding: 12px 20px;
      background-color: #3498db;
      color: #fff;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 1.1em;
    }

    button:hover {
      background-color: #2980b9;
    }

    .btn {
      display: inline-block;
      padding: 10px 16px;
      margin-top: 10px;
      font-size: 1em;
      color: #fff;
      background-color: #3498db;
      border-radius: 6px;
      text-decoration: none;
      transition: background-color 0.3s;
    }

    .btn:hover {
      background-color: #2980b9;
    }
  </style>
</head>

<body>
  <h1>Create New Event</h1>
  <form action="index.php?action=storeEvent" method="POST">
    <label>Title</label>
    <input type="text" name="title" required>

    <label>Type</label>
    <select name="type">
      <option value="workshop">Workshop</option>
      <option value="fundraiser">Fundraiser</option>
      <option value="outreach">Outreach</option>
    </select>

    <label>Description</label>
    <textarea name="description" rows="4"></textarea>

    <label>Start Date & Time</label>
    <input type="datetime-local" name="start_datetime" required>

    <label>End Date & Time</label>
    <input type="datetime-local" name="end_datetime">

    <label>Capacity</label>
    <input type="number" name="capacity" min="0">

    <label>Location</label>
    <input type="text" name="location">

    <button type="submit">Create Event</button>
  </form>

  <p><a href="index.php?action=EventDepartment" class="btn">⬅ Back to Events</a></p>
</body>

</html>