
<?php
require_once __DIR__ . '/../config/database.php';

$search = trim($_GET['search'] ?? '');
$course = trim($_GET['course'] ?? '');
$status = $_GET['status'] ?? 'active';

if (!in_array($status, ['active', 'inactive', 'all'], true)) {
    $status = 'active';
}

$where = " WHERE 1=1 ";
$params = [];
$types = "";

if ($search !== '') {
    $where .= " AND (full_name LIKE ? OR email LIKE ? OR course LIKE ?) ";
    $like = "%$search%";
    $params = array_merge($params, [$like, $like, $like]);
    $types .= "sss";
}

if ($course !== '') {
    $where .= " AND course = ? ";
    $params[] = $course;
    $types .= "s";
}

if ($status !== 'all') {
    $where .= " AND status = ? ";
    $params[] = $status;
    $types .= "s";
}

$stmt = $conn->prepare(
    "SELECT id, full_name, email, mobile, course,
            year_of_study, status, created_at
     FROM students $where ORDER BY id DESC"
);

if ($types !== '') {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="studenthub-students.csv"');

$output = fopen('php://output', 'w');

// UTF-8 BOM helps Excel display characters correctly.
fwrite($output, "\xEF\xBB\xBF");

fputcsv($output, [
    'ID', 'Full Name', 'Email', 'Mobile',
    'Course', 'Year', 'Status', 'Created At'
]);

while ($row = $result->fetch_assoc()) {
    // Reduce spreadsheet formula-injection risk in text cells.
    foreach (['full_name', 'email', 'mobile', 'course', 'status'] as $key) {
        if (preg_match('/^[\s]*[=+\-@\t\r]/', $row[$key])) {
            $row[$key] = "'" . $row[$key];
        }
    }

    fputcsv($output, [
        $row['id'],
        $row['full_name'],
        $row['email'],
        $row['mobile'],
        $row['course'],
        $row['year_of_study'],
        $row['status'],
        $row['created_at']
    ]);
}

fclose($output);
exit;
?>
