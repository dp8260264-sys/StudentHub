
<?php
session_start();
require_once __DIR__ . '/../config/database.php';

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$message = $_SESSION['message'] ?? '';
$messageType = $_SESSION['message_type'] ?? 'success';
unset($_SESSION['message'], $_SESSION['message_type']);

function redirectWithMessage($text, $type = 'success') {
    $_SESSION['message'] = $text;
    $_SESSION['message_type'] = $type;
    header('Location: student-management.php');
    exit;
}

// CREATE, UPDATE and SOFT DELETE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create' || $action === 'update') {
            $id = (int)($_POST['id'] ?? 0);
            $name = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $mobile = trim($_POST['mobile'] ?? '');
            $course = trim($_POST['course'] ?? '');
            $year = filter_var(
                $_POST['year_of_study'] ?? null,
                FILTER_VALIDATE_INT
            );

            if (
                $name === '' || strlen($name) > 100 ||
                !filter_var($email, FILTER_VALIDATE_EMAIL) ||
                strlen($email) > 150 ||
                !preg_match('/^[0-9+\-\s]{7,15}$/', $mobile) ||
                $course === '' || strlen($course) > 100 ||
                $year === false || $year < 1 || $year > 6
            ) {
                throw new InvalidArgumentException(
                    'Please enter valid student details.'
                );
            }

            if ($action === 'create') {
                $stmt = $conn->prepare(
                    "INSERT INTO students
                    (full_name, email, mobile, course, year_of_study)
                    VALUES (?, ?, ?, ?, ?)"
                );
                $stmt->bind_param(
                    "ssssi", $name, $email, $mobile, $course, $year
                );
                $stmt->execute();
                redirectWithMessage('Student added successfully.');
            } else {
                if ($id < 1) {
                    throw new InvalidArgumentException('Invalid student ID.');
                }

                $stmt = $conn->prepare(
                    "UPDATE students SET full_name=?, email=?,
                    mobile=?, course=?, year_of_study=?
                    WHERE id=? AND status='active'"
                );
                $stmt->bind_param(
                    "ssssii", $name, $email, $mobile,
                    $course, $year, $id
                );
                $stmt->execute();

                redirectWithMessage('Student updated successfully.');
            }
        }

        if ($action === 'delete') {
            $id = filter_var(
                $_POST['id'] ?? null,
                FILTER_VALIDATE_INT
            );

            if ($id === false || $id < 1) {
                throw new InvalidArgumentException('Invalid student ID.');
            }

            $stmt = $conn->prepare(
                "UPDATE students SET status='inactive'
                 WHERE id=? AND status='active'"
            );
            $stmt->bind_param("i", $id);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                redirectWithMessage('Student deactivated successfully.');
            }

            redirectWithMessage(
                'Student not found or already inactive.', 'error'
            );
        }
    } catch (InvalidArgumentException $ex) {
        redirectWithMessage($ex->getMessage(), 'error');
    } catch (mysqli_sql_exception $ex) {
        error_log($ex->getMessage());

        $error = $ex->getCode() === 1062
            ? 'This email address is already registered.'
            : 'Database operation failed. Please try again.';

        redirectWithMessage($error, 'error');
    }
}

// READ: search, filter and pagination
$search = trim($_GET['search'] ?? '');
$courseFilter = trim($_GET['course'] ?? '');
$statusFilter = $_GET['status'] ?? 'active';

if (!in_array($statusFilter, ['active', 'inactive', 'all'], true)) {
    $statusFilter = 'active';
}

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;

$where = " WHERE 1=1 ";
$params = [];
$types = "";

if ($search !== '') {
    $where .= " AND (full_name LIKE ? OR email LIKE ? OR course LIKE ?) ";
    $like = "%$search%";
    $params = array_merge($params, [$like, $like, $like]);
    $types .= "sss";
}

if ($courseFilter !== '') {
    $where .= " AND course = ? ";
    $params[] = $courseFilter;
    $types .= "s";
}

if ($statusFilter !== 'all') {
    $where .= " AND status = ? ";
    $params[] = $statusFilter;
    $types .= "s";
}

