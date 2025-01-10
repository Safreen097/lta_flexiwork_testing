<?php
    // Include database connection
    include('connection/connection.php');

    $id = $_SESSION['id']; // Staff ID for filtering

    $sql = "SELECT * FROM admin WHERE staff_id='$id'";
    $result = mysqli_query($connection, $sql);
    $row = mysqli_fetch_assoc($result); 
    $name = $row['name'];
    $icpass = $row['ic_passport'];
    $extension = $row['extension'];
    $uploadDir = 'uploads/';
    $fileName1 = $uploadDir . $id . '_' . $name . '_' . $icpass. $extension;
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
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh; /* Ensure full height */
        }

        .content {
            padding: 20px;
            width: 100%;
            max-width: 600px; /* Optional, limits width for better display */
        }

        .frame {
            width: 300px; /* Adjust frame width */
            height: 300px; /* Adjust frame height */
            border: 2px dashed #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f8f8;
            margin: 10px auto; /* Center the frame horizontally */
            border-radius: 10px;
            position: relative;
        }

        .frame img {
            max-width: 100%;
            max-height: 100%;
            border-radius: 10px;
        }

        .frame:hover {
            background-color: #e9ecef;
        }

    </style>
</head>
<body>
    <div class="content">
        <div class="col-12">
            <div class="card shadow-sm mb-3">
                <div class="card-body d-flex align-items-center justify-content-center text-center">
                    <i class="bi bi-plus-square text-primary fs-4 me-2"></i>
                    <h5 class="card-title m-0">Face Upload</h5>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card shadow-sm mb-3">
                <form id="uploadpic" action="upload_pic_process.php" method="post" enctype="multipart/form-data">
                    <div class="form-group row">
                        <div class="col-12">
                            <input type="text" style="font-weight:bold;text-align:center;" class="form-control" value="SELFIE PICTURE" disabled>
                            <input type="hidden" name="staff_id" value="<?php echo $id; ?>">
                            <input type="hidden" name="name" value="<?php echo $name; ?>">
                            <input type="hidden" name="icpass" value="<?php echo $icpass; ?>">
                        </div>
                        <div class="col-12">
                            <input type="file" class="form-control" name="file" id="file" accept="image/*" onchange="previewImage(event)">
                            <div class="frame" id="imagePreview" style="display: none;">
                                <img id="img_preview" src="#" alt="Image Preview">
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col text-center">
                            <button type="submit" class="btn btn-primary" name="action" value="SUBMIT"><i class="bi bi-upload"></i> UPLOAD</button>
                        </div>
                    </div>
                    <br>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewImage(event) {
            const fileInput = event.target;
            const previewContainer = document.getElementById('imagePreview');
            const previewImage = document.getElementById('img_preview');

            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImage.src = e.target.result;
                    previewContainer.style.display = 'block';
                };
                reader.readAsDataURL(fileInput.files[0]);
            } else {
                previewContainer.style.display = 'none';
                previewImage.src = '#';
            }
        }
    </script>
</body>
</html>
