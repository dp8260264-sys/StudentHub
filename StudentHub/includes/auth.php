<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireLogin(): void
{
    if (empty($_SESSION['user_id'])) {
        header('Location: ../login.php');
        exit;
    }
}

function requireAdmin(): void
{
    requireLogin();

    $role = strtolower(trim((string)($_SESSION['role'] ?? '')));

    if ($role !== 'admin') {
        http_response_code(403);
        exit(
            '<h2>403 - Access Denied</h2>' .
            '<p>Only administrators can access this page.</p>' .
            '<a href="../index.php">Return to Home</a>'
        );
    }
}

function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}