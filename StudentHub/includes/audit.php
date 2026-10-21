<?php

function logAdminAction(
    mysqli $conn,
    string $action,
    string $details = ''
): void {
    $userId = isset($_SESSION['user_id'])
        ? (int)$_SESSION['user_id']
        : null;

    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';

    $stmt = $conn->prepare(
        "INSERT INTO audit_logs
         (user_id, action, details, ip_address)
         VALUES (?, ?, ?, ?)"
    );

    if (!$stmt) {
        error_log('Audit log prepare failed: ' . $conn->error);
        return;
    }

    $stmt->bind_param(
        'isss',
        $userId,
        $action,
        $details,
        $ipAddress
    );

    if (!$stmt->execute()) {
        error_log('Audit log insert failed: ' . $stmt->error);
    }

    $stmt->close();
}