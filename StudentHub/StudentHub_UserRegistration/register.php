<?php

require_once "db.php";
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request.");
}
/* Get form data */
$full_name = trim($_POST["name"] ?? "");
$username = trim($_POST["username"] ?? "");
$email = trim($_POST["email"] ?? "");
$mobile = trim($_POST["mobile"] ?? "");
$password = $_POST["password"] ?? "";
$confirmPassword = $_POST["confirmPassword"] ?? "";
$course = trim($_POST["course"] ?? "");
$year = trim($_POST["year"] ?? "");
$gender = trim($_POST["gender"] ?? "");
$terms = isset($_POST["terms"]);

/* Validation */
$errors = [];

/* Full name */
if ($full_name === "") {
    $errors[] = "Full name is required.";
}

/* Username */
if ($username === "") {
    $errors[] = "Username is required.";
} elseif (!preg_match("/^[a-zA-Z0-9_]{3,50}$/", $username)) {
    $errors[] = "Username must contain 3-50 letters, numbers or underscore.";
}

/* Email */
if ($email === "") {
    $errors[] = "Email is required.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Please enter a valid email address.";
}

/* Mobile */
if (!preg_match("/^[0-9]{10}$/", $mobile)) {
    $errors[] = "Mobile number must contain exactly 10 digits.";
}

/* Password */
if (strlen($password) < 8) {
    $errors[] = "Password must contain at least 8 characters.";
}

/* Confirm password */
if ($password !== $confirmPassword) {
    $errors[] = "Passwords do not match.";
}

/* Course */
if ($course === "") {
    $errors[] = "Please select a course.";
}

/* Year */
if ($year === "") {
    $errors[] = "Please select a year.";
}

/* Gender */
if ($gender === "") {
    $errors[] = "Please select your gender.";
}

/* Terms */
if (!$terms) {
    $errors[] = "You must accept the Terms and Conditions.";
}

/* Display validation errors */
if (!empty($errors)) {
    echo "<h2>Registration Failed</h2>";
    echo "<ul>";
    foreach ($errors as $error) {
        echo "<li>" . htmlspecialchars($error) . "</li>";
    }
    echo "</ul>";
    echo '<a href="../register.html">Go Back</a>';
    exit;
}

/* Check duplicate username and email */
$sql = "SELECT user_id
        FROM users
        WHERE username = ? OR email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ss",
    $username,
    $email
);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo "<h2>Registration Failed</h2>";
    echo "<p>Username or email already exists.</p>";
    echo '<a href="../register.html">Go Back</a>';
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

/* Hash password */
$hashedPassword = password_hash(
    $password,
    PASSWORD_DEFAULT
);

/* Insert data */
$sql = "INSERT INTO users
        (full_name,username,email,
        mobile,password,course,
        year,gender)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ssssssss",
    $full_name, $username,$email,
    $mobile,$hashedPassword,$course,
    $year,$gender);

/* Execute insert */
if ($stmt->execute()) {
    echo "<h2>Registration Successful</h2>";
    echo "<p>Your StudentHub account has been created successfully.</p>";
    echo "<p><strong>Username:</strong> "
        . htmlspecialchars($username)
        . "</p>";
    echo '<p><a href="../login.html">Go to Login</a></p>';
} else {
    echo "<h2>Registration Failed</h2>";
    echo "<p>Unable to create the account.</p>";
}
$stmt->close();
$conn->close();
?>