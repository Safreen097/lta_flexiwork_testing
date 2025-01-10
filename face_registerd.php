<?php
    // Include database connection
    include('connection/connection.php');

    $id = $_SESSION['id']; // Staff ID for filtering

    $sql = "SELECT * FROM admin WHERE admin_id='$id'";
    $result = mysqli_query($connection, $sql);
    $row = mysqli_fetch_assoc($result); 
    $name = $row['name'];

    $name = preg_replace('/[^a-zA-Z0-9_-]/', '', $name); // Removes invalid characters

    $extension = $row['extension'];
    $relativeFilePath = 'uploads/' . $id . '_' . $name . '.'.$extension;

    // Check if the file exists
    $fileExists = file_exists($relativeFilePath);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LTA Attendance System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Roboto', Arial, sans-serif;
            background-color: #f3f4f6; /* Light grey background */
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .content {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .card-title {
            font-weight: bold;
            font-size: 1.2rem;
            color: #374151; /* Neutral text color */
        }

        .btn-primary {
            background-color: #2563eb; /* Accent blue */
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            font-size: 1rem;
            font-weight: bold;
            color: white;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }

        .btn-primary:hover {
            background-color: #1d4ed8; /* Slightly darker blue on hover */
        }

        img {
            width: 100%;
            max-width: 300px;
            height: auto;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 2px solid #e5e7eb; /* Light grey border */
        }

        .frame {
            padding: 10px;
            background-color: #f9fafb; /* Light background for frame */
            border-radius: 10px;
            border: 1px solid #e5e7eb;
        }

        footer {
            margin-top: 20px;
            font-size: 0.9rem;
            color: #9ca3af; /* Grey text for footer */
        }
    </style>
</head>
<body>
    <div class="content">
        <h5 class="card-title">Face Registered</h5>
        <?php if ($fileExists): ?>
            <div class="frame">
                <img src="<?php echo $relativeFilePath; ?>" alt="Registered Face">
            </div>
        <?php else: ?>
            <p class="text-danger">No picture found. Please upload your picture.</p>
            <a href="face_upload.php" class="btn btn-primary">Upload Picture</a>
        <?php endif; ?>
        <footer>
            © 2025 LTA Attendance System
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
