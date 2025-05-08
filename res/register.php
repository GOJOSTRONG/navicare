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


$checkFullnameID = $conn->prepare("SELECT * FROM users WHERE fullname = ? AND id_number = ?");
$checkFullnameID->bind_param("ss", $fullname, $idnumber);
$checkFullnameID->execute();
$result1 = $checkFullnameID->get_result();

if ($result1->num_rows > 0) {
  echo "<script>
          alert('A user with this Full Name and ID Number already exists.');
          window.location.href = 'register.html';
        </script>";
  exit();
}


$checkUsername = $conn->prepare("SELECT * FROM users WHERE username = ?");
$checkUsername->bind_param("s", $username);
$checkUsername->execute();
$result2 = $checkUsername->get_result();

if ($result2->num_rows > 0) {
  echo "<script>
          alert('Username already taken. Please choose another.');
          window.location.href = 'register.html';
        </script>";
  exit();
}

$insert = $conn->prepare("INSERT INTO users (fullname, username, id_number, email, dob, password) VALUES (?, ?, ?, ?, ?, ?)");
$insert->bind_param("ssssss", $fullname, $username, $idnumber, $email, $dob, $password);

if ($insert->execute()) {
  header("Location: Terms_and_Conditions.html");
  exit();
} else {
  echo "Error: " . $insert->error;
}
?>
