<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = ""; // Default password is empty
$database = "form_data";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve form data
$fullname = $_POST['fullname'];
$dob = $_POST['dob'];
$gender = $_POST['gender'];
$email = $_POST['email'];
$contact = $_POST['contact'];
$username = $_POST['username'];
$password = $_POST['password'];
$confirmpassword = $_POST['confirmpassword'];

// Insert data into the database
$sql = "INSERT INTO pcldata (fullname, dob, gender, email, contact, username, password, confirmpassword) VALUES ('$fullname', '$dob', '$gender' , '$email', '$contact', '$username' ,'$password', '$confirmpassword')";
if ($conn->query($sql) === TRUE) {
    echo "Record added successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close connection
$conn->close();
?>
