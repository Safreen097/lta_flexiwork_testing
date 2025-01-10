<?php
// Include database connection
include('connection/connection.php');
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get staff ID from session
$id = $_SESSION['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Staff ID not found in session.']);
    exit;
}

// Get the start and end dates of the current week
$today = new DateTime();
$startOfWeek = clone $today->modify('monday this week');
$endOfWeek = clone $today->modify('sunday this week');

$startDate = $startOfWeek->format('Y-m-d');
$endDate = $endOfWeek->format('Y-m-d');

// Fetch attendance records for the week
$query = "SELECT * FROM attendance WHERE staff_id = '$id' AND date BETWEEN '$startDate' AND '$endDate'";
$result = mysqli_query($connection, $query);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($connection)]);
    exit;
}

// Convert result to an associative array
$attendanceData = [];
while ($row = mysqli_fetch_assoc($result)) {
    $attendanceData[$row['date']] = $row;
}

echo json_encode(['success' => true, 'data' => $attendanceData]);
?>
