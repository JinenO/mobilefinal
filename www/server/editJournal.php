<?php
session_start();
ob_start(); // Start output buffering
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');


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

// Retrieve journal_id from GET or POST request
$journalId = isset($_GET['journal_id']) ? $_GET['journal_id'] : null;

if ($_SERVER['REQUEST_METHOD'] == 'GET' && $journalId) {
    // Retrieve journal data from database for GET request
    $query = "SELECT * FROM journal WHERE journal_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $journalId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode($data); // Send the fetched data as JSON
    } else {
        echo json_encode(['error' => 'Journal entry not found.']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['journal_id'])) {
    // Update journal data in database for POST request (when editing)
    $journalId = $_POST['journal_id'];
    $travelDate = $_POST['travel_date'];
    $feeling = $_POST['feeling'];
    $impression = $_POST['impression'];
    $foodSpending = $_POST['food_spending'];
    $transportSpending = $_POST['transport_spending'];
    $otherSpending = $_POST['other_spending'];
    $spendingAmount = $_POST['spending_amount'];
    
    $spendingCurrency = $_POST['spending_currency'];
    $convertedAmount = $_POST['converted_amount'];
    $convertedCurrency = $_POST['converted_currency'];
    $imageName = isset($_POST['image_name']) ? $_POST['image_name'] : null;
    
    // Handle the image upload (optional, if new image is uploaded)
    if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        $imagePath = $uploadDir . basename($_FILES['image_path']['name']);
        move_uploaded_file($_FILES['image_path']['tmp_name'], $imagePath);
    } else {
        // Keep the existing image if no new image is uploaded
        $query = "SELECT image_path FROM journal WHERE journal_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $journalId);
        $stmt->execute();
        $result = $stmt->get_result();
        $currentData = $result->fetch_assoc();
        $imagePath = $currentData['image_path'];  // Keep the old image path
    }

    // Update the journal entry in the database
    $updateQuery = "UPDATE journal SET
        travel_date = ?,
        feeling = ?,
        impression = ?,
        food_spending = ?,
        transport_spending = ?,
        other_spending = ?,
        spending_amount = ?,
        spending_currency = ?,
        converted_amount = ?,
        converted_currency = ?,
        image_path = ?
        WHERE journal_id = ?";
    
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param('ssssdddsdssi', $travelDate, $feeling, $impression, $foodSpending, $transportSpending,
                    $otherSpending, $spendingAmount, $spendingCurrency, $convertedAmount, $convertedCurrency, $imagePath, $journalId);

    if ($stmt->execute()) {
        echo json_encode(['success' => 'Journal updated successfully!']);
    } else {
        echo json_encode(['error' => 'Failed to update the journal.']);
    }
} else {
    // If missing journal_id or invalid request
    echo json_encode(['error' => 'Invalid request method or missing journal_id.']);
}

$stmt->close();
$conn->close();
ob_end_flush(); 
?>
