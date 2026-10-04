<?php

require_once "db.php";

$sql = "
    SELECT
        registrations.registration_id,
        students.name AS student_name,
        students.email,
        events.event_name,
        events.event_date,
        registrations.registration_date
    FROM registrations
    INNER JOIN students
        ON registrations.student_id = students.student_id
    INNER JOIN events
        ON registrations.event_id = events.event_id
    ORDER BY registrations.registration_id
";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$registrations = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html>

<head>
    <title>StudentHub Registrations</title>

    <style>
        table {
            border-collapse: collapse;
            width: 90%;
        }

        th, td {
            border: 1px solid black;
            padding: 10px;
        }

        th {
            background-color: #eeeeee;
        }
    </style>
</head>

<body>

<h1>StudentHub Event Registrations</h1>

<table>

<tr>
    <th>ID</th>
    <th>Student</th>
    <th>Email</th>
    <th>Event</th>
    <th>Event Date</th>
    <th>Registration Date</th>
</tr>

<?php foreach ($registrations as $row): ?>

<tr>

<td>
    <?= htmlspecialchars($row['registration_id']) ?>
</td>

<td>
    <?= htmlspecialchars($row['student_name']) ?>
</td>

<td>
    <?= htmlspecialchars($row['email']) ?>
</td>

<td>
    <?= htmlspecialchars($row['event_name']) ?>
</td>

<td>
    <?= htmlspecialchars($row['event_date']) ?>
</td>

<td>
    <?= htmlspecialchars($row['registration_date']) ?>
</td>

</tr>

<?php endforeach; ?>

</table>

</body>
</html>