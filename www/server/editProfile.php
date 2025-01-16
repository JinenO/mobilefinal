<?php
session_start();
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

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
$data = json_decode(file_get_contents("php://input"), true);

$username = $data['username'];
$birthday = $data['birthday'];
$gender = $data['gender'];

// Update user profile
$sql = "UPDATE Users SET username = ?, birthday = ?, gender = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssi", $username, $birthday, $gender, $user_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Failed to update profile"]);
}

$stmt->close();
$conn->close();
?>