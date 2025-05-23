<?php
$host = 'localhost';
$db = 'navicare';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db );
if ($conn->connect_error) {
    echo "<script>alert('Database connection error.'); window.history.back();</script>";
    exit();
}

if (!isset($_POST['fullname'], $_POST['username'], $_POST['idnumber'], $_POST['email'], $_POST['dob'], $_POST['password'])) {
    echo "<script>alert('All fields are required.'); window.history.back();</script>";
    exit();
}

$fullname = trim($_POST['fullname']);
$username_posted = trim($_POST['username']);
$idnumber = trim($_POST['idnumber']);
$email_input = trim($_POST['email']);
$dob_posted = trim($_POST['dob']);
$password_posted = $_POST['password'];

// 1. Check for empty fields
if (empty($fullname) || empty($username_posted) || empty($idnumber) || empty($email_input) || empty($dob_posted) || empty($password_posted)) {
    echo "<script>alert('Please fill out all fields.'); window.history.back();</script>";
    exit();
}

// 2. Full Name Validation
if (!preg_match("/^[A-Za-z\s]{2,50}$/", $fullname)) {
    echo "<script>alert('Invalid Full Name. Only letters and spaces allowed, 2-50 characters.'); window.history.back();</script>";
    exit();
}

// 3. Username Validation
if (!preg_match("/^[a-zA-Z0-9._-]{4,20}$/", $username_posted)) {
    echo "<script>alert('Username must be 4-20 characters (letters, numbers, dot, underscore, or hyphen).'); window.history.back();</script>";
    exit();
}
$username_lowercase = strtolower($username_posted);

// 4. ID Number Format Validation: ####M####
if (!preg_match("/^\d{4}M\d{4}$/", $idnumber)) {
    echo "<script>alert('ID Number must be in the format ####M#### (8 characters, M in the middle).'); window.history.back();</script>";
    exit();
}

// 5. Email Validation
if (!filter_var($email_input, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Invalid email format.'); window.history.back();</script>";
    exit();
}

$at_pos = strrpos($email_input, '@');
$local_part = substr($email_input, 0, $at_pos);
$domain_part = substr($email_input, $at_pos + 1);
$required_domain_literal = "wvsu.edu.ph";

if (strtolower($domain_part) !== $required_domain_literal) {
    echo "<script>alert('Email must end with @wvsu.edu.ph'); window.history.back();</script>";
    exit();
}
$email_for_db = $local_part . "@" . $required_domain_literal;

// 6. DOB Validation (MM/DD/YYYY)
$dateObj = DateTime::createFromFormat('m/d/Y', $dob_posted);
if (!($dateObj && $dateObj->format('m/d/Y') === $dob_posted)) {
    echo "<script>alert('Invalid DOB. Use MM/DD/YYYY format.'); window.history.back();</script>";
    exit();
}
$dob_for_db = $dateObj->format('Y-m-d');

// 7. Password Validation
if (preg_match("/\s/", $password_posted)) {
    echo "<script>alert('Password cannot contain whitespace.'); window.history.back();</script>";
    exit();
}
if (strlen($password_posted) < 8) {
    echo "<script>alert('Password must be at least 8 characters long.'); window.history.back();</script>";
    exit();
}
$password_hashed = password_hash($password_posted, PASSWORD_DEFAULT);

// --- Duplicate Checks ---
$checkID = $conn->prepare("SELECT id FROM users WHERE id_number = ?");
$checkID->bind_param("s", $idnumber);
$checkID->execute();
$resultID = $checkID->get_result();
if ($resultID->num_rows > 0) {
    echo "<script>alert('ID Number already registered.'); window.history.back();</script>";
    $checkID->close(); $conn->close(); exit();
}
$checkID->close();

$checkUsername = $conn->prepare("SELECT id FROM users WHERE username = ?");
$checkUsername->bind_param("s", $username_lowercase);
$checkUsername->execute();
if ($checkUsername->get_result()->num_rows > 0) {
    echo "<script>alert('Username is already taken.'); window.history.back();</script>";
    $checkUsername->close(); $conn->close(); exit();
}
$checkUsername->close();

$checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
$checkEmail->bind_param("s", $email_for_db);
$checkEmail->execute();
if ($checkEmail->get_result()->num_rows > 0) {
    echo "<script>alert('Email already exists.'); window.history.back();</script>";
    $checkEmail->close(); $conn->close(); exit();
}
$checkEmail->close();

// --- Insert New User ---
$insert = $conn->prepare("INSERT INTO users (fullname, username, id_number, email, dob, password) VALUES (?, ?, ?, ?, ?, ?)");
$insert->bind_param("ssssss", $fullname, $username_lowercase, $idnumber, $email_for_db, $dob_for_db, $password_hashed);

if ($insert->execute()) {
    $insert->close();
    $conn->close();
    header("Location: Terms_and_Conditions.html");
    exit();
} else {
    echo "<script>alert('Error saving your registration. Please try again later.'); window.history.back();</script>";
}

$insert->close();
$conn->close();
?>
