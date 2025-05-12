<?php
// Must be at the very top before any output
session_start();

// Database connection details
$host = 'localhost';
$db   = 'navicare'; // Your database name
$user = 'root';     // Your database username
$pass = '';         // Your database password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$message = ''; // For displaying success or error messages
$message_type = ''; // 'success' or 'error'

// Check if the user is logged in
if (!isset($_SESSION['user_loggedin']) || $_SESSION['user_loggedin'] !== true || !isset($_SESSION['username'])) {
    // If not logged in, redirect to the login page
    header("Location: login_student.html"); // Or your main login page
    exit;
}

$loggedInUsername = $_SESSION['username']; // Assuming username is stored in session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Basic Validations
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $message = 'All password fields are required.';
        $message_type = 'error';
    } elseif ($newPassword !== $confirmPassword) {
        $message = 'New password and confirm password do not match.';
        $message_type = 'error';
    } else {
        try {
            $pdo = new PDO($dsn, $user, $pass, $options);

            // Fetch current hashed password from the database
            $stmt = $pdo->prepare("SELECT password FROM users WHERE username = :username");
            $stmt->bindParam(':username', $loggedInUsername, PDO::PARAM_STR);
            $stmt->execute();
            $userRow = $stmt->fetch();

            if ($userRow && password_verify($currentPassword, $userRow['password'])) {
                // Current password is correct, hash the new password
                $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

                // Update the password in the database
                $updateStmt = $pdo->prepare("UPDATE users SET password = :new_password WHERE username = :username");
                $updateStmt->bindParam(':new_password', $newPasswordHash, PDO::PARAM_STR);
                $updateStmt->bindParam(':username', $loggedInUsername, PDO::PARAM_STR);

                if ($updateStmt->execute()) {
                    $message = 'Password updated successfully! You will be redirected shortly.';
                    $message_type = 'success';
                    // Optional: Redirect after a short delay or provide a link back
                    // For immediate redirect:
                    // header("Location: your_dashboard_page.php?password_changed=success");
                    // exit;
                    // For this example, we'll show message and let user click back or use JS redirect.
                    echo "<script>
                            setTimeout(function() {
                                window.location.href = 'admin_dashboard.php';
                            }, 3000);
                          </script>";
                } else {
                    $message = 'Failed to update password. Please try again.';
                    $message_type = 'error';
                }
            } else {
                $message = 'Incorrect current password.';
                $message_type = 'error';
            }
        } catch (PDOException $e) {
            error_log("PDO Error in change_password.php: " . $e->getMessage());
            $message = 'A database error occurred. Please try again later.';
            $message_type = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - NaviCare</title>
    <link rel="stylesheet" href="style.css"> <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f7f6; /* Light background */
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }
        .change-password-container {
            background-color: #ffffff;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }
        .change-password-container h2 {
            color: #D81B60; /* Theme color */
            margin-bottom: 25px;
            font-size: 1.8em;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 0.9em;
            color: #495057;
        }
        .form-group input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 1em;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        .form-group input[type="password"]:focus {
            border-color: #e93772; /* Theme focus color */
            box-shadow: 0 0 0 0.2rem rgba(233, 55, 114, 0.25);
            outline: 0;
        }
        .action-buttons {
            margin-top: 30px;
            display: flex;
            justify-content: space-between; /* For side-by-side buttons */
            gap: 15px;
        }
        .action-buttons button,
        .action-buttons a {
            padding: 12px 20px;
            border-radius: 6px;
            font-size: 1em;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            border: none;
            flex-grow: 1; /* Make buttons take equal space */
            text-align: center;
            transition: background-color 0.2s ease, transform 0.1s ease;
        }
        .action-buttons button[type="submit"] {
            background-color: #e93772; /* Theme color */
            color: white;
        }
        .action-buttons button[type="submit"]:hover {
            background-color: #d81b60; /* Darker theme color */
        }
        .action-buttons .cancel-btn {
            background-color: #6c757d; /* Secondary/cancel color */
            color: white;
        }
        .action-buttons .cancel-btn:hover {
            background-color: #5a6268;
        }
        .action-buttons button:active,
        .action-buttons a:active {
             transform: translateY(1px);
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 0.9em;
            text-align: center;
        }
        .message.success {
            background-color: #d4edda; /* Light green */
            color: #155724;         /* Dark green */
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da; /* Light red */
            color: #721c24;         /* Dark red */
            border: 1px solid #f5c6cb;
        }
        @media (max-width: 480px) {
            .change-password-container {
                padding: 20px;
            }
            .action-buttons {
                flex-direction: column; /* Stack buttons on small screens */
            }
        }
    </style>
</head>
<body>
    <div class="change-password-container">
        <h2>Change Your Password</h2>

        <?php if (!empty($message)): ?>
            <div class="message <?php echo htmlspecialchars($message_type); ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form action="change_password.php" method="POST">
            <div class="form-group">
                <label for="current_password">Current Password:</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required minlength="8">
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
            </div>
            <div class="action-buttons">
                <button type="submit">Update Password</button>
                <a href="index.php" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
