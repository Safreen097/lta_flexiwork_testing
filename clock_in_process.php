<?php
// Include database connection
include('connection/connection.php');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Set the timezone to Kuala Lumpur
date_default_timezone_set('Asia/Kuala_Lumpur');

// Read input from JavaScript (POST request)
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['staff_id'], $input['clock_in'], $input['location_status'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
    exit;
}

$staff_id = $input['staff_id'];
$clock_in = date('Y-m-d H:i:s', strtotime($input['clock_in']));
$date = date('Y-m-d', strtotime($clock_in));
$location_status = $input['location_status'];

// Check if attendance record exists for the staff on the given date
$checkQuery = "SELECT * FROM attendance WHERE staff_id = '$staff_id' AND date = '$date'";
$checkResult = mysqli_query($connection, $checkQuery);

if (!$checkResult) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($connection)]);
    exit;
}

if (mysqli_num_rows($checkResult) > 0) {
    // Fetch existing record
    $existingData = mysqli_fetch_assoc($checkResult);

    // Determine which column to update based on current state
    if (empty($existingData['clock_in'])) {
        // First clock-in
        $updateQuery = "UPDATE attendance SET clock_in = '$clock_in', remarks = '$location_status' WHERE staff_id = '$staff_id' AND date = '$date'";
    } elseif (empty($existingData['break1_in']) && empty($existingData['break1_out']) && empty($existingData['clock_out'])) {
        // Second clock-in - Handle it as the end time if the user only clocks in twice
        $updateQuery = "UPDATE attendance SET clock_out = '$clock_in', remarks = '$location_status', break1_in = NULL, break1_out = NULL, status = 'complete' WHERE staff_id = '$staff_id' AND date = '$date'";
    } elseif (empty($existingData['break1_in'])) {
        // Break1 in
        $updateQuery = "UPDATE attendance SET break1_in = '$clock_in' WHERE staff_id = '$staff_id' AND date = '$date'";
    } elseif (empty($existingData['break1_out'])) {
        // Break1 out
        $updateQuery = "UPDATE attendance SET break1_out = '$clock_in' WHERE staff_id = '$staff_id' AND date = '$date'";
    } elseif (empty($existingData['clock_out'])) {
        // Final clock-out
        $updateQuery = "UPDATE attendance SET clock_out = '$clock_in', status = 'complete' WHERE staff_id = '$staff_id' AND date = '$date'";
    } else {
        echo json_encode(['success' => false, 'message' => 'All clock-in slots are already filled.']);
        exit;
    }

    // Execute the update query
    if (!mysqli_query($connection, $updateQuery)) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($connection)]);
    } else {
        echo json_encode(['success' => true, 'message' => 'Clock-in updated successfully.']);
    }
} else {
    // No record exists, insert a new one
    $insertQuery = "INSERT INTO attendance (staff_id, date, clock_in, remarks, status)
                    VALUES ('$staff_id', '$date', '$clock_in', '$location_status', 'incomplete')";
    if (!mysqli_query($connection, $insertQuery)) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($connection)]);
    } else {
        echo json_encode(['success' => true, 'message' => 'Clock-in recorded successfully.']);
    }
}
?>
