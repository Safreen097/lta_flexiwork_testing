<?php
    // Include database connection
    include('connection/connection.php');

    // Get staff ID from POST data
    $id = mysqli_real_escape_string($connection, $_POST['id']);
    $staff_id = mysqli_real_escape_string($connection, $_POST['staff_id']);
    $user = mysqli_real_escape_string($connection, $_POST['user']);
    $pass = mysqli_real_escape_string($connection, $_POST['pass']);


    $sql = "UPDATE admin SET staff_id='$staff_id', user='$user', pass='$pass' WHERE admin_id='$id'";
    
    if (mysqli_query($connection,$sql))  {
        echo "<script>alert('Staff details updated!');</script>";
        echo "<script>window.history.back();</script>";
    }
    // Close the database connection
    mysqli_close($connection);

    exit();
 
?>
