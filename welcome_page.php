<?php
// Must be at the very top before any output
session_start();

// Check if the user is actually logged in and the display name is set
if (!isset($_SESSION['user_loggedin']) || $_SESSION['user_loggedin'] !== true || !isset($_SESSION['display_name'])) {
    // If not logged in, redirect to the login page
    header("Location: login_student.html"); // Or your actual login form page
    exit;
}

// Get the display name and escape it for security (to prevent XSS)
$displayName = htmlspecialchars($_SESSION['display_name']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NaviCare - Welcome <?php echo $displayName; ?></title>
    <link rel="stylesheet" href="style.css"> </head>
<body>
    <div class="gradient-overlay-bottom"></div>
    <div class="overlay">
        <div class="splash-screen">
            <div class="school-logos">
                <img src="res/school_and_dept_Logo.png" alt="School and Department Logos">
            </div>
            <div class= "centered-text2">Hi <?php echo $displayName; ?>!<br></div>
            <div class="welcome-container">
                <div class="welcome-text"> Welcome to</div>
            </div>
            
            <img src="res/navicare_icon.png" alt="NaviCare Logo" class="navicare-logo">
            
            <button class="continue-button" onclick="window.location.href='admin_dashboard.php'">Continue to Dashboard</button>
            </div>
    </div> </body>
</html>