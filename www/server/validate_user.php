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
    // Retrieve JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    $emailOrUsername = $input['emailOrUsername'];
    $birthday = $input['birthday'];
    $gender = $input['gender'];

    // SQL to fetch the user record
    $sql = "SELECT id FROM Users WHERE (email = ? OR username = ?) AND birthday = ? AND gender = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $emailOrUsername, $emailOrUsername, $birthday, $gender);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(['valid' => true]);
    } else {
        echo json_encode(['valid' => false]);
    }

    $stmt->close();
}

$conn->close();
?>