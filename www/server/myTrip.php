<?php
session_start();

// Database connection
$host = "sql103.infinityfree.com";
$username = "if0_38074629";
$password = "eTuY3NCjICH";
$database = "if0_38074629_Mobile_finalProject";
$conn = new mysqli($host, $username, $password, $database);

// Check for errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check for delete action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['collection_id'])) {
    $collection_id = $_GET['collection_id'];

    // Delete the collection from the database
    $query = "DELETE FROM collection WHERE collection_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $collection_id); // Bind collection_id as integer

    if ($stmt->execute()) {
        echo json_encode(["success" => true]); // Notify success
    } else {
        echo json_encode(["success" => false, "error" => "Failed to delete collection"]); // Notify failure
    }
    exit();
}


$user_id = $_SESSION['user_id'] ?? null;

if ($user_id) {
    $query = "
        SELECT c.collection_id, c.attraction_id, c.hotel_id,
               s.state_name AS state, co.country_name AS country,
               a.attraction_name, a.imageD AS attraction_image, a.state_id, 
               a.category AS attraction_category, a.description AS attraction_description, 
               a.rating AS attraction_rating, a.location AS attraction_location, 
               a.opening_hours AS attraction_opening_hours, a.entrance_fee AS attraction_entrance_fee, 
               a.nearby_attraction AS attraction_nearby_attraction, a.website AS attraction_website,
               h.hotel_name, h.address AS hotel_address, h.image AS hotel_image, 
               h.star_rating AS hotel_star_rating, h.price_range AS hotel_price_range,
               h.website AS hotel_website, h.description AS hotel_description, 
               h.nearbyAttraction AS hotel_nearbyAttraction,
               hs.state_name AS hotel_state, hco.country_name AS hotel_country

        FROM collection c
        LEFT JOIN attractions a ON c.attraction_id = a.attraction_id
        LEFT JOIN hotels h ON c.hotel_id = h.hotel_id
        
        LEFT JOIN states s ON a.state_id = s.state_id
        LEFT JOIN countries co ON s.country_id = co.country_id
        
        -- Join for hotel state and country
        LEFT JOIN states hs ON h.state_id = hs.state_id
        LEFT JOIN countries hco ON hs.country_id = hco.country_id
        
        WHERE c.id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $collections = [];
    while ($row = $result->fetch_assoc()) {
        $collections[] = $row;
    }

    echo json_encode($collections);
} else {
    echo json_encode(["error" => "User not logged in"]);
}

$conn->close();

?>
