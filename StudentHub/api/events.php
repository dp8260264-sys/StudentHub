<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';

function sendJson(int $status, array $response): void
{
    http_response_code($status);

    echo json_encode(
        $response,
        JSON_UNESCAPED_UNICODE
    );

    exit;
}

function validDate(string $date): bool
{
    $parsed = DateTime::createFromFormat('!Y-m-d', $date);

    return $parsed !== false
        && $parsed->format('Y-m-d') === $date;
}

try {
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    // GET: List all events or search events
    if ($method === 'GET') {
        $search = trim($_GET['q'] ?? '');

        if ($search !== '') {
            $stmt = $conn->prepare(
                'SELECT id, title, category, event_date,
                        description, status
                 FROM events_pr12
                 WHERE title LIKE ? OR category LIKE ?
                    OR description LIKE ?
                 ORDER BY id DESC'
            );

            $like = '%' . $search . '%';
            $stmt->bind_param('sss', $like, $like, $like);
        } else {
            $stmt = $conn->prepare(
                'SELECT id, title, category, event_date,
                        description, status
                 FROM events_pr12
                 ORDER BY id DESC'
            );
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $events = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        sendJson(200, [
            'success' => true,
            'message' => 'Events fetched successfully.',
            'data' => $events
        ]);
    }

    // Only GET and POST are supported
    if ($method !== 'POST') {
        header('Allow: GET, POST');

        sendJson(405, [
            'success' => false,
            'message' => 'Method not allowed.'
        ]);
    }

    // Read JSON sent by JavaScript Fetch API
    $rawBody = file_get_contents('php://input');
    $input = json_decode($rawBody, true);

    if (!is_array($input)) {
        sendJson(400, [
            'success' => false,
            'message' => 'Invalid JSON request.'
        ]);
    }

    $action = $input['action'] ?? '';

    // DELETE: Delete an event by ID
    if ($action === 'delete') {
        $id = filter_var(
            $input['id'] ?? null,
            FILTER_VALIDATE_INT
        );

        if (!$id || $id < 1) {
            sendJson(422, [
                'success' => false,
                'message' => 'A valid event ID is required.'
            ]);
        }

        $stmt = $conn->prepare(
            'DELETE FROM events_pr12 WHERE id = ?'
        );

        $stmt->bind_param('i', $id);
        $stmt->execute();
        $deleted = $stmt->affected_rows;
        $stmt->close();

        if ($deleted === 0) {
            sendJson(404, [
                'success' => false,
                'message' => 'Event not found.'
            ]);
        }

        sendJson(200, [
            'success' => true,
            'message' => 'Event deleted successfully.'
        ]);
    }

    // CREATE and UPDATE: Validate event information
    if (!in_array($action, ['create', 'update'], true)) {
        sendJson(400, [
            'success' => false,
            'message' => 'Action must be create, update or delete.'
        ]);
    }

    $title = trim($input['title'] ?? '');
    $category = trim($input['category'] ?? '');
    $eventDate = trim($input['event_date'] ?? '');
    $description = trim($input['description'] ?? '');
    $status = $input['status'] ?? 'open';

    if ($title === '' || strlen($title) > 150) {
        sendJson(422, [
            'success' => false,
            'message' => 'Title is required and must be under 150 characters.'
        ]);
    }

    if ($category === '' || strlen($category) > 80) {
        sendJson(422, [
            'success' => false,
            'message' => 'Category is required and must be under 80 characters.'
        ]);
    }

    if (!validDate($eventDate)) {
        sendJson(422, [
            'success' => false,
            'message' => 'Enter a valid date in YYYY-MM-DD format.'
        ]);
    }

    if ($description === '') {
        sendJson(422, [
            'success' => false,
            'message' => 'Description is required.'
        ]);
    }

    if (!in_array($status, ['open', 'closed'], true)) {
        sendJson(422, [
            'success' => false,
            'message' => 'Status must be open or closed.'
        ]);
    }

    // CREATE: Add a new event
    if ($action === 'create') {
        $stmt = $conn->prepare(
            'INSERT INTO events_pr12
             (title, category, event_date, description, status)
             VALUES (?, ?, ?, ?, ?)'
        );

        $stmt->bind_param(
            'sssss',
            $title,
            $category,
            $eventDate,
            $description,
            $status
        );

        $stmt->execute();
        $newId = $conn->insert_id;
        $stmt->close();

        sendJson(201, [
            'success' => true,
            'message' => 'Event added successfully.',
            'id' => $newId
        ]);
    }

    // UPDATE: Update an existing event
    $id = filter_var(
        $input['id'] ?? null,
        FILTER_VALIDATE_INT
    );

    if (!$id || $id < 1) {
        sendJson(422, [
            'success' => false,
            'message' => 'A valid event ID is required for update.'
        ]);
    }

    $stmt = $conn->prepare(
        'UPDATE events_pr12
         SET title = ?, category = ?, event_date = ?,
             description = ?, status = ?
         WHERE id = ?'
    );

    $stmt->bind_param(
        'sssssi',
        $title,
        $category,
        $eventDate,
        $description,
        $status,
        $id
    );

    $stmt->execute();
    $affected = $stmt->affected_rows;
    $stmt->close();

    if ($affected === 0) {
        $check = $conn->prepare(
            'SELECT id FROM events_pr12 WHERE id = ?'
        );

        $check->bind_param('i', $id);
        $check->execute();
        $exists = $check->get_result()->num_rows > 0;
        $check->close();

        if (!$exists) {
            sendJson(404, [
                'success' => false,
                'message' => 'Event not found.'
            ]);
        }
    }

    sendJson(200, [
        'success' => true,
        'message' => 'Event updated successfully.'
    ]);

} catch (Throwable $e) {
    error_log($e->getMessage());

    sendJson(500, [
        'success' => false,
        'message' => 'Server error. Check PHP and database configuration.'
    ]);
}