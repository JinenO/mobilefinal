<?php
session_start();

// Database connection
$servername = "sql101.infinityfree.com";
$username = "if0_38064906"; // replace with your InfinityFree database username
$password = "InfinityAcc329"; // replace with your InfinityFree database password
$dbname = "if0_38064906_final"; // replace with your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // SQL to fetch the user record
    $sql = "SELECT id, password FROM Users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashedPassword);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashedPassword)) {
            $_SESSION['user_id'] = $id;
            echo "Login successful!";
        } else {
            echo "Invalid password. Please try again.";
        }
    } else {
        echo "No user found with that email. Please try again.";
    }

    $stmt->close();
}

$conn->close();
?>
