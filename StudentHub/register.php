<?php
$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $mobile = trim($_POST["mobile"] ?? "");
    $course = trim($_POST["course"] ?? "");

    if ($name === "" || $email === "" || $mobile === "" || $course === "") {
        $message = "Please fill all required fields.";
        $messageType = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageType = "error";
    } elseif (!preg_match("/^[0-9]{10}$/", $mobile)) {
        $message = "Mobile number must contain exactly 10 digits.";
        $messageType = "error";
    } else {
        $name = htmlspecialchars($name, ENT_QUOTES, "UTF-8");
        $email = htmlspecialchars($email, ENT_QUOTES, "UTF-8");
        $mobile = htmlspecialchars($mobile, ENT_QUOTES, "UTF-8");
        $course = htmlspecialchars($course, ENT_QUOTES, "UTF-8");

        $file = __DIR__ . "/data/registrations.csv";

        if (!file_exists($file)) {
            $fp = fopen($file, "w");
            fputcsv($fp, ["Name", "Email", "Mobile", "Course"]);
            fclose($fp);
        }

        $fp = fopen($file, "a");
        if ($fp) {
            fputcsv($fp, [$name, $email, $mobile, $course]);
            fclose($fp);
            $message = "Registration successful!";
            $messageType = "success";
        } else {
            $message = "Unable to save registration.";
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
<title>StudentHub - Registration</title>
<link rel="stylesheet" href="styles.css">
<style>
.form-box{max-width:600px;padding:25px;background:#fff;border-radius:10px}
.form-group{margin-bottom:18px}.form-group label{display:block;margin-bottom:6px;font-weight:bold}
.form-group input,.form-group select{width:100%;padding:10px;box-sizing:border-box}
.form-box button{padding:10px 20px;cursor:pointer}
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
<li><a href="contact.php">Support</a></li>
<li><a href="register.php" aria-current="page">Register</a></li>
</ul>
</nav>
</aside>
<main class="content">
<nav aria-label="Breadcrumb"><ol><li><a href="dashboard.html">Home</a></li><li aria-current="page">Register</li></ol></nav>
<header><p>ACCOUNT</p><h1>Student Registration</h1><p>Create your StudentHub account.</p></header>
<section class="form-box">
<?php if ($message !== ""): ?><div class="<?php echo $messageType; ?>"><?php echo $message; ?></div><?php endif; ?>
<form method="POST" action="register.php">
<div class="form-group"><label for="name">Full Name</label><input type="text" id="name" name="name" required></div>
<div class="form-group"><label for="email">Email</label><input type="email" id="email" name="email" required></div>
<div class="form-group"><label for="mobile">Mobile Number</label><input type="text" id="mobile" name="mobile" maxlength="10" required></div>
<div class="form-group"><label for="course">Course</label>
<select id="course" name="course" required>
<option value="">Select Course</option><option>Computer Engineering</option><option>Information Technology</option><option>Computer Science</option>
</select></div>
<button type="submit">Register</button>
</form>
</section>
</main>
</div>
</body>
</html>
