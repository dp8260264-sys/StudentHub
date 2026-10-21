<?php
$menuRole = strtolower(trim((string)($_SESSION['role'] ?? 'student')));
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="../index.php">StudentHub</a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainMenu">
            <ul class="navbar-nav me-auto">

                <?php if ($menuRole === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="dashboard.php">Dashboard</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link"
                           href="events.php">Manage Events</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link"
                           href="audit-logs.php">Audit Logs</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="../index.php">Home</a>
                    </li>
                <?php endif; ?>

            </ul>

            <span class="navbar-text text-white me-3">
                Role: <?= htmlspecialchars($menuRole) ?>
            </span>

            <a href="../logout.php" class="btn btn-outline-light btn-sm">
                Logout
            </a>
        </div>
    </div>
</nav>