<?php
if (!isset($_GET['lat']) || !isset($_GET['lng']) || !isset($_GET['radius'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

$latitude = $_GET['lat'];
$longitude = $_GET['lng'];
$radius = $_GET['radius'];

// Construct the Overpass API query URL
$url = "https://overpass-api.de/api/interpreter?data=[out:json];node[\"amenity\"=\"police\"](around:$radius,$latitude,$longitude);out;";

// Send HTTP GET request to the Overpass API
$response = file_get_contents($url);

// Check if the response is not false
if ($response === FALSE) {
    http_response_code(500);
    echo json_encode(['error' => 'Error occurred while fetching data from Overpass API']);
    exit;
}

// Decode the JSON response
$data = json_decode($response, true);

// Check if JSON decoding succeeded
if ($data === NULL) {
    http_response_code(500);
    echo json_encode(['error' => 'Error occurred while decoding JSON response']);
    exit;
}

// Output the result as JSON
header('Content-Type: application/json');
echo json_encode($data);
?>
