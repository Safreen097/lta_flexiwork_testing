<?php
// Include database connection
include('connection/connection.php');

// Initialize variables
$query = $_GET['query'] ?? '';
$filterStaffId = $_GET['filter_staff'] ?? '';
$staffDetails = null;

// Fetch all staff names for the dropdown
$dropdownQuery = "SELECT admin_id, UPPER(name) AS name FROM admin ORDER BY name ASC";
$dropdownResult = mysqli_query($connection, $dropdownQuery);

// Check if a staff member is selected from the dropdown
if (!empty($filterStaffId)) {
    $safeStaffId = mysqli_real_escape_string($connection, $filterStaffId);
    $sql = "SELECT * FROM admin WHERE admin_id = '$safeStaffId'";
    $result = mysqli_query($connection, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $staffDetails = mysqli_fetch_assoc($result);
    }
}

// Check if query is not empty for search input
if (!empty($query)) {
    $safeQuery = mysqli_real_escape_string($connection, $query);
    $sql = "SELECT * FROM admin WHERE admin_id = '$safeQuery' OR name LIKE '%$safeQuery%'";
    $result = mysqli_query($connection, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $staffDetails = mysqli_fetch_assoc($result);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Delete Staff</title>
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

        footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
            color: #6c757d;
        }

        .alert {
            margin-top: 20px;
        }

        .list-group-item {
            background-color: #f8f9fc;
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
                <h1>Delete Staff</h1>
                <p class="mb-0">Search for a staff member to remove from the system.</p>
            </div>

            <div class="card p-4 mb-4">
                <form method="get" class="row g-3 align-items-center">
                    <!-- Dropdown to Select Staff -->
                    <div class="col-md-6">
                        <label for="filter_staff" class="form-label">Select Staff to Delete</label>
                        <select class="form-control" name="filter_staff" id="filter_staff" onchange="this.form.submit()">
                            <option value="">Select Staff</option>
                            <?php while ($dropdownRow = mysqli_fetch_assoc($dropdownResult)): ?>
                                <option value="<?= $dropdownRow['admin_id'] ?>" <?= $filterStaffId == $dropdownRow['admin_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($dropdownRow['name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Search by Name -->
                    <div class="col-md-6">
                        <label for="query" class="form-label">Search by Staff Name</label>
                        <input type="text" class="form-control" id="query" name="query" placeholder="Enter staff ID or name" value="<?= htmlspecialchars($query) ?>">
                    </div>

                    <!-- Submit Button -->
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-2"></i>Search
                        </button>
                    </div>
                </form>
            </div>

            <!-- Staff Details -->
            <?php if ($staffDetails): ?>
                <div class="card mb-4 p-3">
                    <h5 class="fw-bold text-success">Staff Details</h5>
                    <ul class="list-group">
                        <li class="list-group-item"><strong>ID:</strong> <?= htmlspecialchars($staffDetails['staff_id']) ?></li>
                        <li class="list-group-item"><strong>Name:</strong> <?= htmlspecialchars($staffDetails['name']) ?></li>
                        <li class="list-group-item"><strong>IC / Passport:</strong> <?= htmlspecialchars($staffDetails['ic_passport']) ?></li>
                    </ul>
                </div>

                <!-- Delete Confirmation -->
                <div class="card p-4">
                    <h5 class="fw-bold text-danger">Remove Staff</h5>
                    <form action="process_delete_staff.php" method="post">
                        <input type="hidden" name="staff_id" value="<?= htmlspecialchars($staffDetails['admin_id']) ?>">
                        
                        <!-- Resignation Date Input -->
                        <div class="mb-3">
                            <label for="resignation_date" class="form-label">Resignation Date</label>
                            <input type="date" class="form-control" id="resignation_date" name="resignation_date" required>
                        </div>
                        
                        <p>Are you sure you want to remove this staff member?</p>
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-trash me-2"></i>Remove
                        </button>
                    </form>
                </div>

            <?php elseif (!empty($query) || !empty($filterStaffId)): ?>
                <div class="alert alert-danger">No staff found matching your query. Please try again.</div>
            <?php endif; ?>

            <footer>
                &copy; <?= date('Y'); ?> LTA Services Sdn Bhd. All rights reserved.
            </footer>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
