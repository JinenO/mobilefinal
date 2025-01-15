<?php
// Start the session and allow necessary headers
session_start();
ob_start(); // Start output buffering
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('HTTP/1.1 200 OK');
    exit;
}

// Database connection
$host = "sql103.infinityfree.com";
$username = "if0_38074629";
$password = "eTuY3NCjICH";
$database = "if0_38074629_Mobile_finalProject";
$conn = new mysqli($host, $username, $password, $database);

// Check user login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch journals
    fetchJournals($conn, $userId);
} 


// Fetch journals function
function fetchJournals($conn, $userId) {
    $sql = "SELECT * FROM journal WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $journals = $result->fetch_all(MYSQLI_ASSOC);

    if ($journals) {
        echo json_encode(['success' => true, 'data' => $journals]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No records found']);
    }
}
// Check for force delete action
if (isset($_GET['action']) && $_GET['action'] == 'force_delete' && isset($_GET['journal_id'])) {
    $journal_id = $_GET['journal_id']; // Get journal_id from request

    // Force delete the journal entry from the database
    $query = "DELETE FROM journal WHERE journal_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $journal_id); // Bind journal_id as an integer

    if ($stmt->execute()) {
        echo json_encode(["success" => true]); // Notify success
    } else {
        echo json_encode(["success" => false, "error" => "Failed to delete journal"]); // Notify failure
    }
    exit();
}


// Close the connection at the end of the script
$conn->close();


ob_end_flush(); // Ensure the response is sent
?>
