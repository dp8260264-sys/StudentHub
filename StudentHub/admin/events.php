
<?php
session_start();
require_once __DIR__ . '/../config/database.php';

mysqli_set_charset($conn, 'utf8mb4');

if (empty($_SESSION['event_csrf'])) {
    $_SESSION['event_csrf'] = bin2hex(random_bytes(32));
}

$csrf = $_SESSION['event_csrf'];
$uploadDir = __DIR__ . '/../uploads/event-posters/';
$uploadUrl = '../uploads/event-posters/';
$maxSize = 2 * 1024 * 1024;

$allowed = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp'
];

$message = '';
$error = '';
$edit = null;

function h($value): string {
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}

function savePoster($file, $uploadDir, $maxSize, $allowed) {
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if (($file['error'] ?? -1) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Poster upload failed.');
    }

    if ($file['size'] > $maxSize) {
        throw new RuntimeException('Poster must be 2 MB or smaller.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);

    if (!isset($allowed[$mime])) {
        throw new RuntimeException(
            'Only JPG, PNG and WEBP images are allowed.'
        );
    }

    if (@getimagesize($file['tmp_name']) === false) {
        throw new RuntimeException('The uploaded file is not a valid image.');
    }

    if (!is_dir($uploadDir) &&
        !mkdir($uploadDir, 0755, true)) {
        throw new RuntimeException('Cannot create poster folder.');
    }

    $filename = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];

    if (!move_uploaded_file(
        $file['tmp_name'],
        $uploadDir . $filename
    )) {
        throw new RuntimeException('Could not save the poster.');
    }

    return $filename;
}

/* ADD OR UPDATE EVENT */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPoster = null;

    try {
        if (!hash_equals(
            $_SESSION['event_csrf'] ?? '',
            $_POST['csrf'] ?? ''
        )) {
            throw new RuntimeException(
                'Invalid request. Refresh the page and retry.'
            );
        }

        if (($_POST['action'] ?? '') !== 'save') {
            throw new RuntimeException('Invalid action.');
        }

        $rawId = $_POST['id'] ?? '';
        $id = ($rawId === '')
            ? null
            : filter_var($rawId, FILTER_VALIDATE_INT);

        if ($rawId !== '' && (!$id || $id < 1)) {
            throw new RuntimeException('Invalid event ID.');
        }

        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $eventDate = $_POST['event_date'] ?? '';
        $description = trim($_POST['description'] ?? '');
        $status = $_POST['status'] ?? 'open';

        if ($title === '' || strlen($title) > 150) {
            throw new RuntimeException(
                'Enter an event title (maximum 150 characters).'
            );
        }

        if ($category === '' || strlen($category) > 80) {
            throw new RuntimeException('Enter a valid category.');
        }

        $date = DateTime::createFromFormat('!Y-m-d', $eventDate);

        if (!$date || $date->format('Y-m-d') !== $eventDate) {
            throw new RuntimeException('Enter a valid event date.');
        }

        if ($description === '') {
            throw new RuntimeException('Enter an event description.');
        }

        if (!in_array($status, ['open', 'closed'], true)) {
            throw new RuntimeException('Select a valid event status.');
        }

        $oldPoster = null;

        if ($id !== null) {
            $stmt = $conn->prepare(
                'SELECT poster FROM events_pr12 WHERE id = ?'
            );
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $old = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$old) {
                throw new RuntimeException('Event not found.');
            }

            $oldPoster = $old['poster'];
        }

        $newPoster = savePoster(
            $_FILES['poster'] ?? ['error' => UPLOAD_ERR_NO_FILE],
            $uploadDir,
            $maxSize,
            $allowed
        );

        $poster = $newPoster ?? $oldPoster;

        if ($id !== null) {
            $stmt = $conn->prepare(
                'UPDATE events_pr12
                 SET title = ?, category = ?, event_date = ?,
                     description = ?, status = ?, poster = ?
                 WHERE id = ?'
            );

            $stmt->bind_param(
                'ssssssi',
                $title,
                $category,
                $eventDate,
                $description,
                $status,
                $poster,
                $id
            );

            $stmt->execute();
            $stmt->close();

            $message = 'Event updated successfully.';
        } else {
            $stmt = $conn->prepare(
                'INSERT INTO events_pr12
                 (title, category, event_date, description, status, poster)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );

            $stmt->bind_param(
                'ssssss',
                $title,
                $category,
                $eventDate,
                $description,
                $status,
                $poster
            );

            $stmt->execute();
            $stmt->close();

            $message = 'Event added successfully.';
        }

        // Remove the old poster only after saving the new record.
        if (
            $newPoster !== null &&
            $oldPoster &&
            is_file($uploadDir . basename($oldPoster))
        ) {
            unlink($uploadDir . basename($oldPoster));
        }

    } catch (Throwable $e) {
        // Remove a newly uploaded file if the database operation failed.
        if (
            $newPoster !== null &&
            is_file($uploadDir . basename($newPoster))
        ) {
            unlink($uploadDir . basename($newPoster));
        }

        $error = $e->getMessage();
    }
}

/* LOAD EVENT FOR EDITING */
if (isset($_GET['edit'])) {
    $editId = filter_var($_GET['edit'], FILTER_VALIDATE_INT);

    if ($editId && $editId > 0) {
        $stmt = $conn->prepare(
            'SELECT * FROM events_pr12 WHERE id = ?'
        );

        $stmt->bind_param('i', $editId);
        $stmt->execute();
        $edit = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }
}

