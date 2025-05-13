<?php
// Must be at the very top before any output
session_start();

// Check if the user is actually logged in
if (!isset($_SESSION['user_loggedin']) || $_SESSION['user_loggedin'] !== true) {
    header("Location: login_student.html"); // Your actual login form page
    exit;
}

// Fetch user details from session, using your database column names as keys
// Ensure these are set correctly during your login process
$displayName = isset($_SESSION['fullname']) ? htmlspecialchars($_SESSION['fullname']) : "User";
$userEmail = isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : "user@example.com";
$userType = isset($_SESSION['user_type']) ? htmlspecialchars($_SESSION['user_type']) : "not_specified"; // Default from ENUM
$userGender = isset($_SESSION['user_gender']) ? htmlspecialchars($_SESSION['user_gender']) : "none"; // Default from ENUM

// Determine display emoji based on gender
$displayEmoji = '👤'; // Default generic user
if ($userGender == 'male') {
    $displayEmoji = '👨🏻'; // With skin tone
} elseif ($userGender == 'female') {
    $displayEmoji = '👩🏻'; // With skin tone
} elseif ($userGender == 'other' || $userGender == 'prefer_not_to_say') {
    $displayEmoji = '😊'; // Expressive emoji for other/prefer not to say
}
// If $userGender is 'none' or empty, it will use the default 👤
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NaviCare - Dashboard</title>
  <link rel="stylesheet" href="style.css"> <style>
    /* Style for the settings icon trigger */
    .profile-settings-trigger-container {
        position: absolute;
        top: 25px; /* Adjust for your layout */
        right: 30px; /* Adjust for your layout */
        z-index: 999;
    }
    #profileSettingsTriggerIcon {
        cursor: pointer;
        width: 32px;
        height: 32px;
        padding: 4px;
        border-radius: 50%;
        background-color: rgba(230, 230, 230, 0.6);
        box-shadow: 0 1px 4px rgba(0,0,0,0.25);
        transition: background-color 0.2s ease;
    }
    #profileSettingsTriggerIcon:hover {
        background-color: rgba(200, 200, 200, 0.8);
    }

    /* Profile Modal Styles */
    .profile-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.65);
        font-family: 'Segoe UI', sans-serif;
    }
    .profile-modal-content {
        background-color:rgba(255, 255, 255, 0.85);
        margin: 7% auto;
        padding: 25px 35px;
        width: 90%;
        max-width: 450px;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        position: relative;
        color: #333;
    }
    .profile-modal-close {
        color: #666;
        position: absolute;
        top: 15px;
        right: 20px;
        font-size: 28px;
        font-weight: bold;
        line-height: 1;
    }
    .profile-modal-close:hover,
    .profile-modal-close:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;
    }
    .profile-header {
        text-align: center;
        margin-bottom: 25px;
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 20px;
    }
    .profile-emoji-display {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background-color: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        margin: 0 auto 15px auto;
        color: #495057;
        border: 3px solid #ffffff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }
    .profile-header h2 {
        margin-bottom: 6px;
        color: #D81B60;
        font-size: 1.8em;
        font-weight: 600;
    }
    .profile-header p {
        font-size: 0.9em;
        color: #555;
        margin-bottom: 4px;
        line-height: 1.4;
    }
    .profile-header p.user-email {
        font-size: 0.85em;
        color: #777;
    }
    .profile-header p strong {
        color: #333;
        font-weight: 600;
    }
    .profile-settings h3 {
        font-size: 1.3em;
        color: #D81B60;
        margin-top: 5px;
        margin-bottom: 20px;
        text-align: center;
        font-weight: 600;
    }
    .setting-item {
        margin-bottom: 18px;
    }
    .setting-item label {
        display: block;
        margin-bottom: 7px;
        font-weight: 500;
        font-size: 0.9em;
        color: #495057;
    }
    .setting-item select,
    .setting-item .profile-action-button {
        padding: 10px 14px;
        border-radius: 6px;
        border: 1px solid #ced4da;
        font-size: 0.95em;
        width: 100%;
        box-sizing: border-box;
        background-color: #fff;
        color: #495057;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .setting-item select:focus,
    .setting-item .profile-action-button:focus {
        border-color: #e93772;
        box-shadow: 0 0 0 0.2rem rgba(233, 55, 114, 0.25);
        outline: 0;
    }
    .setting-item .profile-action-button {
        background-color: #e93772;
        color: white;
        cursor: pointer;
        border: none;
        font-weight: 500;
    }
    .setting-item .profile-action-button:hover {
        background-color: #d81b60;
    }
     .setting-item .profile-action-button:active {
        transform: translateY(1px);
    }
    .profile-actions {
        text-align: center;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #f0f0f0;
    }
    .profile-logout-btn {
        background-color: #6c757d;
        color: white;
        padding: 11px 22px;
        text-decoration: none;
        border-radius: 6px;
        display: inline-block;
        font-weight: 500;
        font-size: 0.95em;
        border: none;
        cursor: pointer;
        transition: background-color 0.2s ease, transform 0.1s ease;
    }
    .profile-logout-btn:hover {
        background-color: #5a6268;
    }
    .profile-logout-btn:active {
        transform: translateY(1px);
    }

    @media (max-width: 600px) {
        .profile-settings-trigger-container {
            top: 15px;
            right: 15px;
        }
        #profileSettingsTriggerIcon {
            width: 28px;
            height: 28px;
        }
        .profile-modal-content {
            margin: 5% auto;
            padding: 20px 25px;
            max-width: 95%;
        }
        .profile-header h2 { font-size: 1.6em; }
        .profile-emoji-display { width: 80px; height: 80px; font-size: 40px; }
        .profile-settings h3 { font-size: 1.2em; }
    }
  </style>
</head>
<body>
  <div class="gradient-overlay-bottom"></div>
  <div class="overlay">
    <div class="profile-settings-trigger-container">
        <img src="res/settings.png" alt="Profile Settings" id="profileSettingsTriggerIcon" title="Profile and Settings">
    </div>

    <div class="container">
      <div class="header"> <img src="res/school_and_dept_Logo.png" alt="school and dept">
      </div>
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
    </div>
  </div>
  <div id="profileModal" class="profile-modal">
    <div class="profile-modal-content">
        <span class="profile-modal-close" title="Close">&times;</span>
        <div class="profile-header">
            <div id="profileEmojiDisplay" class="profile-emoji-display">
                <?php echo $displayEmoji; ?>
            </div>
            <h2><?php echo $displayName; ?></h2>
            <p class="user-email"><?php echo $userEmail; ?></p>
            <p><strong>User Type:</strong> <span id="currentUserTypeDisplay"><?php echo ucwords(str_replace('_', ' ', $userType)); ?></span></p>
        </div>

        <div class="profile-settings">
            <h3>Profile Settings</h3>
            <div class="setting-item">
                <label for="profileGender">Gender:</label>
                <select id="profileGender" name="gender">
                    <option value="none" <?php if(empty($userGender) || $userGender == 'none') echo 'selected'; ?>>Not Specified</option>
                    <option value="male" <?php if($userGender == 'male') echo 'selected'; ?>>Male</option>
                    <option value="female" <?php if($userGender == 'female') echo 'selected'; ?>>Female</option>
                    <option value="other" <?php if($userGender == 'other') echo 'selected'; ?>>Other</option>
                    <option value="prefer_not_to_say" <?php if($userGender == 'prefer_not_to_say') echo 'selected'; ?>>Prefer not to say</option>
                </select>
            </div>
            <div class="setting-item">
                <label for="profileUserType">User Type:</label>
                <select id="profileUserType" name="userType">
                    <option value="not_specified" <?php if($userType == 'not_specified') echo 'selected'; ?>>Not Specified</option>
                    <option value="student" <?php if($userType == 'student') echo 'selected'; ?>>Student</option>
                    <option value="faculty" <?php if($userType == 'faculty') echo 'selected'; ?>>Faculty</option>
                </select>
            </div>
            <div class="setting-item">
                <button type="button" id="changePasswordBtn" class="profile-action-button">Change Password</button>
            </div>
            <div class="setting-item">
                <button type="button" id="saveProfileSettingsBtn" class="profile-action-button">Save Settings</button>
            </div>
        </div>

        <div class="profile-actions">
            <a href="logout.php" class="profile-logout-btn">Logout</a>
        </div>
    </div>
  </div>

  <script src="script.js"></script> <script>
    // Encapsulate profile modal specific JS
    (function() {
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById("profileModal");
            const triggerIcon = document.getElementById("profileSettingsTriggerIcon");
            const closeButton = modal ? modal.querySelector(".profile-modal-close") : null;
            const genderSelect = document.getElementById('profileGender');
            const userTypeSelect = document.getElementById('profileUserType'); // New selector
            const emojiDisplay = document.getElementById('profileEmojiDisplay');
            const userTypeDisplay = document.getElementById('currentUserTypeDisplay'); // For updating display text

            function getEmojiForGender(genderValue) {
                switch (genderValue) {
                    case 'male': return '👨🏻';
                    case 'female': return '👩🏻';
                    case 'other':
                    case 'prefer_not_to_say': return '😊';
                    case 'none':
                    case '':
                    default: return '👤';
                }
            }

            if (triggerIcon && modal) {
                triggerIcon.onclick = function(event) {
                    event.preventDefault();
                    modal.style.display = "block";
                    if (emojiDisplay && genderSelect) {
                         emojiDisplay.innerHTML = getEmojiForGender(genderSelect.value);
                    }
                    // Optionally, pre-fill user type display if needed, though PHP does it initially
                }
            }

            if (closeButton && modal) {
                closeButton.onclick = function() {
                    modal.style.display = "none";
                }
            }

            if (modal) {
                window.onclick = function(event) {
                    if (event.target == modal) {
                        modal.style.display = "none";
                    }
                }
            }

            if (genderSelect && emojiDisplay) {
                genderSelect.onchange = function() {
                    emojiDisplay.innerHTML = getEmojiForGender(this.value);
                };
            }

            const changePasswordBtn = document.getElementById('changePasswordBtn');
            if (changePasswordBtn) {
                changePasswordBtn.onclick = function() {
                    window.location.href = 'change_password.php';
                }
            }

            const saveProfileSettingsBtn = document.getElementById('saveProfileSettingsBtn');
            if (saveProfileSettingsBtn) {
                saveProfileSettingsBtn.onclick = function() {
                    const selectedGender = genderSelect ? genderSelect.value : null;
                    const selectedUserType = userTypeSelect ? userTypeSelect.value : null; // Get user type

                    if (selectedGender === null || selectedUserType === null) {
                        alert("Gender and User Type must be selected to save."); // Or handle more gracefully
                        return;
                    }

                    const formData = new FormData();
                    formData.append('gender', selectedGender);
                    formData.append('user_type', selectedUserType); // Add user_type to form data

                    fetch('update_profile_settings.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.text().then(text => {
                                let errorMsg = 'Network response was not ok.';
                                try {
                                    const errData = JSON.parse(text);
                                    if (errData && errData.message) {
                                        errorMsg = errData.message;
                                    }
                                } catch (e) {
                                    errorMsg = text.substring(0, 100);
                                }
                                throw new Error(errorMsg);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            alert(data.message || 'Settings saved successfully!');
                            // Update the displayed user type text in the modal header
                            if (userTypeDisplay && userTypeSelect) {
                                const selectedOptionText = userTypeSelect.options[userTypeSelect.selectedIndex].text;
                                userTypeDisplay.textContent = selectedOptionText;
                            }
                            // PHP session variables for gender and user_type need to be updated by 'update_profile_settings.php'
                            // for the changes to persist across page loads/modal reopens without a full page reload.
                            if (modal) modal.style.display = "none";
                        } else {
                            alert(data.message || 'Failed to save settings. Please try again.');
                        }
                    })
                    .catch(error => {
                        console.error('Error saving settings:', error);
                        alert('An error occurred: ' + error.message);
                    });
                }
            }
        });
    })();
  </script>
</body>
</html>
