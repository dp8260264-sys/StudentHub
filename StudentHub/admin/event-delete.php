
<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: events.php');
    exit;
}

// Verify the CSRF token.
$token = $_POST['csrf'] ?? '';
$sessionToken = $_SESSION['event_csrf'] ?? '';

if (
    !is_string($token) ||
    !is_string($sessionToken) ||
    $sessionToken === '' ||
    !hash_equals($sessionToken, $token)
) {
    http_response_code(403);
    exit('Invalid request. Return to the events page and try again.');
}

// Validate event ID.
$id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header('Location: events.php');
    exit;
}

try {
    // Get the poster filename before deleting the event.
    $stmt = $conn->prepare(
        'SELECT poster FROM events_pr12 WHERE id = ?'
    );
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $event = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$event) {
        header('Location: events.php');
        exit;
    }

    // Delete from the same table used by events.php.
    $stmt = $conn->prepare(
        'DELETE FROM events_pr12 WHERE id = ?'
    );
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $deleted = $stmt->affected_rows;
    $stmt->close();

    // Delete the poster only if the event was deleted successfully.
    if ($deleted > 0 && !empty($event['poster'])) {
        $posterPath = __DIR__ . '/../uploads/event-posters/'
            . basename($event['poster']);

        if (is_file($posterPath)) {
            unlink($posterPath);
        }
    }

    header('Location: events.php');
    exit;

} catch (Throwable $e) {
    error_log('Event deletion failed: ' . $e->getMessage());
    http_response_code(500);
    exit('Could not delete the event. Please check the database and try again.');
}
