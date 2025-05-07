<?php
session_start();

$host = 'localhost';
$db = 'navicare';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$username = $_POST['username'];
$idnumber = $_POST['idnumber'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE username = ? AND id_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $idnumber);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
  $user = $result->fetch_assoc();
  if (password_verify($password, $user['password'])) {
    $_SESSION['user'] = $user['username'];
    header("Location: welcome.html"); // Change to your target page
    exit();
  } else {
    showError("Incorrect password.");
  }
} else {
  showError("User not found.");
}
function showError($message) {
  echo "
  <script>
    alert('$message');
    window.location.href = 'login_student.html'; // Redirect back to login page
  </script>";
}
?>
<? echo $_SESSION["user"]; ?>