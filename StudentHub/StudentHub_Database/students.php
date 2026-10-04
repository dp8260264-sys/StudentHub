<?php

require_once "db.php";

$email = "drashti@example.com";

$sql = "SELECT * FROM students WHERE email = ?";

$stmt = $pdo->prepare($sql);

$stmt->execute([$email]);

$student = $stmt->fetch();

?>

<!DOCTYPE html>
<html>
<head>
    <title>StudentHub - Student Search</title>
</head>

<body>

<h1>Student Search</h1>

<?php

if ($student) {

    echo "<p><strong>Student ID:</strong> " . htmlspecialchars($student['student_id']) . "</p>";

    echo "<p><strong>Name:</strong> " . htmlspecialchars($student['name']) . "</p>";

    echo "<p><strong>Email:</strong> " . htmlspecialchars($student['email']) . "</p>";

    echo "<p><strong>Course:</strong> " . htmlspecialchars($student['course']) . "</p>";

    echo "<p><strong>Semester:</strong> " . htmlspecialchars($student['semester']) . "</p>";

} else {

    echo "<p>Student not found.</p>";

}

?>

</body>
</html>