<?php
// Must be at the very top before any output
// session_start(); // Uncomment if you start using sessions on this page

$host = 'localhost';
$db = 'navicare';
$user = 'root';
$pass = ''; // Your database password

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    // Log error and show a generic message to the user
    error_log("Connection failed: " . $conn->connect_error);
    die("<script>alert('Database connection error. Please try again later.'); window.location.href = 'register.html';</script>");
}

// Check if all POST variables are set
if (!isset($_POST['fullname'], $_POST['username'], $_POST['idnumber'], $_POST['email'], $_POST['dob'], $_POST['password'])) {
    echo "<script>
            alert('Please fill all required fields. (Initial check failed)');
            window.location.href = 'register.html';
          </script>";
    exit();
}

$fullname = trim($_POST['fullname']);
$username_posted = trim($_POST['username']);
$idnumber = trim($_POST['idnumber']);
$email_input = trim($_POST['email']); // User's original email input
$dob_posted = trim($_POST['dob']); // Format: MM/DD/YYYY
$password_posted = $_POST['password']; // Password trimming is usually not done

// --- Server-side Validations ---

// 1. Basic non-empty checks (after trimming)
if (empty($fullname) || empty($username_posted) || empty($idnumber) || empty($email_input) || empty($dob_posted) || empty($password_posted)) {
    echo "<script>alert('All fields are required. (Empty check failed)'); window.location.href = 'register.html';</script>";
    $conn->close();
    exit();
}

// 2. Full Name Validation
if (!preg_match("/^[A-Za-z\s]{2,50}$/", $fullname)) {
    echo "<script>alert('Invalid Full Name. Must be 2-50 characters, letters and spaces only.'); window.location.href = 'register.html';</script>";
    $conn->close();
    exit();
}

// 3. Username Validation
if (!preg_match("/^[a-zA-Z0-9._-]{4,20}$/", $username_posted)) {
    echo "<script>alert('Invalid Username. Must be 4-20 characters (alphanumeric, ., _, or -).'); window.location.href = 'register.html';</script>";
    $conn->close();
    exit();
}
$username_lowercase = strtolower($username_posted); // Username is case-insensitive

// 4. ID Number Validation
if (!preg_match("/^\d{6,10}$/", $idnumber)) { // Example: 6-10 digits. Adjust if needed.
    echo "<script>alert('Invalid ID Number. Must be 6-10 digits (example format).'); window.location.href = 'register.html';</script>";
    $conn->close();
    exit();
}

// 5. Email Validation
//    Local part case-sensitive, domain must be @wvsu.edu.ph (case-insensitive check for domain input)
if (!filter_var($email_input, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Invalid email format.'); window.location.href = 'register.html';</script>";
    $conn->close();
    exit();
}

$at_pos = strrpos($email_input, '@');
// This check is somewhat redundant if filter_var passed, but good for explicit logic
if ($at_pos === false) {
    echo "<script>alert('Invalid email format (missing @ symbol).'); window.location.href = 'register.html';</script>";
    $conn->close();
    exit();
}

$local_part = substr($email_input, 0, $at_pos);
$domain_part = substr($email_input, $at_pos + 1);
$required_domain_literal = "wvsu.edu.ph"; // The domain we expect, in lowercase

// Check if the domain part (case-insensitively) matches the required domain
if (strtolower($domain_part) !== $required_domain_literal) {
    echo "<script>alert('Email address must be from the @wvsu.edu.ph domain.'); window.location.href = 'register.html';</script>";
    $conn->close();
    exit();
}

// Construct the email for DB: original case for local part, canonical lowercase for domain
$email_for_db = $local_part . "@" . $required_domain_literal;


// 6. Date of Birth Validation
$dateObj = DateTime::createFromFormat('m/d/Y', $dob_posted);
if (!($dateObj && $dateObj->format('m/d/Y') === $dob_posted)) {
    echo "<script>
            alert('Invalid Date of Birth. Please use MM/DD/YYYY format and ensure it is a valid date.');
            window.location.href = 'register.html';
          </script>";
    $conn->close();
    exit();
}
$dob_for_db = $dateObj->format('Y-m-d');

// 7. Password Validation
if (preg_match("/\s/", $password_posted)) {
    echo "<script>alert('Password cannot contain whitespace.'); window.location.href = 'register.html';</script>";
    $conn->close();
    exit();
}
if (strlen($password_posted) < 8) { // Example: minimum 8 characters
    echo "<script>alert('Password must be at least 8 characters long.'); window.location.href = 'register.html';</script>";
    $conn->close();
    exit();
}
$password_hashed = password_hash($password_posted, PASSWORD_DEFAULT);


// --- Database Checks (Uniqueness) ---

// Check for existing fullname and ID number combination
$checkFullnameID = $conn->prepare("SELECT id FROM users WHERE fullname = ? AND id_number = ?");
if (!$checkFullnameID) { /* ... error handling ... */ }
$checkFullnameID->bind_param("ss", $fullname, $idnumber);
$checkFullnameID->execute();
$result1 = $checkFullnameID->get_result();
if ($result1->num_rows > 0) {
    echo "<script>alert('A user with this Full Name and ID Number already exists.'); window.location.href = 'register.html';</script>";
    $checkFullnameID->close(); $conn->close(); exit();
}
$checkFullnameID->close();

// Check for existing username (case-insensitive)
$checkUsername = $conn->prepare("SELECT id FROM users WHERE username = ?");
if (!$checkUsername) { /* ... error handling ... */ }
$checkUsername->bind_param("s", $username_lowercase);
$checkUsername->execute();
$result2 = $checkUsername->get_result();
if ($result2->num_rows > 0) {
    echo "<script>alert('Username already taken. Please choose another.'); window.location.href = 'register.html';</script>";
    $checkUsername->close(); $conn->close(); exit();
}
$checkUsername->close();

// Check for existing email (using $email_for_db which has case-sensitive local part and normalized domain)
$checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
if (!$checkEmail) {
    error_log("Prepare failed (checkEmail): (" . $conn->errno . ") " . $conn->error);
    die("<script>alert('An error occurred (DB3). Please try again.'); window.location.href = 'register.html';</script>");
}
$checkEmail->bind_param("s", $email_for_db); // Using $email_for_db
$checkEmail->execute();
$result3 = $checkEmail->get_result();

if ($result3->num_rows > 0) {
    echo "<script>
            alert('This email address is already registered.');
            window.location.href = 'register.html';
          </script>";
    $checkEmail->close();
    $conn->close();
    exit();
}
$checkEmail->close();


// --- Prepare INSERT statement ---
$insert = $conn->prepare("INSERT INTO users (fullname, username, id_number, email, dob, password) VALUES (?, ?, ?, ?, ?, ?)");
if (!$insert) {
    error_log("Prepare failed (insert): (" . $conn->errno . ") " . $conn->error);
    die("<script>alert('An error occurred during registration (DB4). Please try again.'); window.location.href = 'register.html';</script>");
}
// Using $email_for_db for insertion
$insert->bind_param("ssssss", $fullname, $username_lowercase, $idnumber, $email_for_db, $dob_for_db, $password_hashed);

if ($insert->execute()) {
    header("Location: Terms_and_Conditions.html"); // Ensure this page exists
    $insert->close();
    $conn->close();
    exit();
} else {
    error_log("Execute failed (insert): (" . $insert->errno . ") " . $insert->error);
    echo "<script>
            alert('Error during registration. Please try again.');
            window.location.href = 'register.html';
          </script>";
}

$insert->close();
$conn->close();
?>