$countStmt = $conn->prepare(
    "SELECT COUNT(*) AS total FROM students $where"
);
if ($types !== '') {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$total = (int)$countStmt->get_result()->fetch_assoc()['total'];

$totalPages = max(1, (int)ceil($total / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

$sql = "SELECT * FROM students $where
        ORDER BY id DESC LIMIT ? OFFSET ?";
$listStmt = $conn->prepare($sql);
$listParams = array_merge($params, [$perPage, $offset]);
$listTypes = $types . "ii";
$listStmt->bind_param($listTypes, ...$listParams);
$listStmt->execute();
$students = $listStmt->get_result();

// Courses for the filter
$courseResult = $conn->query(
    "SELECT DISTINCT course FROM students ORDER BY course"
);

// EDIT: load selected student
$editStudent = null;
$editId = filter_var($_GET['edit'] ?? null, FILTER_VALIDATE_INT);

if ($editId !== false && $editId !== null && $editId > 0) {
    $editStmt = $conn->prepare(
        "SELECT * FROM students WHERE id=? AND status='active'"
    );
    $editStmt->bind_param("i", $editId);
    $editStmt->execute();
    $editStudent = $editStmt->get_result()->fetch_assoc();

    if (!$editStudent) {
        $message = 'Student not found or inactive.';
        $messageType = 'error';
    }
}

$formAction = $editStudent ? 'update' : 'create';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Management | StudentHub</title>
    <link rel="stylesheet" href="student-management.css">
</head>
<body>
<header class="topbar">
    <div>
        <h1>StudentHub</h1>
        <p>Student Management Dashboard</p>
    </div>
    <a class="back-link" href="../index.html">Back to Home</a>
</header>

<main class="container">
    <section class="page-heading">
        <div>
            <h2>Manage Students</h2>
            <p>Add, search, edit and manage student records.</p>
        </div>
        <div class="total-box">
            <span>Matching Records</span>
            <strong><?= $total ?></strong>
        </div>
    </section>

    <?php if ($message !== ''): ?>
        <div class="alert <?= $messageType === 'error' ? 'error' : 'success' ?>">
            <?= e($message) ?>
        </div>
    <?php endif; ?>

    <section class="panel">
        <h3><?= $editStudent ? 'Edit Student' : 'Add New Student' ?></h3>

        <form method="post" action="student-management.php" class="student-form">
            <input type="hidden" name="action" value="<?= e($formAction) ?>">
            <?php if ($editStudent): ?>
                <input type="hidden" name="id" value="<?= (int)$editStudent['id'] ?>">
            <?php endif; ?>

            <label>
                Full Name
                <input name="full_name" maxlength="100" required
                    value="<?= e($editStudent['full_name'] ?? '') ?>"
                    placeholder="Enter full name">
            </label>

            <label>
                Email Address
                <input type="email" name="email" maxlength="150" required
                    value="<?= e($editStudent['email'] ?? '') ?>"
                    placeholder="student@example.com">
            </label>

            <label>
                Mobile Number
                <input name="mobile" maxlength="15"
                    pattern="[0-9+\-\s]{7,15}" required
                    value="<?= e($editStudent['mobile'] ?? '') ?>"
                    placeholder="Enter mobile number">
            </label>

            <label>
                Course
                <select name="course" required>
                    <option value="">Select course</option>
                    <?php
                    $courseOptions = [
                        'Computer Engineering',
                        'Information Technology',
                        'Artificial Intelligence'
                    ];
                    foreach ($courseOptions as $option):
                    ?>
                        <option value="<?= e($option) ?>"
                            <?= ($editStudent['course'] ?? '') === $option
                                ? 'selected' : '' ?>>
                            <?= e($option) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Year of Study
                <select name="year_of_study" required>
                    <option value="">Select year</option>
                    <?php for ($y = 1; $y <= 6; $y++): ?>
                        <option value="<?= $y ?>"
                            <?= (int)($editStudent['year_of_study'] ?? 0) === $y
                                ? 'selected' : '' ?>>
                            Year <?= $y ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </label>

            <div class="form-actions">
                <button class="btn primary" type="submit">
                    <?= $editStudent ? 'Save Changes' : 'Add Student' ?>
                </button>
                <?php if ($editStudent): ?>
                    <a class="btn secondary" href="student-management.php">
                        Cancel
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </section>

    <section class="panel">
        <h3>Student Records</h3>

        <form method="get" class="filters">
            <input type="search" name="search"
                value="<?= e($search) ?>"
                placeholder="Search name, email or course">

            <select name="course">
                <option value="">All Courses</option>
                <?php while ($c = $courseResult->fetch_assoc()): ?>
                    <option value="<?= e($c['course']) ?>"
                        <?= $courseFilter === $c['course'] ? 'selected' : '' ?>>
                        <?= e($c['course']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <select name="status">
                <option value="active" <?= $statusFilter === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= $statusFilter === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                <option value="all" <?= $statusFilter === 'all' ? 'selected' : '' ?>>All Statuses</option>
            </select>

            <button class="btn primary" type="submit">Search / Filter</button>
            <a class="btn secondary" href="student-management.php">Reset</a>
            <a class="btn export" href="export-students.php?<?= e(http_build_query([
                'search' => $search,
                'course' => $courseFilter,
                'status' => $statusFilter
            ])) ?>">Export CSV</a>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student</th>
                        <th>Contact</th>
                        <th>Course</th>
                        <th>Year</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($students->num_rows === 0): ?>
                    <tr><td colspan="7" class="empty">
                        No student records found.
                    </td></tr>
                <?php else: ?>
                    <?php while ($s = $students->fetch_assoc()): ?>
                        <tr>
                            <td><?= (int)$s['id'] ?></td>
                            <td>
                                <strong><?= e($s['full_name']) ?></strong>
                                <small><?= e($s['email']) ?></small>
                            </td>
                            <td><?= e($s['mobile']) ?></td>
                            <td><?= e($s['course']) ?></td>
                            <td><?= (int)$s['year_of_study'] ?></td>
                            <td>
                                <span class="status <?= e($s['status']) ?>">
                                    <?= e(ucfirst($s['status'])) ?>
                                </span>
                            </td>
                            <td class="actions">
                                <?php if ($s['status'] === 'active'): ?>
                                    <a class="btn small secondary"
                                       href="?edit=<?= (int)$s['id'] ?>">Edit</a>
                                    <form method="post" class="inline-form"
                                        onsubmit="return confirm('Deactivate this student?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
                                        <button class="btn small danger" type="submit">
                                            Delete
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="muted">No actions</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <span>Page <?= $page ?> of <?= $totalPages ?></span>
            <div>
                <?php if ($page > 1): ?>
                    <a class="btn secondary" href="?<?= e(http_build_query([
                        'search' => $search, 'course' => $courseFilter,
                        'status' => $statusFilter, 'page' => $page - 1
                    ])) ?>">Previous</a>
                <?php endif; ?>

                <?php if ($page < $totalPages): ?>
                    <a class="btn secondary" href="?<?= e(http_build_query([
                        'search' => $search, 'course' => $courseFilter,
                        'status' => $statusFilter, 'page' => $page + 1
                    ])) ?>">Next</a>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>
</body>
</html>
