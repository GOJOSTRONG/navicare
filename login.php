<?php
// Must be at the very top before any output
session_start();

$host = 'localhost';
$db = 'navicare';
$user = 'root'; // Your database username
$pass = '';     // Your database password

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    // Log this error rather than die directly on a production server
    error_log("Connection failed: " . $conn->connect_error);
    showError("An internal server error occurred. Please try again later."); // showError function defined below
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
// Fetch all necessary user details for the session
$sql = "SELECT id, fullname, username, email, password, user_type, gender FROM users WHERE username = ? AND id_number = ?";
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
        $_SESSION['user_id'] = $user_data['id']; // Store the user's primary ID
        $_SESSION['username'] = $user_data['username']; // Store username (can be used as a unique identifier if needed)
        
        // These are crucial for the dashboard and profile pop-up
        $_SESSION['fullname'] = $user_data['fullname']; // This will be the display name
        $_SESSION['email'] = $user_data['email'];
        $_SESSION['user_type'] = $user_data['user_type'];
        $_SESSION['user_gender'] = $user_data['gender'];

        // Redirect to the dashboard page
        // IMPORTANT: Change 'navicare_dashboard_updated_v2.php' if your dashboard file has a different name
        header("Location: admin_dashboard.php"); 
        exit();
    } else {
        // Incorrect password
        $stmt->close();
        $conn->close();
        showError("Incorrect username, ID number, or password.");
    }
} else {
    // User not found with the given username and id_number
    $stmt->close();
    $conn->close();
    showError("Incorrect username, ID number, or password.");
}

// Function to show error (using JavaScript alert and redirect)
function showError($message) {
    // It's better to display errors on the login page itself rather than alert,
    // but for simplicity and matching your original style:
    echo "
    <script>
      alert('" . addslashes($message) . "');
      window.location.href = 'login_student.html'; // Or your actual login form page
    </script>";
    exit(); // Stop script execution
}
?>
