<?php
// Pull in the secure database connection bridge
require_once 'dbconnect.php';

$csvFile = 'sac_landmarks_raw.csv';

if (!file_exists($csvFile)) {
    die("Error: The source file '$csvFile' could not be found.");
}

// --- NEW RESET STEP ---
// Wipe the tables clean so we start with a fresh slate on every refresh
echo "Clearing out old database records... ";
$pdo->exec("TRUNCATE TABLE staging_historical_landmarks");
$pdo->exec("TRUNCATE TABLE city_landmarks");
echo "Done!<br><br>";
// ----------------------

// 1. EXTRACT: Open the text data stream
if (($handle = fopen($csvFile, 'r')) !== false) {
    
    // Read the first line to bypass the column headers row
    $headers = fgetcsv($handle, 1000, ',');
    
    // Clean the invisible UTF-8 BOM characters from the very first header element
    if ($headers !== false && isset($headers[0])) {
        $headers[0] = preg_replace('/[\x{00EF}\x{00BB}\x{00BF}\x{FEFF}]/u', '', $headers[0]);
    }

    echo "<h2>Starting Programmatic ETL Data Pipeline...</h2>";
    echo "Staging records in memory:<br><br>";

    // Prepare a secure PDO SQL statement to load data into the staging table
    // This automates the extraction dump into staging_historical_landmarks
    $stagingSql = "INSERT INTO staging_historical_landmarks (
        objectid, apn, house, street_name, street_type, assessment, resource_name, ordinance, shape__area, shape__length
    ) VALUES (
        :objectid, :apn, :house, :street_name, :street_type, :assessment, :resource_name, :ordinance, :shape__area, :shape__length
    )";
    
    $stmt = $pdo->prepare($stagingSql);
    
    $importCount = 0;

    // 2. TRANSFORM & LOAD (STAGING): Loop through row-by-row
    while (($row = fgetcsv($handle, 1000, ',')) !== false) {
        // Map the CSV index numbers cleanly to names matching the database columns
        $data = [
            ':objectid'      => $row[0],
            ':apn'           => $row[1],
            ':house'         => $row[2],
            ':street_name'   => $row[3],
            ':street_type'   => $row[4],
            ':assessment'    => $row[5],
            ':resource_name' => $row[6],
            ':ordinance'     => $row[7],
            ':shape__area'   => $row[8],
            ':shape__length' => $row[9]
        ];
        
        // Execute the insert statement for this row
        $stmt->execute($data);
        $importCount++;
        
        // Output a lightweight progress tracker for the first few records
        if ($importCount <= 2) {
            echo "Staged Object ID: " . htmlspecialchars($row[0]) . " - " . htmlspecialchars($row[3]) . " ST<br>";
        }
    }
    
    fclose($handle);
    echo "<br><strong>Successfully extracted and staged $importCount raw records!</strong><br>";

    // 3. TRANSFORM & LOAD (PRODUCTION): Execute your advanced SQL script logic
    echo "Running data normalization and pushing to production tables... ";
    
    $productionSql = "INSERT INTO city_landmarks (
        objectid, apn, resource_name, street_address, ordinance, shape__area, shape__length
    )
    SELECT 
        objectid, 
        CASE WHEN apn IS NULL OR TRIM(apn) = '' THEN 'UNKNOWN' ELSE TRIM(apn) END,
        CASE 
            WHEN resource_name IS NULL OR TRIM(resource_name) = '' 
            THEN CONCAT('Historic Property at ', CONCAT_WS(' ', TRIM(house), TRIM(street_name), TRIM(street_type)))
            ELSE TRIM(resource_name)
        END,
        CONCAT_WS(' ', TRIM(house), TRIM(street_name), TRIM(street_type)),
        ordinance, shape__area, shape__length
    FROM staging_historical_landmarks";

    // Tell PDO to fire the master SQL migration statement
    $pdo->exec($productionSql);
    
    echo "Done!<br><br>";
    echo "<h3>ETL Pipeline Complete! Database is fully normalized and populated.</h3>";

} else {
    echo "Error: Unable to open the data stream.";
}