<?php
// Must be at the very top before any output
session_start();

$host = 'localhost';
$db = 'navicare';
$user = 'root'; // Your database username
$pass = '';     // Your database password

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    // It's better to log this error than to die directly on a production server
    error_log("Connection failed: " . $conn->connect_error);
    showError("An internal server error occurred. Please try again later.");
    exit(); // Stop script execution
}

// Check if form data is submitted
if (!isset($_POST['username'], $_POST['idnumber'], $_POST['password'])) {
    showError("Please fill in all required fields.");
    exit();
}

$username_posted = $_POST['username'];
$idnumber_posted = $_POST['idnumber'];
$password_posted = $_POST['password'];

// Prepare statement to prevent SQL injection
// Assuming 'username' in the DB is the name you want to display.
// If you have a 'first_name' or 'full_name' column, select that too.
$sql = "SELECT username, password /*, first_name, last_name */ FROM users WHERE username = ? AND id_number = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    showError("An internal server error occurred during login preparation.");
    exit();
}

$stmt->bind_param("ss", $username_posted, $idnumber_posted);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user_data = $result->fetch_assoc();
    // Verify the password
    if (password_verify($password_posted, $user_data['password'])) {
        // Password is correct, set session variables
        $_SESSION['user_loggedin'] = true;

        // Store the name to be displayed.
        // Change $user_data['username'] to $user_data['first_name'] or similar if you have that field
        $_SESSION['display_name'] = $user_data['username'];

        // Redirect to the welcome page (must be a .php file)
        header("Location: welcome_page.php");
        exit();
    } else {
        $stmt->close();
        $conn->close();
        showError("Incorrect username, ID number, or password.");
    }
} else {
    $stmt->close();
    $conn->close();
    showError("Incorrect username, ID number, or password.");
}

// Function to show error (using JavaScript alert and redirect)
function showError($message) {
    // It's better to display errors on the login page itself rather than alert,
    // but for simplicity and matching original code:
    echo "
    <script>
      alert('" . addslashes($message) . "');
      window.location.href = 'login_student.html'; // Or your actual login form page
    </script>";
    exit(); // Stop script execution
}
?>