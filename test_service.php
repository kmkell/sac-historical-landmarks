<?php
// 1. Pull in the database connection keys and your new concierge service
require_once 'dbconnect.php';
require_once 'LandmarkService.php';

// 2. Hire the concierge and hand them the database keys ($pdo comes from dbconnect.php)
$landmarkService = new LandmarkService($pdo);

echo "<h2>Testing Data Access Layer Abstraction...</h2>";

// 3. Ask the concierge to perform the 'getById' service for our unique edge-case row
$recordId = 3420;
$landmark = $landmarkService->getById($recordId);

// 4. Inspect what the concierge brought back
if ($landmark) {
    echo "<strong>Success! Record #$recordId retrieved through the service class:</strong><br><br>";
    echo "<pre>";
    print_r($landmark);
    echo "</pre>";
} else {
    echo "Error: Record #$recordId could not be found in the production table.";
}