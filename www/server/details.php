<?php
session_start();
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Connect to database
$host = "sql103.infinityfree.com";
$username = "if0_38074629";
$password = "eTuY3NCjICH";
$database = "if0_38074629_Mobile_finalProject";
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'] ?? '';
$category = $_GET['category'] ?? '';

// Initialize row to store query results
$row = null;

if ($category === 'hotel') {
    $stmt = $conn->prepare("SELECT * FROM hotels WHERE hotel_id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    }
    $stmt->close();
} elseif ($category === 'attraction') {
    $stmt = $conn->prepare("SELECT * FROM attractions WHERE attraction_id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    }
    $stmt->close();
}

$message = ''; // Variable for storing the success or error message

if ($row) {
 if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Assuming user is logged in and user_id is available in session
    $user_id = $_SESSION['user_id'] ?? null;

    if ($user_id) {
        // Check if the item already exists in the collection
        $existing_check_query = "";
        if ($category === 'hotel') {
            $existing_check_query = "SELECT * FROM collection WHERE id = ? AND hotel_id = ?";
        } elseif ($category === 'attraction') {
            $existing_check_query = "SELECT * FROM collection WHERE id = ? AND attraction_id = ?";
        }

        if (!empty($existing_check_query)) {
            $stmt = $conn->prepare($existing_check_query);
            $stmt->bind_param("is", $user_id, $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            if ($result->num_rows > 0) {
                
                $message = "This item is already in your collection.";
            } else {
                // Proceed to insert the new entry into the collection
                if ($category === 'hotel') {
                    $stmt = $conn->prepare("INSERT INTO collection (id, hotel_id) VALUES (?, ?)");
                    $stmt->bind_param("is", $user_id, $id);
                } elseif ($category === 'attraction') {
                    $stmt = $conn->prepare("INSERT INTO collection (id, attraction_id) VALUES (?, ?)");
                    $stmt->bind_param("is", $user_id, $id);
                }

                if ($stmt->execute()) {
                    $message = $category === 'hotel' 
                        ? "Hotel successfully added to your collection!" 
                        : "Attraction successfully added to your collection!";
                } else {
                    $message = "Failed to add the item to your collection.";
                }
                $stmt->close();
            }
        } else {
            $message = "Invalid category specified.";
        }
    } else {
        $message = "Please log in to add this item to your collection.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ($category === 'hotel' ? $row['hotel_name'] : $row['attraction_name']); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/details.css">
</head>
<style>
        /* Styling for the h3 element */
        h3 {
            font-size: 18px; /* Make h3 smaller */
            font-family: 'Arial', sans-serif; /* Attractive font */
            font-weight: bold; /* Bold text */
            color: #333; /* Dark color for the title */
        }

        p{
            font-size: 12px;
        }
        /* Making images larger within carousel */
        .carousel-item img {
            max-height: 500px; /* Adjust max-height to make images bigger */
            width: 100%; /* Ensure it fills the container horizontally */
            object-fit: contain; /* Ensure the image covers the container without distortion */
        }

        /* Optional: Customize the alert style */
        .alert-info {
            background-color: #f0f9ff;
            border-color: #a6e1ff;
        }

        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        .carousel-control-prev-icon, 
        .carousel-control-next-icon {
            filter: invert(100%);
        }
    </style>
<body>
      <!-- Back Button -->
        <button id="backButton" class="btn btn-secondary mb-4">Back</button>
    

     <div class="container">
        <!-- Success or Error Message Display -->
        <?php if ($message): ?>
            <div class="alert <?php echo ($message === 'This item is already in your collection.') ? 'alert-danger' : 'alert-info'; ?> mt-5">
                <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <h1><?php echo ($category === 'hotel' ? $row['hotel_name'] : $row['attraction_name']); ?></h1>
        <div class="row details-container">
            <div class="col-md-6">
                <!-- Carousel or image display -->
                <?php
                    $raw_images = $category === 'hotel' ? $row['image'] : $row['imageD'];
                    $images = array_map('trim', preg_split('/[\s,]+/', $raw_images));
                    $images = array_map(function ($image) {
                        return 'img/' . $image;
                    }, $images);
                ?>
                <?php if (!empty($images)): ?>
                    <div id="imageCarousel" class="carousel slide" data-ride="carousel">
                        <ol class="carousel-indicators">
                            <?php foreach ($images as $index => $image): ?>
                                <li data-target="#imageCarousel" data-slide-to="<?php echo $index; ?>" class="<?php echo $index === 0 ? 'active' : ''; ?>"></li>
                            <?php endforeach; ?>
                        </ol>
                        <div class="carousel-inner">
                            <?php foreach ($images as $index => $image): ?>
                                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                    <img src="<?php echo $image; ?>" class="d-block w-100" alt="Image <?php echo $index + 1; ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <a class="carousel-control-prev" href="#imageCarousel" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#imageCarousel" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                <?php else: ?>
                    <p>No images available.</p>
                <?php endif; ?>
            </div>

            <div class="col-md-6">
                <div class="card card-custom">
                    <?php if ($category === 'hotel'): ?>
                        <h3>Description</h3>
                        <p class="desc"><?php echo $row['description'] ?? 'No description available'; ?></p>
                        <h3>Address</h3>
                        <p><?php echo $row['address'] ?? 'No address available'; ?></p>
                        <h3>Star Rating</h3>
                        <p><?php echo $row['star_rating'] ?? 'No rating available'; ?></p>
                        <h3>Price Range</h3>
                        <p><?php echo $row['price_range'] ?? 'No price range available'; ?></p>
                    <?php else: ?>
                        <h3>Description</h3>
                        <p><?php echo $row['description'] ?? 'No description available'; ?></p>
                        <h3>Location</h3>
                        <p><?php echo $row['location'] ?? 'No location available'; ?></p>
                        <h3>Opening Hours</h3>
                        <p><?php echo $row['opening_hours'] ?? 'No opening hours available'; ?></p>
                        <h3>Entrance Fee</h3>
                        <p><?php echo $row['entrance_fee'] ?? 'No entrance fee information available'; ?></p>
                    <?php endif; ?>

                    <h3>Website</h3>
                    <p>
                        <?php if (!empty($row['website'])): ?>
                            <a href="<?php echo $row['website']; ?>" target="_blank">Click here to visit official website</a>
                        <?php else: ?>
                            No website available.
                        <?php endif; ?>
                    </p>
                </div>

                <!-- Add to Collection Form -->
                <?php if ($id): ?>
                    <form method="POST" action="">
                        <button type="submit" name="add_to_collection" class="btn btn-primary">Add to Collection</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

  
    <script>

        
        document.getElementById('backButton').addEventListener('click', () => {
    window.history.back(); // Navigate to the previous page
});

    </script>
 <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script></body>
</html>

<?php
} else {
    echo "<p>No details found for the requested item.</p>";
}

$conn->close();
?>