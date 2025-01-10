<?php
// Include database connection
include('connection/connection.php');

// Initialize query variables
$query = $_GET['query'] ?? ''; // Search query for staff name or ID
$month = $_GET['month'] ?? ''; // Month filter
$year = $_GET['year'] ?? '';   // Year filter
$filterStaffId = $_GET['filter_staff'] ?? ''; // Dropdown filter for specific staff

// Construct SQL query with INNER JOIN (without working_hours_$day tables)
$sql = "SELECT 
            attendance.staff_id, 
            admin.name AS staff_name, 
            MONTH(attendance.date) AS month, 
            YEAR(attendance.date) AS year, 
            MIN(attendance.date) AS representative_date
        FROM attendance
        INNER JOIN admin ON attendance.staff_id = admin.admin_id
        WHERE 1";

// Apply filters if set
if (!empty($month)) {
    $sql .= " AND MONTH(attendance.date) = '" . mysqli_real_escape_string($connection, $month) . "'";
}
if (!empty($year)) {
    $sql .= " AND YEAR(attendance.date) = '" . mysqli_real_escape_string($connection, $year) . "'";
}
if (!empty($query)) {
    $sql .= " AND (attendance.staff_id LIKE '%" . mysqli_real_escape_string($connection, $query) . "%' 
                OR admin.name LIKE '%" . mysqli_real_escape_string($connection, $query) . "%')";
}
if (!empty($filterStaffId)) {
    $sql .= " AND attendance.staff_id = '" . mysqli_real_escape_string($connection, $filterStaffId) . "'";
}

// Group by staff ID and month
$sql .= " GROUP BY attendance.staff_id, MONTH(attendance.date), YEAR(attendance.date)";
$sql .= " ORDER BY representative_date DESC";

// Execute query
$result = mysqli_query($connection, $sql);

// Fetch all staff names for dropdown
$dropdownQuery = "SELECT admin_id AS staff_id, UPPER(name) AS name FROM admin ORDER BY name ASC";
$dropdownResult = mysqli_query($connection, $dropdownQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Record</title>
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

        .sidebar {
            background-color: #4e73df;
            color: #fff;
            min-height: 100vh;
            padding: 15px;
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

        .content {
            flex-grow: 1;
            padding: 20px;
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
                <h1>Attendance Record</h1>
                <p class="mb-0">Filter and view attendance records.</p>
            </div>

            <!-- Filter Form -->
            <div class="card p-4 mb-4">
                <form method="get">
                    <div class="row g-3">
                        <!-- Month Filter -->
                        <div class="col-md-6">
                            <label for="month" class="form-label">Month</label>
                            <select id="month" name="month" class="form-select">
                                <option value="">Select Month</option>
                                <?php for ($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?= str_pad($i, 2, '0', STR_PAD_LEFT); ?>"
                                        <?= ($month == str_pad($i, 2, '0', STR_PAD_LEFT)) ? 'selected' : ''; ?>>
                                        <?= date('F', mktime(0, 0, 0, $i, 1)); ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <!-- Year Filter -->
                        <div class="col-md-6">
                            <label for="year" class="form-label">Year</label>
                            <select id="year" name="year" class="form-select">
                                <option value="">Select Year</option>
                                <?php for ($i = 2024; $i <= 2035; $i++): ?>
                                    <option value="<?= $i; ?>" <?= ($year == $i) ? 'selected' : ''; ?>>
                                        <?= $i; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <!-- Search by Name -->
                        <div class="col-md-6">
                            <label for="query" class="form-label">Search by Staff Name</label>
                            <input type="text" id="query" class="form-control" name="query" placeholder="Enter Staff Name or ID" value="<?= htmlspecialchars($query); ?>">
                        </div>

                        <!-- Dropdown to Select Staff -->
                        <div class="col-md-6">
                            <label for="filter_staff" class="form-label">Select Staff</label>
                            <select class="form-select" id="filter_staff" name="filter_staff" onchange="this.form.submit()">
                                <option value="">Select Staff</option>
                                <?php while ($dropdownRow = mysqli_fetch_assoc($dropdownResult)): ?>
                                    <option value="<?= htmlspecialchars($dropdownRow['staff_id']); ?>"
                                        <?= ($filterStaffId == $dropdownRow['staff_id']) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($dropdownRow['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-md-12 text-end mt-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-filter me-2"></i>Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Attendance Records Table -->
            <div class="card p-4">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th>Staff ID</th>
                                <th>Staff Name</th>
                                <th style="text-align: center;">Month</th>
                                <th style="text-align: center;">Year</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['staff_id']); ?></td>
                                        <td><?= htmlspecialchars($row['staff_name']); ?></td>
                                        <td style="text-align: center;"><?= htmlspecialchars(date('F', mktime(0, 0, 0, $row['month'], 1))); ?></td>
                                        <td style="text-align: center;"><?= htmlspecialchars($row['year']); ?></td>
                                        <td style="text-align: center;">
                                            <a href="attendance_details.php?id=<?= htmlspecialchars($row['staff_id']) ?>&date=<?= date('m', mktime(0, 0, 0, $row['month'], 1)); ?>" class="btn btn-info btn-sm">
                                                <i class="bi bi-file-earmark me-2"></i>Details
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No records found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
