<?php
    include('connection/connection.php');
    
    $act = $_REQUEST['act'];

    if ($act == "login") {
        $username = $_REQUEST['username'];
        $password = $_REQUEST['password'];

        // To prevent from SQL injection
        $username = stripslashes($username);
        $password = stripslashes($password);
        $username = mysqli_real_escape_string($connection, $username);
        $password = mysqli_real_escape_string($connection, $password);

        // Query to check the username and password
        $sql = "SELECT * FROM admin WHERE user='$username' AND pass='$password'";
        $query = mysqli_query($connection, $sql);

        if (mysqli_num_rows($query) != 0) {
            $row = mysqli_fetch_assoc($query);

            // Storing session data
            $_SESSION['login_user'] = $username;
            $_SESSION['id'] = $row['staff_id'];

            // Check the user role and navigate to the appropriate page
            if ($row['role'] == 'admin') {
                // If the user is an admin, redirect to the admin interface
                header('Location: superadmin.php');
                exit;
            } elseif ($row['role'] == 'staff') {
                // If the user is a tech, redirect to the main page
                header('Location: frame.php');
                exit;
            } else {
                echo "<script>alert('Role not recognized. Please contact the administrator.'); window.history.back(-1);</script>";
            }
        } else {
            // Invalid credentials
            echo "<script>alert('Username and Password invalid'); window.history.back(-1);</script>";
        }
    }
?>
