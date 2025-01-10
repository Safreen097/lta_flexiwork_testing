<?php
    include('connection/connection.php');

    // Destroy session and unset session variables
    session_destroy();
    unset($_SESSION['login_user']);
    unset($_SESSION['id']);

    // Redirect to index.php after logout
    header('Location: index.html');
    exit; // Ensure script execution stops after redirection
?>