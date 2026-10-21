
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

// Record dashboard visit if audit logging is configured.
try {
    $action = 'VIEW_DASHBOARD';
    $details = 'Admin opened the dashboard';
    $userId = (int) $_SESSION['admin_id'];
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';

    $stmt = $conn->prepare(
        'INSERT INTO audit_logs (user_id, action, details, ip_address)
         VALUES (?, ?, ?, ?)'
    );
    $stmt->bind_param('isss', $userId, $action, $details, $ip);
    $stmt->execute();
    $stmt->close();
} catch (Throwable $e) {
    // Dashboard can still open if audit logging is not configured yet.
}

// Count only tables that exist in this database.
function countTable(mysqli $conn, string $table): ?int {
    $allowed = ['admin_users', 'events_pr12', 'students', 'registrations'];

    if (!in_array($table, $allowed, true)) {
        return null;
    }

    try {
        $result = $conn->query("SELECT COUNT(*) AS total FROM `$table`");
        $row = $result->fetch_assoc();
        return (int) $row['total'];
    } catch (Throwable $e) {
        return null;
    }
}

$adminCount = countTable($conn, 'admin_users');
$eventCount = countTable($conn, 'events_pr12');
$studentCount = countTable($conn, 'students');
$registrationCount = countTable($conn, 'registrations');

$adminName = $_SESSION['admin_name'] ?? 'Administrator';

// Dynamic menu based on role.
$menu = [
    ['label' => 'Dashboard', 'url' => 'dashboard.php', 'icon' => '🏠']
];

if (($_SESSION['role'] ?? '') === 'admin') {
    $menu[] = ['label' => 'Manage Events', 'url' => 'events.php', 'icon' => '📅'];
    $menu[] = ['label' => 'Audit Logs', 'url' => 'audit-logs.php', 'icon' => '📋'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>StudentHub | Admin Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        body {
            background: #f4f6fb;
            font-family: Arial, sans-serif;
        }
        .sidebar {
            min-height: 100vh;
            background: #172554;
        }
        .brand {
            color: #fff;
            font-size: 22px;
            font-weight: bold;
        }
        .sidebar a {
            display: block;
            color: #dbeafe;
            text-decoration: none;
            padding: 12px 14px;
            margin: 6px 0;
            border-radius: 8px;
        }
        .sidebar a:hover,
        .sidebar a.active {
            background: #2563eb;
            color: #fff;
        }
        .topbar, .stat-card, .content-card {
            background: #fff;
            border: 0;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(15, 23, 42, .06);
        }
        .stat-card {
            padding: 22px;
            height: 100%;
        }
        .stat-number {
            font-size: 30px;
            font-weight: bold;
            color: #172554;
        }
        .stat-label {
            color: #64748b;
            font-size: 14px;
        }
        .stat-icon {
            font-size: 27px;
        }
        .main-content {
            padding: 28px;
        }
        @media (max-width: 767px) {
            .sidebar { min-height: auto; }
            .main-content { padding: 16px; }
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">

        <!-- Sidebar / Dynamic Navigation -->
        <aside class="col-md-3 col-lg-2 sidebar p-3">
            <div class="brand mb-4">🎓 StudentHub</div>
            <p class="text-uppercase small text-white-50">Main Menu</p>

            <?php foreach ($menu as $item): ?>
                <a
                    class="<?= $item['label'] === 'Dashboard' ? 'active' : '' ?>"
                    href="<?= h($item['url']) ?>"
                >
                    <?= $item['icon'] ?> <?= h($item['label']) ?>
                </a>
            <?php endforeach; ?>

            <hr class="border-secondary mt-4">
            <a href="logout.php">🚪 Logout</a>

            <div class="text-white-50 small mt-4">
                Role-Based Access Control enabled
            </div>
        </aside>

        <!-- Main Dashboard -->
        <main class="col-md-9 col-lg-10 main-content">

            <div class="topbar p-3 mb-4 d-flex justify-content-between
                        align-items-center flex-wrap gap-2">
                <div>
                    <h1 class="h4 mb-1">Admin Dashboard</h1>
                    <p class="text-secondary mb-0">
                        Welcome back, <?= h($adminName) ?>!
                    </p>
                </div>

                <span class="badge text-bg-primary p-2">
                    Administrator
                </span>
            </div>

            <h2 class="h5 mb-3">Dashboard Overview</h2>

            <div class="row g-3 mb-4">

                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between">
                            <span class="stat-icon">👤</span>
                            <span class="badge text-bg-light">Accounts</span>
                        </div>
                        <div class="stat-number">
                            <?= $adminCount === null ? 'N/A' : $adminCount ?>
                        </div>
                        <div class="stat-label">Admin Accounts</div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between">
                            <span class="stat-icon">📅</span>
                            <span class="badge text-bg-light">Events</span>
                        </div>
                        <div class="stat-number">
                            <?= $eventCount === null ? 'N/A' : $eventCount ?>
                        </div>
                        <div class="stat-label">StudentHub Events</div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between">
                            <span class="stat-icon">🎓</span>
                            <span class="badge text-bg-light">Students</span>
                        </div>
                        <div class="stat-number">
                            <?= $studentCount === null ? 'N/A' : $studentCount ?>
                        </div>
                        <div class="stat-label">Registered Students</div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between">
                            <span class="stat-icon">📝</span>
                            <span class="badge text-bg-light">Records</span>
                        </div>
                        <div class="stat-number">
                            <?= $registrationCount === null ? 'N/A' : $registrationCount ?>
                        </div>
                        <div class="stat-label">Registrations</div>
                    </div>
                </div>

            </div>

            <div class="content-card p-4 mb-4">
                <h2 class="h5">Role-Based Access Control</h2>
                <p class="text-secondary">
                    Your current role is <strong>Administrator</strong>.
                    Protected dashboard access requires a valid admin session.
                </p>
                <div class="d-flex gap-2 flex-wrap">
                    <span class="badge text-bg-success">Authenticated</span>
                    <span class="badge text-bg-primary">Admin Role</span>
                    <span class="badge text-bg-secondary">Session Active</span>
                </div>
            </div>

            <div class="content-card p-4">
                <div class="d-flex justify-content-between align-items-center
                            flex-wrap gap-2">
                    <div>
                        <h2 class="h5">Audit Log</h2>
                        <p class="text-secondary mb-0">
                            Review recorded administrator activities.
                        </p>
                    </div>
                    <a href="audit-logs.php" class="btn btn-primary">
                        View Audit Logs
                    </a>
                </div>
            </div>

            <p class="text-secondary small mt-4 mb-0">
                StudentHub Admin Panel &copy; <?= date('Y') ?>
            </p>

        </main>
    </div>
</div>

</body>
</html>
