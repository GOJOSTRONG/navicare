<?php
// Must be at the very top before any output
session_start();

// Set content type to JSON for the response
header('Content-Type: application/json');

$host = 'localhost';
$db   = 'navicare';
$user = 'root';
$pass = ''; // Your database password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Response array
$response = ['success' => false, 'message' => 'An unknown error occurred.'];

// Check if the user is logged in and username is set in session
// IMPORTANT: Use the primary session identifier you set during login.
// If you set $_SESSION['user_id'] (from the auto-incrementing 'id' column), use that.
// If you set $_SESSION['username'], use that. Let's assume 'user_id' for better practice.
if (!isset($_SESSION['user_loggedin']) || $_SESSION['user_loggedin'] !== true || !isset($_SESSION['user_id'])) {
    // If using username in session: !isset($_SESSION['username'])
    $response['message'] = 'Access Denied: User not logged in or session identifier missing.';
    echo json_encode($response);
    exit;
}

// Check if data is received via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and validate gender
    $newGender = $_POST['gender'] ?? null;
    $allowedGenders = ['male', 'female', 'other', 'prefer_not_to_say', 'none', ''];
    if ($newGender === null || !in_array($newGender, $allowedGenders, true)) {
        $response['message'] = 'Invalid or missing gender value provided.';
        echo json_encode($response);
        exit;
    }
    if ($newGender === '') { // Treat empty string as 'none' for DB consistency
        $newGender = 'none';
    }

    // Retrieve and validate user_type
    $newUserType = $_POST['user_type'] ?? null;
    // Ensure these values match your ENUM definition in the 'users' table
    $allowedUserTypes = ['student', 'faculty'];
    if ($newUserType === null || !in_array($newUserType, $allowedUserTypes, true)) {
        $response['message'] = 'Invalid or missing user type value provided.';
        echo json_encode($response);
        exit;
    }
    
    // Get the logged-in user's identifier from the session
    $loggedInUserId = $_SESSION['user_id']; // Or $_SESSION['username'] if you use that

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);

        // Prepare the SQL UPDATE statement
        // Assumes your 'users' table has 'gender', 'user_type' columns, and an 'id' column as PK
        $sql = "UPDATE users SET gender = :gender, user_type = :user_type WHERE id = :user_id";
        // If using username as identifier: WHERE username = :username
        
        $stmt = $pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':gender', $newGender, PDO::PARAM_STR);
        $stmt->bindParam(':user_type', $newUserType, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $loggedInUserId, PDO::PARAM_INT); // Or PDO::PARAM_STR if using username

        // Execute the statement
        if ($stmt->execute()) {
            // Even if rowCount is 0 (values were the same), the operation was "successful"
            // Update the session variables
            $_SESSION['user_gender'] = $newGender;
            $_SESSION['user_type'] = $newUserType;
            
            $response['success'] = true;
            if ($stmt->rowCount() > 0) {
                $response['message'] = 'Profile settings updated successfully.';
            } else {
                $response['message'] = 'Profile settings reflect the new values (no change made if values were the same).';
            }
        } else {
            $response['message'] = 'Database update failed.';
        }
    } catch (PDOException $e) {
        error_log("PDO Error in update_profile_settings.php: " . $e->getMessage());
        $response['message'] = 'Database error occurred. Please try again later.';
        // $response['debug_error'] = $e->getMessage(); // For development only
    }
} else {
    $response['message'] = 'Invalid request method.';
    //yogg talaka
}

// Send the JSON response
echo json_encode($response);
?>
