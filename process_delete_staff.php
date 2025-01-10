<?php
    include('connection/connection.php');

    // Set the timezone to Kuala Lumpur
    date_default_timezone_set('Asia/Kuala_Lumpur');

    // Get the staff ID from POST request
    $id = $_POST['staff_id'];
    $resignation_date = $_POST['resignation_date'];

    // Fetch the staff details from the `admin` table
    $fetch_data = "SELECT admin_id, staff_id, name, ic_passport FROM admin WHERE admin_id='$id'";
    $result_data = mysqli_query($connection, $fetch_data);
    $row_data = mysqli_fetch_assoc($result_data);

    $admin_id = $row_data['admin_id'];
    $staff_id = $row_data['staff_id'];
    $name = $row_data['name']; // Fixed typo ($row_date -> $row_data)
    $ic_passport = $row_data['ic_passport'];

    // Get the current timestamp
    $timestamp_action = date('Y-m-d H:i:s'); // Format: YYYY-MM-DD HH:MM:SS

    // Insert the unactive staff into the `past_staff` table
    $unactive_staff = "INSERT INTO past_staff (admin_id, staff_id, name, ic_passport, resign_date, timestamp_action) 
                    VALUES ('$admin_id', '$staff_id', '$name', '$ic_passport', '$resignation_date', '$timestamp_action')";

    mysqli_query($connection, $unactive_staff);

    // Start a transaction
    mysqli_begin_transaction($connection);

    try {
        // Prepare and execute the main delete queries
        $deleteAdmin = $connection->prepare("DELETE FROM admin WHERE admin_id = ?");
        $deleteAdmin->bind_param("s", $id);
        $deleteAdmin->execute();

        $deleteAttendance = $connection->prepare("DELETE FROM attendance WHERE staff_id = ?");
        $deleteAttendance->bind_param("s", $id);
        $deleteAttendance->execute();

        // Commit the transaction if all queries succeed
        mysqli_commit($connection);

        // Success message
        echo "<script>alert('Staff and related records deleted successfully!');</script>";
        echo "<script>window.history.back();</script>";
    } catch (Exception $e) {
        // Rollback the transaction if any query fails
        mysqli_rollback($connection);

        // Error message
        echo "<script>alert('Failed to delete records: " . $e->getMessage() . "');</script>";
        echo "<script>window.history.back();</script>";
    }

    // Close the connection
    $connection->close();
?>
