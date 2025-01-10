<?php
    // Include database connection
    include('connection/connection.php');

    // Fetch the submitted data
    $fullname = mysqli_real_escape_string($connection, $_POST['fullName']);
    $icnumber = mysqli_real_escape_string($connection, $_POST['icNumber']);
    $staffid = mysqli_real_escape_string($connection, $_POST['staffid']);
    $username = mysqli_real_escape_string($connection, $_POST['username']);
    $pass = mysqli_real_escape_string($connection, $_POST['pass']);

    // Insert new staff into the `admin` table
    $sql = "
    INSERT INTO admin (
        staff_id, name, user, pass, ic_passport, role,
        mon_worktype, mon_start, mon_end, mon_breakin, mon_breakout, mon_remarks,
        tue_worktype, tue_start, tue_end, tue_breakin, tue_breakout, tue_remarks,
        wed_worktype, wed_start, wed_end, wed_breakin, wed_breakout, wed_remarks,
        thu_worktype, thu_start, thu_end, thu_breakin, thu_breakout, thu_remarks,
        fri_worktype, fri_start, fri_end, fri_breakin, fri_breakout, fri_remarks,
        sat_worktype, sat_start, sat_end, sat_breakin, sat_breakout, sat_remarks,
        sun_worktype, sun_start, sun_end, sun_breakin, sun_breakout, sun_remarks
    ) VALUES (
        '$staffid', '$fullname', '$username', '$pass', '$icnumber', 'staff',";

    $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

    foreach ($daysOfWeek as $day) {
        // Fetch form values for each day
        $worktype = mysqli_real_escape_string($connection, $_POST[strtolower($day) . '_worktype']);
        $start = mysqli_real_escape_string($connection, $_POST[strtolower($day) . '_start']);
        $end = mysqli_real_escape_string($connection, $_POST[strtolower($day) . '_end']);
        $breakin = mysqli_real_escape_string($connection, $_POST[strtolower($day) . '_breakin']);
        $breakout = mysqli_real_escape_string($connection, $_POST[strtolower($day) . '_breakout']);
        $remarks = mysqli_real_escape_string($connection, $_POST[strtolower($day) . '_remarks']);

        // Add to SQL values
        $sql .= "'$worktype', '$start', '$end', '$breakin', '$breakout', '$remarks',";
    }

    // Remove the trailing comma and close the query
    $sql = rtrim($sql, ',') . ')';

    // Execute the query
    if (mysqli_query($connection, $sql)) {
        echo "<script>alert('New staff registered successfully!');</script>";
        echo "<script>window.location.href = 'manage_staff.php';</script>";
    } else {
        echo "Error: " . mysqli_error($connection);
    }
?>
