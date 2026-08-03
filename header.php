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
    <style>
        /* Base typography and clean UI styles */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
        }
        header {
            background-color: #2c3e50;
            color: #fff;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        header h1 { margin: 0; font-size: 1.8rem; }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-radius: 5px;
            overflow: hidden;
        }
        .data-table th, .data-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .data-table th {
            background-color: #34495e;
            color: #fff;
            font-weight: bold;
        }
        .data-table tr:hover { background-color: #f5f5f5; }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 0.8rem;
            font-weight: bold;
            border-radius: 3px;
            background-color: #e74c3c;
            color: #fff;
        }
    </style>
</head>
<body>
    <header>
        <h1>Sacramento Historical Landmarks Registry</h1>
    </header>