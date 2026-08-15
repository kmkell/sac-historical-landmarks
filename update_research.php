<?php
require_once 'header.php';

// Ensure the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = isset($_POST['landmark_id']) ? (int)$_POST['landmark_id'] : 0;
$year_built = trim($_POST['year_built'] ?? '');
$architect = trim($_POST['architect'] ?? '');
$notes = trim($_POST['notes'] ?? '');
$selected_styles = $_POST['styles'] ?? []; // Array of style IDs checked in the form

if ($id <= 0) {
    die("Invalid landmark ID.");
}

try {
    // 1. Save/Update research notes, year built, and architect
    $landmarkService->saveResearch($id, $year_built, $architect, $notes);
    
    // 2. Update architectural styles mapping
    $landmarkService->updateLandmarkStyles($id, $selected_styles);
    
    // Redirect back to the landmark's detail page
    header("Location: detail.php?id=" . $id);
    exit;
} catch (Exception $e) {
    die("Error saving research: " . htmlspecialchars($e->getMessage()));
}