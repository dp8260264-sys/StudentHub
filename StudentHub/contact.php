<?php
$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $subject = trim($_POST["subject"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $details = trim($_POST["details"] ?? "");

    if ($subject === "" || $category === "" || $details === "") {
        $message = "All fields are required.";
        $messageType = "error";
    } else {
        $subject = htmlspecialchars($subject, ENT_QUOTES, "UTF-8");
        $category = htmlspecialchars($category, ENT_QUOTES, "UTF-8");
        $details = htmlspecialchars($details, ENT_QUOTES, "UTF-8");

        $file = __DIR__ . "/data/contacts.json";
        $contacts = [];

        if (file_exists($file)) {
            $json = file_get_contents($file);
            $decoded = json_decode($json, true);
            if (is_array($decoded)) $contacts = $decoded;
        }

        $contacts[] = [
            "subject" => $subject,
            "category" => $category,
            "details" => $details,
            "date" => date("Y-m-d H:i:s")
        ];

        if (file_put_contents($file, json_encode($contacts, JSON_PRETTY_PRINT), LOCK_EX) !== false) {
            $message = "Support request submitted successfully.";
            $messageType = "success";
        } else {
            $message = "Unable to save request.";
            $messageType = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>StudentHub - Contact</title>
<link rel="stylesheet" href="styles.css">
<style>
.form-box{max-width:650px;padding:25px;background:#fff;border-radius:10px}
.form-group{margin-bottom:18px}.form-group label{display:block;margin-bottom:6px;font-weight:bold}
.form-group input,.form-group select,.form-group textarea{width:100%;padding:10px;box-sizing:border-box}
.form-group textarea{min-height:140px;resize:vertical}.form-box button{padding:10px 20px;cursor:pointer}
.success{padding:12px;margin-bottom:15px;background:#d4edda;color:#155724}
.error{padding:12px;margin-bottom:15px;background:#f8d7da;color:#721c24}
</style>
</head>
<body>
<div class="layout">
<aside class="sidebar">
<header><h2>STUDENTHUB</h2><p>Student Portal</p></header>
<nav aria-label="Student Portal Navigation">
<ul>
<li><a href="dashboard.html">Dashboard</a></li>
<li><a href="announcements.html">Announcements</a></li>
<li><a href="timetable.html">Courses &amp; Timetable</a></li>
<li><a href="attendance.html">Attendance</a></li>
<li><a href="results.html">Results</a></li>
<li><a href="materials.html">Study Resources</a></li>
<li><a href="events.html">Events</a></li>
<li><a href="contact.php" aria-current="page">Support</a></li>
<li><a href="register.php">Register</a></li>
</ul>
</nav>
</aside>
<main class="content">
<nav aria-label="Breadcrumb"><ol><li><a href="dashboard.html">Home</a></li><li aria-current="page">Support</li></ol></nav>
<header><p>SUPPORT</p><h1>Contact StudentHub</h1><p>Submit a support request.</p></header>
<section class="form-box">
<?php if ($message !== ""): ?><div class="<?php echo $messageType; ?>"><?php echo $message; ?></div><?php endif; ?>
<form method="POST" action="contact.php">
<div class="form-group"><label for="subject">Subject</label><input type="text" id="subject" name="subject" required></div>
<div class="form-group"><label for="category">Category</label>
<select id="category" name="category" required>
<option value="">Select Category</option><option value="academic">Academic</option><option value="attendance">Attendance</option><option value="results">Results</option><option value="portal">Portal Access</option><option value="other">Other</option>
</select></div>
<div class="form-group"><label for="details">Details</label><textarea id="details" name="details" required></textarea></div>
<button type="submit">Submit Request</button>
</form>
</section>
</main>
</div>
</body>
</html>
