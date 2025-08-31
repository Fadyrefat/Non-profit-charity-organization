<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Event Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            padding: 20px;
        }

        h1,
        h2 {
            color: #2c3e50;
        }

        .card {
            background: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #3498db;
            color: #fff;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 10px;
        }

        .btn:hover {
            background: #2980b9;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        select,
        input[type="text"],
        input[type="email"],
        input[type="number"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .checkbox-group label {
            display: inline;
            margin-right: 15px;
            font-weight: normal;
        }
    </style>
</head>

<body>

    <a href="index.php?action=EventDepartment" class="btn">⬅ Back to Events</a>

    <div class="card">
        <h1><?= htmlspecialchars($event['title']) ?></h1>
        <p><strong>Type:</strong> <?= htmlspecialchars($event['type']) ?></p>
        <p><strong>Location:</strong> <?= htmlspecialchars($event['location']) ?></p>
        <p><strong>Start:</strong> <?= htmlspecialchars($event['start_datetime']) ?></p>
        <p><strong>End:</strong> <?= htmlspecialchars($event['end_datetime']) ?></p>
        <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($event['description'])) ?></p>
    </div>

    <div class="card">
        <h2>Attendees</h2>
        <?php if (!empty($attendees)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Type</th>
                        <th>Ticket</th>
                        <th>Checked In</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendees as $a): ?>
                        <tr>
                            <td><?= htmlspecialchars($a['name']) ?></td>
                            <td><?= htmlspecialchars($a['email']) ?></td>
                            <td><?= htmlspecialchars($a['phone']) ?></td>
                            <td><?= htmlspecialchars($a['user_type']) ?></td>
                            <td><?= htmlspecialchars($a['ticket_type']) ?></td>
                            <td><?= $a['checked_in'] ? "✅" : "❌" ?></td>
                            <td>
                                <form action="index.php?action=updateAttendance" method="POST" style="display:inline;">
                                    <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                    <input type="hidden" name="attendee_id" value="<?= $a['id'] ?>">
                                    <input type="hidden" name="checked_in" value="<?= $a['checked_in'] ? 0 : 1 ?>">
                                    <button type="submit" class="btn">
                                        <?= $a['checked_in'] ? "Uncheck" : "Check-in" ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No attendees yet.</p>
        <?php endif; ?>
    </div>

    <div class="card">
        <h2>Register Attendee</h2>
        <form action="index.php?action=registerAttendee" method="POST">
            <input type="hidden" name="event_id" value="<?= $event['id'] ?>">

            <label for="selected_user">Select User</label>
            <select name="selected_user" required>
                <option value="">-- Select User --</option>
                <?php
                $db = Database::getInstance()->getConnection();

                // Donors
                $res = $db->query("SELECT id, email FROM donors");
                while ($row = $res->fetch_assoc()) {
                    echo "<option value='donor:{$row['id']}'>{$row['email']} (donor)</option>";
                }

                // Volunteers
                $res = $db->query("SELECT id, email FROM volunteers");
                while ($row = $res->fetch_assoc()) {
                    echo "<option value='volunteer:{$row['id']}'>{$row['email']} (volunteer)</option>";
                }

                // Beneficiaries
                $res = $db->query("SELECT id, email FROM beneficiaries");
                while ($row = $res->fetch_assoc()) {
                    echo "<option value='beneficiary:{$row['id']}'>{$row['email']} (beneficiary)</option>";
                }
                ?>
            </select>

            <label for="ticket_type">Ticket Type</label>
            <select name="ticket_type" required>
                <option value="General">General</option>
                <option value="VIP">VIP</option>
                <option value="VIP+">VIP+</option>
            </select>

            <label>Reminder Through (Optional):</label>
            <div class="checkbox-group">
                <label><input type="checkbox" name="reminder_methods[]" value="email"> Email</label>
                <label><input type="checkbox" name="reminder_methods[]" value="sms"> SMS</label>
                <label><input type="checkbox" name="reminder_methods[]" value="whatsapp"> WhatsApp</label>
            </div>

            <button type="submit" class="btn">Register Attendee</button>
        </form>
    </div>

    <div class="card">
        <h2>Send Reminder</h2>
        <form action="index.php?action=sendReminder" method="POST">
            <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
            <button type="submit" class="btn">Send Reminder to All Attendees</button>
        </form>
    </div>

</body>

</html>