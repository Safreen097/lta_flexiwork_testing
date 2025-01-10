<?php
    // Include database connection
    include('connection/connection.php');

    $id = $_SESSION['id']; // Staff ID for filtering

    $sql = "SELECT * FROM past_staff";
    $result = mysqli_query($connection, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Manage Staff</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fc;
            margin: 0;
        }

        /* Sidebar Styles */
        .sidebar {
            background-color: #4e73df;
            color: #fff;
            min-height: 100vh;
            padding: 15px;
            display: flex;
            flex-direction: column;
        }

        .sidebar a {
            color: #fff;
            text-decoration: none;
            display: block;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
        }

        .sidebar a:hover {
            background-color: #2e59d9;
        }

        /* Main Content Styles */
        .content {
            flex-grow: 1;
            padding: 20px;
            background-color: #f8f9fc;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #4e73df;
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 1.5rem;
        }

        .btn-primary {
            background: linear-gradient(90deg, #4e73df, #2e59d9);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(90deg, #2e59d9, #4e73df);
        }

        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 5px rgba(78, 115, 223, 0.5);
        }

        .form-label {
            font-weight: bold;
        }

        .table th {
            background-color: #4e73df;
            color: white;
            text-align: center;
        }

        .table td {
            text-align: center;
            vertical-align: middle;
        }

        .pagination {
            justify-content: center;
        }

        footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="main-container d-flex">
        <!-- Sidebar -->
        <div class="sidebar">
            <h5 class="text-center fw-bold mb-3">LTA SERVICES SDN BHD</h5>
            <a href="superadmin.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
            <hr>
            <h6 class="fw-bold">MENU</h6>
            <a href="register_staff.php"><i class="bi bi-person-add me-2"></i> Register</a>
            
            <!-- Remove Staff with Submenu -->
            <a href="#removeStaffSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                <i class="bi bi-person-dash me-2"></i> Remove Staff
            </a>
            <ul class="collapse list-unstyled ms-4" id="removeStaffSubmenu">
                <li>
                    <a href="delete_staff.php"><i class="bi bi-person-x me-2"></i>Remove Staff</a>
                </li>
                <li>
                    <a href="past_staff.php"><i class="bi bi-archive me-2"></i>Past Staff</a>
                </li>
            </ul>
            
            <a href="manage_staff.php"><i class="bi bi-person-gear me-2"></i> Manage staff details</a>
            <a href="view_working_hours.php"><i class="bi bi-person-gear me-2"></i> View all working hours</a>
            <a href="attendance_record.php"><i class="bi bi-file-earmark me-2"></i> Attendance Record</a>
            <hr>

            <!-- Face Registered with Submenu -->
            <a href="#faceRegisteredSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                <i class="bi bi-check2-square me-2"></i> Face Registered
            </a>
            <ul class="collapse list-unstyled ms-4" id="faceRegisteredSubmenu">
                <li>
                    <a href="face_upload.php?id=<?php echo $id;?>"><i class="bi bi-plus-square me-2"></i>Register Face</a>
                </li>
                <li>
                    <a href="face_registerd.php?id=<?php echo $id;?>"><i class="bi bi-check-circle me-2"></i>View Registered Faces</a>
                </li>
            </ul>

            <a href="check_in.php?id=<?php echo $id;?>"><i class="bi bi-clock-fill me-2"></i> Clock In</a>
            <a href="logout.php" onClick="return confirm('Are you sure you want to log out?');"><i class="bi bi-box-arrow-left me-2"></i> Logout</a>
        </div>


        <!-- Main Content -->
        <div class="content">
            <!-- Header -->
            <div class="header">
                <h1>Past Staff</h1>
                <p class="mb-0">Staff that resigned before.</p>
            </div>

            <!-- Staff Details Table -->
            <div class="card p-4">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Staff Name</th>
                                <th>IC / PASSPORT NUMBER</th>
                                <th>RESIGNATION DATE</th>
                                <th>DATE TIME WHEN STAFF BEEN REMOVE FROM SYSTEM</th>
                                <th>STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($staffDetails = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <!-- Convert name to uppercase -->
                                    <td><?= strtoupper(htmlspecialchars($staffDetails['name'])) ?></td>
                                    
                                    <!-- Display IC / Passport -->
                                    <td><?= htmlspecialchars($staffDetails['ic_passport']) ?></td>
                                    
                                    <!-- Format resign_date -->
                                    <td>
                                        <?php 
                                        $resignDate = date('d-m-Y', strtotime($staffDetails['resign_date']));
                                        echo htmlspecialchars($resignDate); 
                                        ?>
                                    </td>
                                    
                                    <!-- Format timestamp_action -->
                                    <td>
                                        <?php 
                                        $timestampAction = date('d-m-Y H:i:s', strtotime($staffDetails['timestamp_action']));
                                        echo htmlspecialchars($timestampAction); 
                                        ?>
                                    </td>
                                    
                                    <!-- Display status -->
                                    <td>
                                        <span class="btn btn-danger btn-sm"><?= htmlspecialchars($staffDetails['staff_status']) ?></span>
                                    </td> 
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

            <footer>
                &copy; <?= date('Y'); ?> LTA Services Sdn Bhd. All rights reserved.
            </footer>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

