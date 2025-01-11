<?php
session_start();

// Database connection
$servername = "sql103.infinityfree.com";
$username = "if0_38074629"; // Replace with your InfinityFree database username
$password = "eTuY3NCjICH"; // Replace with your InfinityFree database password
$dbname = "if0_38074629_Mobile_finalProject"; // Replace with your InfinityFree database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit();
}

$user_id = $_SESSION['user_id'];

// SQL to fetch user details
$sql = "SELECT username, email, birthday, gender FROM Users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo json_encode($user);
} else {
    echo json_encode(["error" => "User not found"]);
}

$stmt->close();
$conn->close();
?>