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
            alert('Please fill all required fields.');
            window.location.href = 'register.html';
          </script>";
    exit();
}

$fullname = trim($_POST['fullname']);
$username_posted = trim($_POST['username']);
$idnumber = trim($_POST['idnumber']);
$email_posted = trim($_POST['email']);
$dob_posted = trim($_POST['dob']); // Format: MM/DD/YYYY
$password_posted = $_POST['password']; // Password trimming is usually not done

// --- Server-side Validations ---

// 1. Basic non-empty checks (though client-side should catch this)
if (empty($fullname) || empty($username_posted) || empty($idnumber) || empty($email_posted) || empty($dob_posted) || empty($password_posted)) {
    echo "<script>alert('All fields are required.'); window.location.href = 'register.html';</script>";
    $conn->close();
    exit();
}

// 2. Email Validation (format and domain)
$email_lowercase = strtolower($email_posted); // Convert to lowercase for consistent checking and storage
if (!filter_var($email_lowercase, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Invalid email format.'); window.location.href = 'register.html';</script>";
    $conn->close();
    exit();
}
// Check for @wvsu.edu.ph domain
$required_domain = "@wvsu.edu.ph";
if (substr($email_lowercase, -strlen($required_domain)) !== $required_domain) {
    echo "<script>alert('Email address must be from the @wvsu.edu.ph domain.'); window.location.href = 'register.html';</script>";
    $conn->close();
    exit();
}

// 3. Date of Birth Validation and Conversion
$dateObj = DateTime::createFromFormat('m/d/Y', $dob_posted);
if (!($dateObj && $dateObj->format('m/d/Y') === $dob_posted)) {
    echo "<script>
            alert('Invalid Date of Birth. Please use MM/DD/YYYY format and ensure it is a valid date.');
            window.location.href = 'register.html';
          </script>";
    $conn->close();
    exit();
}
$dob_for_db = $dateObj->format('Y-m-d'); // Convert to YYYY-MM-DD for MySQL

// 4. Password Hashing (do this after all other validations pass)
$password_hashed = password_hash($password_posted, PASSWORD_DEFAULT);

// 5. Username: Convert to lowercase for consistent checking and storage
$username_lowercase = strtolower($username_posted);


// --- Database Checks ---

// Check for existing fullname and ID number combination
$checkFullnameID = $conn->prepare("SELECT id FROM users WHERE fullname = ? AND id_number = ?");
if (!$checkFullnameID) {
    error_log("Prepare failed (checkFullnameID): (" . $conn->errno . ") " . $conn->error);
    die("<script>alert('An error occurred (DB1). Please try again.'); window.location.href = 'register.html';</script>");
}
$checkFullnameID->bind_param("ss", $fullname, $idnumber);
$checkFullnameID->execute();
$result1 = $checkFullnameID->get_result();

if ($result1->num_rows > 0) {
    echo "<script>
            alert('A user with this Full Name and ID Number already exists.');
            window.location.href = 'register.html';
          </script>";
    $checkFullnameID->close();
    $conn->close();
    exit();
}
$checkFullnameID->close();

// Check for existing username (case-insensitive by storing and checking lowercase)
$checkUsername = $conn->prepare("SELECT id FROM users WHERE username = ?");
if (!$checkUsername) {
    error_log("Prepare failed (checkUsername): (" . $conn->errno . ") " . $conn->error);
    die("<script>alert('An error occurred (DB2). Please try again.'); window.location.href = 'register.html';</script>");
}
$checkUsername->bind_param("s", $username_lowercase); // Check lowercase username
$checkUsername->execute();
$result2 = $checkUsername->get_result();

if ($result2->num_rows > 0) {
    echo "<script>
            alert('Username already taken. Please choose another.');
            window.location.href = 'register.html';
          </script>";
    $checkUsername->close();
    $conn->close();
    exit();
}
$checkUsername->close();

// Check for existing email (case-insensitive by storing and checking lowercase)
$checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
if (!$checkEmail) {
    error_log("Prepare failed (checkEmail): (" . $conn->errno . ") " . $conn->error);
    die("<script>alert('An error occurred (DB3). Please try again.'); window.location.href = 'register.html';</script>");
}
$checkEmail->bind_param("s", $email_lowercase); // Check lowercase email
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
// Insert lowercase username and email for consistency
$insert = $conn->prepare("INSERT INTO users (fullname, username, id_number, email, dob, password) VALUES (?, ?, ?, ?, ?, ?)");
if (!$insert) {
    error_log("Prepare failed (insert): (" . $conn->errno . ") " . $conn->error);
    die("<script>alert('An error occurred during registration (DB4). Please try again.'); window.location.href = 'register.html';</script>");
}
// Bind parameters: s for string. Date is inserted as a 'YYYY-MM-DD' string.
$insert->bind_param("ssssss", $fullname, $username_lowercase, $idnumber, $email_lowercase, $dob_for_db, $password_hashed);

if ($insert->execute()) {
    // Registration successful
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
