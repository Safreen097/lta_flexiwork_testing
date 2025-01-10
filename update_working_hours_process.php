<?php
// Include database connection
include('connection/connection.php');

// Get staff ID from POST data
$staff_id = mysqli_real_escape_string($connection, $_POST['id']);

// Determine which button was clicked
$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action == 'Update') {
    performUpdate($connection, $staff_id);
} else {
    echo "No valid action specified.";
}

// Function to perform UPDATE operations
function performUpdate($connection, $staff_id) {
    // Define day codes and their corresponding database column prefixes
    $dayCodes = [
        'mon' => 'mon_',
        'tue' => 'tue_',
        'wed' => 'wed_',
        'thu' => 'thu_',
        'fri' => 'fri_',
        'sat' => 'sat_',
        'sun' => 'sun_',
    ];

    $success = true; // Track operation success

    foreach ($dayCodes as $dayCode => $prefix) {
        // Sanitize and retrieve POST inputs
        $workType = isset($_POST[$dayCode . '_worktype']) ? mysqli_real_escape_string($connection, $_POST[$dayCode . '_worktype']) : null;
        $startTime = isset($_POST[$dayCode . '_start']) ? mysqli_real_escape_string($connection, $_POST[$dayCode . '_start']) : null;
        $endTime = isset($_POST[$dayCode . '_end']) ? mysqli_real_escape_string($connection, $_POST[$dayCode . '_end']) : null;
        $breakStart = isset($_POST[$dayCode . '_breakstart']) ? mysqli_real_escape_string($connection, $_POST[$dayCode . '_breakstart']) : null;
        $breakEnd = isset($_POST[$dayCode . '_breakend']) ? mysqli_real_escape_string($connection, $_POST[$dayCode . '_breakend']) : null;
        $remark = isset($_POST[$dayCode . '_remark']) ? mysqli_real_escape_string($connection, $_POST[$dayCode . '_remark']) : null;

        // Prepare UPDATE query for the admin table
        $sql = "UPDATE admin SET "
             . "{$prefix}worktype = '$workType', "
             . "{$prefix}start = '$startTime', "
             . "{$prefix}end = '$endTime', "
             . "{$prefix}breakin = '$breakStart', "
             . "{$prefix}breakout = '$breakEnd', "
             . "{$prefix}remarks = '$remark' "
             . "WHERE admin_id = '$staff_id'";

        // Execute the query and track success
        if (!mysqli_query($connection, $sql)) {
            $success = false;
        }
    }

    // Redirect after all operations
    if ($success) {
        echo "<script>alert('Working hours updated successfully!'); window.location.href = 'edit_staff.php?id=$staff_id';</script>";
    } else {
        echo "<script>alert('Error updating working hours!'); window.history.back();</script>";
    }
}

// Close the database connection
mysqli_close($connection);

exit();
?>
