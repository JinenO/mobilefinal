<?php
// Database credentials
$servername = "sql101.infinityfree.com";
$username = "if0_38064906"; // replace with your InfinityFree database username
$password = "InfinityAcc329"; // replace with your InfinityFree database password
$dbname = "if0_38064906_final"; // replace with your InfinityFree database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from the form
    $email = $_POST['email'];
    $username = $_POST['username'];
    $birthday = $_POST['birthday'];
    $password = $_POST['password'];
    $gender = $_POST['gender'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert data into the database
    $sql = "INSERT INTO Users (email, username, birthday, password, gender) VALUES ('$email', '$username', '$birthday', '$hashed_password', '$gender')";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>