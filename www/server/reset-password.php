<?php
header('Content-Type: application/json');
session_start(); // Optional, depending on session usage

// Database credentials
$servername = "sql103.infinityfree.com";
$username = "if0_38074629"; // replace with your InfinityFree database username
$password = "eTuY3NCjICH"; // replace with your InfinityFree database password
$dbname = "if0_38074629_Mobile_finalProject"; // replace with your InfinityFree database name

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit();
}

// Handle JSON input
$input = json_decode(file_get_contents('php://input'), true);

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($input)) {
    // Retrieve and sanitize input
    $userInput = trim($input['emailOrUsername']);
    $newPassword = $input['newPassword'];

    // Validate input fields
    if (empty($userInput) || empty($newPassword)) {
        echo json_encode(["success" => false, "message" => "All fields are required"]);
        exit();
    }

    // Validate password strength
    if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $newPassword)) {
        echo json_encode(["success" => false, "message" => "Password does not meet security requirements"]);
        exit();
    }

    // Hash the new password for security
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    // SQL query to update the password for matching email or username
    $sql = "UPDATE Users SET password = ? WHERE email = ? OR username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $hashedPassword, $userInput, $userInput);

    // Execute the query and check the result
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(["success" => true, "message" => "Password reset successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => "User not found"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Error resetting password"]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}

$conn->close();
?>