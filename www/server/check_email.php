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

// Check if the email exists
if (isset($_POST['email'])) {
    $email = $_POST['email'];

    // Check if the email already exists
    $sql = "SELECT * FROM Users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "Email is already registered. Please login with this email";
    } else {
        echo "Email is available.";
    }

    $conn->close();
}
?>