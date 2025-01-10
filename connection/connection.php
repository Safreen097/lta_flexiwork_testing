<?php
    $servername = "DB_HOST";
    $username = "DB_USER";
    $password = "DB_PASSWORD";
    $db = "DB_NAME";

    // Establish connection
    $connection = mysqli_connect($servername, $username, $password, $db);

    // Check connection
    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Start session
    session_start();
?>
