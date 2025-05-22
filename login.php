<?php
// Must be at the very top before any output
session_start();

// Check if this script is accessed via a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // If not a POST request, redirect to the login page
    header("Location: login_student.html"); 
    exit();
}

$host = 'localhost';
$db = 'navicare';
$user = 'root'; // Your database username
$pass = '';     // Your database password

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    showError("An internal server error occurred. Please try again later."); // showError function defined below
    // exit() is called within showError
}

// Check if all required form data is submitted via POST
// This check is now more specific to a POST request context due to the check above.
if (!isset($_POST['username'], $_POST['idnumber'], $_POST['password']) || 
    empty(trim($_POST['username'])) || empty(trim($_POST['idnumber'])) || empty(trim($_POST['password']))) {
    showError("Please fill in all required fields.");
    // exit() is called within showError
}

$username_posted = $_POST['username'];
$idnumber_posted = $_POST['idnumber'];
$password_posted = $_POST['password'];

// Prepare statement to prevent SQL injection
$sql = "SELECT id, fullname, username, email, password, user_type, gender FROM users WHERE username = ? AND id_number = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    showError("An internal server error occurred during login preparation.");
    // exit() is called within showError
}

$stmt->bind_param("ss", $username_posted, $idnumber_posted);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user_data = $result->fetch_assoc();
    if (password_verify($password_posted, $user_data['password'])) {
        $_SESSION['user_loggedin'] = true;
        $_SESSION['user_id'] = $user_data['id'];
        $_SESSION['username'] = $user_data['username'];
        $_SESSION['fullname'] = $user_data['fullname'];
        $_SESSION['email'] = $user_data['email'];
        $_SESSION['user_type'] = $user_data['user_type'];
        $_SESSION['user_gender'] = $user_data['gender'];

        // Redirect to the dashboard page
        header("Location: welcome_page.php"); 
        exit();
    } else {
        $stmt->close();
        $conn->close();
        showError("Incorrect username, ID number, or password.");
        // exit() is called within showError
    }
} else {
    $stmt->close();
    $conn->close();
    showError("Incorrect username, ID number, or password.");
    // exit() is called within showError
}

// Function to show error (using JavaScript alert and redirect)
function showError($message) {
    // Ensure no further output interferes with headers or script execution
    if (session_status() == PHP_SESSION_ACTIVE) {
        session_write_close(); // Close session before outputting JS if you want to be very careful
    }
    // Clear any previously sent headers if possible (though session_start and output make this tricky)
    // @ob_clean(); 

    echo "
    <script>
      alert('" . addslashes($message) . "');
      window.location.href = 'login_student.html'; // Or your actual login form page
    </script>";
    exit(); // Stop script execution
}
?>