<?php
$host = 'localhost';
$db = 'navicare';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$fullname = $_POST['fullname'];
$username = $_POST['username'];
$idnumber = $_POST['idnumber'];
$email = $_POST['email'];
$dob = $_POST['dob'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

$sql = "INSERT INTO users (fullname, username, id_number, email, dob, password) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $fullname, $username, $idnumber, $email, $dob, $password);

if ($stmt->execute()) {
  header("Location: Terms_and_Conditions.html"); //directly go to Terms and Condition
  exit();
} else {
  echo "Error: " . $stmt->error;
}
?>
