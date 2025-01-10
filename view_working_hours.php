<?php
    // Include database connection
    include('connection/connection.php');

    $id = $_SESSION['id']; // Staff ID for filtering

    // Fetch all staff names for the dropdown
    $dropdownQuery = "SELECT staff_id, UPPER(name) AS name FROM admin ORDER BY name ASC";
    $dropdownResult = mysqli_query($connection, $dropdownQuery);

    // Initialize variables
    $searchQuery = $_GET['search'] ?? ''; // Search query
    $filterStaffId = $_GET['filter_staff'] ?? ''; // Dropdown filter
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
    $limit = 10; // Records per page
    $offset = ($page - 1) * $limit; // Offset for the current page

    // Build the SQL query
    $sql = "SELECT *, UPPER(name) AS name_upper FROM admin";
    $countSql = "SELECT COUNT(*) as total FROM admin";

    // Apply search and filter conditions
    $whereClauses = [];
    if (!empty($searchQuery)) {
        $safeQuery = mysqli_real_escape_string($connection, $searchQuery);
        $whereClauses[] = "name LIKE '%$safeQuery%'";
    }
    if (!empty($filterStaffId)) {
        $safeStaffId = mysqli_real_escape_string($connection, $filterStaffId);
        $whereClauses[] = "staff_id = '$safeStaffId'";
    }

    // Combine all conditions with AND
    if (!empty($whereClauses)) {
        $sql .= " WHERE " . implode(" AND ", $whereClauses);
        $countSql .= " WHERE " . implode(" AND ", $whereClauses);
    }

    // Add ORDER BY and LIMIT for pagination
    $sql .= " ORDER BY name_upper ASC LIMIT $limit OFFSET $offset";

    // Execute queries
    $result = mysqli_query($connection, $sql);
    $countResult = mysqli_query($connection, $countSql);
    $totalRecords = mysqli_fetch_assoc($countResult)['total'];
    $totalPages = ceil($totalRecords / $limit); // Calculate total pages
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - View working hours</title>
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
            font-size: 14px;
        }

        .table td {
            text-align: center;
            vertical-align: middle;
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
                <h1>Staff Working Hours</h1>
                <p class="mb-0">View all working hours for staff</p>
            </div>

            <!-- Search and Filter Form -->
            <div class="card p-4 mb-4">
                <form method="get" class="row g-3">
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="search" placeholder="Search Staff Name" value="<?= htmlspecialchars($searchQuery) ?>">
                    </div>
                    <div class="col-md-4">
                        <select class="form-control" name="filter_staff" onchange="this.form.submit()">
                            <option value="">Filter by Staff Name</option>
                            <?php while ($dropdownRow = mysqli_fetch_assoc($dropdownResult)): ?>
                                <option value="<?= $dropdownRow['staff_id'] ?>" <?= $filterStaffId == $dropdownRow['staff_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($dropdownRow['name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Search</button>
                    </div>
                </form>
            </div>

            <!-- Staff Details Table -->
            <div class="card p-4">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-primary text-center">
                            <tr>
                                <th rowspan="2" class="align-middle">STAFF NAME</th>
                                <th colspan="2">MONDAY</th>
                                <th colspan="2">TUESDAY</th>
                                <th colspan="2">WEDNESDAY</th>
                                <th colspan="2">THURSDAY</th>
                                <th colspan="2">FRIDAY</th>
                                <th colspan="2">SATURDAY</th>
                                <th colspan="2">SUNDAY</th> 
                                <th>ACTION</th>
                            </tr>
                            <tr>
                                <th>START TIME</th>
                                <th>END TIME</th>
                                <th>START TIME</th>
                                <th>END TIME</th>
                                <th>START TIME</th>
                                <th>END TIME</th>
                                <th>START TIME</th>
                                <th>END TIME</th>
                                <th>START TIME</th>
                                <th>END TIME</th>
                                <th>START TIME</th>
                                <th>END TIME</th>
                                <th>START TIME</th>
                                <th>END TIME</th>
                                <th>MANAGE WORKING HOURS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                    <tr>
                                        <td class="text-start fw-bold" style="font-size: 15px;"><?= htmlspecialchars($row['name_upper']) ?></td>
                                        <td><?= htmlspecialchars($row['mon_start']) ?: '-' ?></td>
                                        <td><?= htmlspecialchars($row['mon_end']) ?: '-' ?></td>
                                        <td><?= htmlspecialchars($row['tue_start']) ?: '-' ?></td>
                                        <td><?= htmlspecialchars($row['tue_end']) ?: '-' ?></td>
                                        <td><?= htmlspecialchars($row['wed_start']) ?: '-' ?></td>
                                        <td><?= htmlspecialchars($row['wed_end']) ?: '-' ?></td>
                                        <td><?= htmlspecialchars($row['thu_start']) ?: '-' ?></td>
                                        <td><?= htmlspecialchars($row['thu_end']) ?: '-' ?></td>
                                        <td><?= htmlspecialchars($row['fri_start']) ?: '-' ?></td>
                                        <td><?= htmlspecialchars($row['fri_end']) ?: '-' ?></td>
                                        <td><?= htmlspecialchars($row['sat_start']) ?: '-' ?></td>
                                        <td><?= htmlspecialchars($row['sat_end']) ?: '-' ?></td>
                                        <td><?= htmlspecialchars($row['sun_start']) ?: '-' ?></td>
                                        <td><?= htmlspecialchars($row['sun_end']) ?: '-' ?></td>
                                        <td>
                                            <a href="edit_staff.php?id=<?= urlencode($row['admin_id']) ?>" class="btn btn-info btn-sm text-white">
                                                <i class="bi bi-pencil-square me-1"></i>Manage
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="16" class="text-center text-danger">No staff found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <br>
            <!-- Pagination Controls -->
            <nav>
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($searchQuery) ?>&filter_staff=<?= urlencode($filterStaffId) ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($searchQuery) ?>&filter_staff=<?= urlencode($filterStaffId) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($searchQuery) ?>&filter_staff=<?= urlencode($filterStaffId) ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <footer style="text-align: center;">
                &copy; <?= date('Y'); ?> LTA Services Sdn Bhd. All rights reserved.
            </footer>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