/* SEARCH AND FILTER */
$search = trim($_GET['search'] ?? '');
$categoryFilter = trim($_GET['category'] ?? '');
$statusFilter = $_GET['status'] ?? '';

$sql = 'SELECT * FROM events_pr12 WHERE 1=1';
$types = '';
$params = [];

if ($search !== '') {
    $sql .= ' AND (title LIKE ? OR description LIKE ?)';
    $like = '%' . $search . '%';
    $types .= 'ss';
    $params[] = $like;
    $params[] = $like;
}

if ($categoryFilter !== '') {
    $sql .= ' AND category = ?';
    $types .= 's';
    $params[] = $categoryFilter;
}

if (in_array($statusFilter, ['open', 'closed'], true)) {
    $sql .= ' AND status = ?';
    $types .= 's';
    $params[] = $statusFilter;
}

$sql .= ' ORDER BY event_date DESC';

$stmt = $conn->prepare($sql);

if ($types !== '') {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$events = $stmt->get_result();
$stmt->close();

$categories = $conn->query(
    'SELECT DISTINCT category
     FROM events_pr12
     ORDER BY category'
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Event Management | StudentHub</title>
    <link rel="stylesheet" href="events.css">
</head>
<body>
<main class="container">
    <h1>Event Management</h1>
    <p>Add, view, edit and manage StudentHub events.</p>

    <?php if ($message !== ''): ?>
        <p class="success"><?= h($message) ?></p>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <p class="error"><?= h($error) ?></p>
    <?php endif; ?>

    <section class="panel">
        <h2><?= $edit ? 'Update Event' : 'Add New Event' ?></h2>

        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id"
                   value="<?= h($edit['id'] ?? '') ?>">

            <label for="title">Event Title</label>
            <input id="title" name="title" maxlength="150" required
                   value="<?= h($edit['title'] ?? '') ?>">

            <label for="category">Category</label>
            <input id="category" name="category" maxlength="80" required
                   placeholder="Workshop, Seminar, Festival"
                   value="<?= h($edit['category'] ?? '') ?>">

            <label for="event_date">Event Date</label>
            <input id="event_date" type="date" name="event_date" required
                   value="<?= h($edit['event_date'] ?? '') ?>">

            <label for="description">Description</label>
            <textarea id="description" name="description"
                      required><?= h($edit['description'] ?? '') ?></textarea>

            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="open"
                    <?= ($edit['status'] ?? 'open') === 'open'
                        ? 'selected' : '' ?>>Open</option>
                <option value="closed"
                    <?= ($edit['status'] ?? '') === 'closed'
                        ? 'selected' : '' ?>>Closed</option>
            </select>

            <label for="poster">Poster (JPG, PNG or WEBP; maximum 2 MB)</label>
            <input id="poster" type="file" name="poster"
                   accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">

            <?php if (!empty($edit['poster'])): ?>
                <p>Current poster:</p>
                <img class="poster"
                     src="<?= h($uploadUrl . basename($edit['poster'])) ?>"
                     alt="Current event poster">
                <p>Leave the poster field empty to keep the current poster.</p>
            <?php endif; ?>

            <button type="submit">
                <?= $edit ? 'Update Event' : 'Add Event' ?>
            </button>

            <?php if ($edit): ?>
                <a href="events.php">Cancel edit</a>
            <?php endif; ?>
        </form>
    </section>

    <section class="panel">
        <h2>Event List</h2>

        <form method="get" class="filters">
            <input name="search"
                   placeholder="Search title or description"
                   value="<?= h($search) ?>">

            <select name="category">
                <option value="">All categories</option>
                <?php while ($cat = $categories->fetch_assoc()): ?>
                    <option value="<?= h($cat['category']) ?>"
                        <?= $categoryFilter === $cat['category']
                            ? 'selected' : '' ?>>
                        <?= h($cat['category']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <select name="status">
                <option value="">All statuses</option>
                <option value="open"
                    <?= $statusFilter === 'open' ? 'selected' : '' ?>>
                    Open
                </option>
                <option value="closed"
                    <?= $statusFilter === 'closed' ? 'selected' : '' ?>>
                    Closed
                </option>
            </select>

            <button type="submit">Search / Filter</button>
            <a href="events.php">Clear</a>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Poster</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($event = $events->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?php if (!empty($event['poster'])): ?>
                                <img class="poster"
                                     src="<?= h($uploadUrl . basename($event['poster'])) ?>"
                                     alt="Event poster">
                            <?php else: ?>
                                No poster
                            <?php endif; ?>
                        </td>
                        <td><?= h($event['title']) ?></td>
                        <td><?= h($event['category']) ?></td>
                        <td><?= h($event['event_date']) ?></td>
                        <td><?= h(ucfirst($event['status'])) ?></td>
                        <td>
                            <a href="events.php?edit=<?= (int)$event['id'] ?>">
                                Edit
                            </a>

                            <form method="post" action="event-delete.php"
                                  onsubmit="return confirm('Delete this event permanently?');">
                                <input type="hidden" name="csrf"
                                       value="<?= h($csrf) ?>">
                                <input type="hidden" name="id"
                                       value="<?= (int)$event['id'] ?>">
                                <button class="danger" type="submit">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
</body>
</html>
