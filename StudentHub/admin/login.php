<?php
session_start();
require_once __DIR__ . '/../config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare(
        "SELECT id, username, password_hash, role
         FROM admin_users WHERE username = ? LIMIT 1"
    );
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();

    if ($admin && password_verify($password, $admin['password_hash'])
        && $admin['role'] === 'admin') {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int) $admin['id'];
        $_SESSION['admin_name'] = $admin['username'];
        $_SESSION['role'] = 'admin';

        header('Location: dashboard.php');
        exit;
    }

    $error = 'Invalid admin username or password. Check admin_users table and password hash.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>StudentHub Admin Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width:450px">
  <div class="card shadow-sm">
    <div class="card-body p-4">
      <h1 class="h3 mb-3">StudentHub Admin Login</h1>
      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <form method="post">
        <label class="form-label" for="username">Username</label>
        <input class="form-control mb-3" id="username" name="username" required>
        <label class="form-label" for="password">Password</label>
        <input class="form-control mb-3" type="password" id="password" name="password" required>
        <button class="btn btn-primary w-100">Log In</button>
      </form>
    </div>
  </div>
</div>
</body>
</html>