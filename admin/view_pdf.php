<?php
// view_pdf.php

include('include/config.php'); // Database connection

// Folder where PDFs are stored
$upload_dir_name = 'uploads_pdf';
$upload_path = __DIR__ . '/' . $upload_dir_name . '/'; // Absolute path

// Enable error reporting (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get the result ID from URL
$result_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($result_id <= 0) {
    die("Invalid result ID.");
}

// Fetch the file path and exam title from the database
$sql = "SELECT file_path, exam_title FROM results WHERE result_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database error: " . htmlspecialchars($conn->error));
}

$stmt->bind_param("i", $result_id);
$stmt->execute();
$stmt->bind_result($db_file_path, $exam_title);

if (!$stmt->fetch()) {
    $stmt->close();
    die("No PDF found for the given ID.");
}

$stmt->close();
$conn->close();

// Full path to the PDF file
$file_full_path = realpath($upload_path . basename($db_file_path)); // Prevent directory traversal

// Validate file existence
if (!$file_full_path || !file_exists($file_full_path) || !is_readable($file_full_path)) {
    die("PDF file not found or unreadable: " . htmlspecialchars($db_file_path));
}

// Send headers to display PDF in browser
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . basename($db_file_path) . '"');
header('Content-Length: ' . filesize($file_full_path));
header('Accept-Ranges: bytes');

// Output the file
readfile($file_full_path);
exit();
?>
