<?php
session_start();
ob_start(); // Start output buffering
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Database connection
$host = "sql103.infinityfree.com";
$username = "if0_38074629";
$password = "eTuY3NCjICH";
$database = "if0_38074629_Mobile_finalProject";
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Make sure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in."]);
    exit;
}

// Fetch form data
$id = $_SESSION['user_id']; // Assuming the user is logged in
$place_name = $_POST['place_name'] ?? '';
$state = $_POST['state'] ?? '';
$country = $_POST['country'] ?? '';
$travel_date = $_POST['travel_date'] ?? '';
$feeling = $_POST['feeling'] ?? '';
$impression = $_POST['impression'] ?? '';
$spending_amount = $_POST['spending_amount'] ?? 0;
$spending_currency = $_POST['spending_currency'] ?? '';
$converted_amount = $_POST['converted_amount'] ?? 0;
$converted_currency = $_POST['converted_currency'] ?? '';
$image = $_FILES['image_path'] ?? null; // Retrieve the uploaded image directly
$food_spending = (int) ($_POST['food_spending'] ?? 0);
$transport_spending = (int) ($_POST['transport_spending'] ?? 0);
$other_spending = (int) ($_POST['other_spending'] ?? 0);
$collection_id = $_POST['collection_id'] ?? null;

// Validate mandatory fields
if (!$place_name || !$state || !$country || !$travel_date) {
    echo json_encode(["error" => "Missing required fields"]);
    exit;
}

// Image file processing
$image_path = null;
if ($image && $image['tmp_name']) {
    // Change the path to the `uploads/` folder located in the root (htdocs) folder
    $upload_dir = '../uploads/'; // Relative path to uploads directory located under htdocs

    // Check if the uploads folder exists, if not, no need to create it
    if (!file_exists($upload_dir)) {
        echo json_encode(["error" => "Uploads folder does not exist"]);
        exit;
    }

    $image_name = preg_replace("/[^a-zA-Z0-9_-]/", "", basename($image['name'])); // Secure the filename
    $image_path = $upload_dir . $image_name . '_' . uniqid() . '.png'; // Create the file path

    if (!move_uploaded_file($image['tmp_name'], $image_path)) {
        echo json_encode(["error" => "Failed to save image"]);
        exit;
    }
} else {
    echo json_encode(["error" => "No image uploaded"]);
    exit;
}

// Insert query with spending fields
$query = "INSERT INTO journal (
    id, place_name, state, country, travel_date, feeling, impression, 
    spending_amount, spending_currency, converted_amount, converted_currency, image_path, food_spending, transport_spending, other_spending
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($query);

// Debug values to ensure proper passing of data


$stmt->bind_param("issssssdsdssiii", $id, $place_name, $state, $country, $travel_date, $feeling, $impression, $spending_amount, $spending_currency, $converted_amount, $converted_currency, $image_path, $food_spending, $transport_spending, $other_spending);

if ($stmt->execute()) {
    // Delete collection_id from collection table if provided
    if ($collection_id) {
        $delete_query = "DELETE FROM collection WHERE collection_id = ?";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param("i", $collection_id);
        if (!$delete_stmt->execute()) {
            echo json_encode(["error" => "Failed to delete collection entry."]);
            $delete_stmt->close();
            exit;
        }
        $delete_stmt->close();
    }

    // Respond with success and redirect URL
    echo json_encode(["success" => true, "redirect" => "displayJournal.html"]);
} else {
    echo json_encode(["error" => "Failed to insert journal data."]);
}
$stmt->close();
$conn->close();
ob_end_flush(); // Ensure the response is sent

?>

