<?php
// Set headers for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Connecting to the database
$host = "sql103.infinityfree.com";
$username = "if0_38074629";
$password = "eTuY3NCjICH";
$database = "if0_38074629_Mobile_finalProject";

$conn = new mysqli($host, $username, $password, $database);
// Check if the connection is established before proceeding
if (!$conn) {
    echo json_encode(["error" => "Failed to connect to the database."]);
    exit;
}

// Get the collection_id from GET parameters
$collection_id = $_GET['collection_id'] ?? null;

// Check if collection_id is provided
if ($collection_id) {
    // SQL query to fetch the data
    $query = "
        SELECT 
            s.state_name, 
            a.attraction_name, 
            co.country_name,
            h.hotel_name
        FROM collection c
        LEFT JOIN attractions a ON c.attraction_id = a.attraction_id
        LEFT JOIN hotels h ON c.hotel_id = h.hotel_id
        LEFT JOIN states s ON (a.state_id = s.state_id OR h.state_id = s.state_id)
        LEFT JOIN countries co ON (s.country_id = co.country_id)
        WHERE c.collection_id = ?
    ";

    // Prepare the query statement
    $stmt = $conn->prepare($query);

    if ($stmt) {
        // Bind the parameter and execute the query
        $stmt->bind_param("i", $collection_id); // "i" means integer
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if the result has any rows
        if ($row = $result->fetch_assoc()) {
            echo json_encode([
                "state_name" => $row['state_name'],
                "attraction_name" => $row['attraction_name'],
                "country_name" => $row['country_name'],
                "hotel_name" => $row['hotel_name']
            ]);
        } else {
            // No matching data found for the collection_id
            echo json_encode(["error" => "No data found for the provided collection ID"]);
        }

        // Close the statement
        $stmt->close();
    } else {
        echo json_encode(["error" => "Failed to prepare query"]);
    }
} else {
    // Missing collection_id in the request
    echo json_encode(["error" => "Collection ID is missing"]);
}

// Close the database connection (if needed)
$conn->close();
?>



