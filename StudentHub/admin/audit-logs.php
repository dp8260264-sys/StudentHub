
<?php
session_start();

require_once __DIR__ . '/../config/database.php';

if (
    empty($_SESSION['admin_id']) ||
    ($_SESSION['role'] ?? '') !== 'admin'
) {
    header('Location: login.php');
    exit;
}

function h($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$logs = [];
$error = '';

try {
    $result = $conn->query(
        "SELECT id, user_id, action, details, ip_address, created_at
         FROM audit_logs
         ORDER BY id DESC
         LIMIT 100"
    );

    while ($row = $result->fetch_assoc()) {
        $logs[] = $row;
    }
} catch (Throwable $e) {
    $error = 'Audit log table not found. Please create the audit_logs table in phpMyAdmin.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>StudentHub Audit Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark px-4">
    <a class="navbar-brand" href="dashboard.php">StudentHub Admin</a>
    <div class="d-flex gap-3">
        <a class="text-white" href="dashboard.php">Dashboard</a>
        <a class="text-white" href="audit-logs.php">Audit Logs</a>
        <a class="text-white" href="logout.php">Logout</a>
    </div>
</nav>

<div class="container py-4">
    <h1 class="h3 mb-3">Administrator Audit Logs</h1>
    <p class="text-secondary">Recent activities recorded by the system.</p>

    <?php if ($error !== ''): ?>
        <div class="alert alert-warning"><?= h($error) ?></div>
    <?php else: ?>
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>User ID</th>
                            <th>Action</th>
                            <th>Details</th>
                            <th>IP Address</th>
                            <th>Date &amp; Time</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (count($logs) === 0): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                No audit records found yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?= h($log['id']) ?></td>
                                <td><?= h($log['user_id']) ?></td>
                                <td><?= h($log['action']) ?></td>
                                <td><?= h($log['details']) ?></td>
                                <td><?= h($log['ip_address']) ?></td>
                                <td><?= h($log['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <a href="dashboard.php" class="btn btn-primary mt-3">Back to Dashboard</a>
</div>
</body>
</html>
