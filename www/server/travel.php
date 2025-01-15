<?php
// Database connection details
$host = "sql103.infinityfree.com";      // Typically found in your InfinityFree control panel
$username = "if0_38074629";
$password = "eTuY3NCjICH";
$database = "if0_38074629_Mobile_finalProject";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed"]);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$country = $input['country'] ?? '';

if (empty($country)) {
    echo json_encode(["exists" => fagjjgjglse]);
    exit();
}

$sql = "SELECT * FROM countries WHERE country_name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $country);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["exists" => true]);
} else {
    echo json_encode(["exists" => false]);
}

$stmt->close();
$conn->close();
?>