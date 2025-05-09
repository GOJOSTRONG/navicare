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
  <title>NaviCare - Admin Dashboard</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <!-- Gradient pink-white -->
  <div class="gradient-overlay-bottom"></div>
  <div class="overlay">
    <!-- Main Content -->
    <div class="container">
      <div class="header">
        <img src="res/school_and_dept_Logo.png" alt="school and dept">
      </div>
      <a href="logout.php" class="back-button2">Logout</a>
      <div class="navicare-icon">
        <img src="res/navicare_icon.png" alt="NaviCare Logo">
      </div>
      <div class="select service">
        <p class="select-service">How can we help? </p>
        <div class="service-types">
          <a href="res/nav_for_wvsu.html" class="Navigate-Hospital">
            <img src="res/nav_for_wvsu.png" alt="Navigate Hospital Button">
            <p>Navigate WVSU Affiliated Hospitals</p>
          </a>
          <a href="care_for_your_health.html" class="Care-for-Your-Health">
            <img src="res/guest_icon .png" alt="Care for Your Health Button">
            <p>Learn Nursing Care </p>
          </a>
        </div>
      </div>
    </div> <!-- .container -->
  </div> <!-- .overlay -->
 
  <!-- JavaScript -->
  <script src="script.js"></script>
</body>
</html>