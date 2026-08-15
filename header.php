<?php
// Centralized Application Bootstrap Initialization
require_once 'dbconnect.php';
require_once 'LandmarkService.php';

// Instantiate the global data access service once per page lifecycle
$landmarkService = new LandmarkService($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sacramento Historical Landmarks Registry</title>
   <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <header>
        <h1>Sacramento Historical Landmarks Registry</h1>
    </header>