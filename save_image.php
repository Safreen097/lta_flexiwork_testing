<?php
// Include database connection
include('connection/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $image = $data['image']; // Base64 encoded image
    $pictureId = $data['pictureId']; // Picture ID
    $userId = $data['userId']; // User ID
    $userName = $data['userName']; // User name
    $userIcPassport = $data['userIcPassport']; // IC/Passport

    // Extract the file extension from the base64 string
    if (strpos($image, 'data:image/png;base64,') === 0) {
        $fileExtension = 'png';
        $image = str_replace('data:image/png;base64,', '', $image);
    } elseif (strpos($image, 'data:image/jpeg;base64,') === 0) {
        $fileExtension = 'jpeg';
        $image = str_replace('data:image/jpeg;base64,', '', $image);
    } elseif (strpos($image, 'data:image/jpg;base64,') === 0) {
        $fileExtension = 'jpg';
        $image = str_replace('data:image/jpg;base64,', '', $image);
    } else {
        // Unsupported format
        echo "Unsupported image format.";
        exit;
    }

    // Decode the base64 image
    $image = base64_decode($image);

    // Generate the file name
    $uploadDir = 'uploads/';
    $fileName = $uploadDir . $userId . '_' . $userName . '_' . $userIcPassport . "_picture{$pictureId}." . $fileExtension;

    // Create the uploads directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Save the file
    if (file_put_contents($fileName, $image)) {
        echo "Image saved as {$fileName}";

        // Determine the column to update based on the pictureId
        $extensionColumn = ($pictureId == 1) ? 'extension_1' : 'extension_2';

        // Update the corresponding extension column in the database
        $updateQuery = "UPDATE admin SET $extensionColumn = ? WHERE staff_id = ?";
        $stmt = $connection->prepare($updateQuery);
        $stmt->bind_param("ss", $fileExtension, $userId);

        if ($stmt->execute()) {
            echo " and database updated successfully.";
        } else {
            echo " but failed to update the database: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Failed to save the image.";
    }

    // Close the database connection
    $connection->close();
}
?>
