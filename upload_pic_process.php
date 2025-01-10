<?php
    // Include database connection
    include('connection/connection.php');

    $staff_id = $_POST['staff_id'];
    $name = $_POST['name'];
    $icpass = $_POST['icpass'];

    $name = preg_replace('/[^a-zA-Z0-9_-]/', '', $name); // Removes invalid characters

    $uploadDir = 'uploads/';
    if (!empty($_FILES['file']['name'])) {
        $fileExtension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $newname = $staff_id.'_'.$name.'.'.$fileExtension;
        $filePath = $uploadDir . $newname;
        $fileUploaded = move_uploaded_file($_FILES['file']['tmp_name'], $filePath);
    } else {
        $fileExtension = ''; // Set a default value for the extension if no image is uploaded
    }

    $sql = "UPDATE admin SET extension = '$fileExtension' WHERE admin_id='$staff_id'";
    $result = mysqli_query($connection,$sql);

    // Show the alert only once if any of the insertions were successful
    if ($result)  {
        echo "<script>alert('Picture uploaded successfully!');</script>";
        echo "<script>window.history.back();</script>";;
    }
?>
