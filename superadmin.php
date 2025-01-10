<?php
    // Include database connection
    include('connection/connection.php');

    // Set the timezone to Kuala Lumpur
    date_default_timezone_set('Asia/Kuala_Lumpur');

    $id = $_SESSION['id']; // Staff ID for filtering

    // Debug the database connection
    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Get week offset (default to 0 for current week)
    $weekOffset = isset($_GET['weekOffset']) ? (int)$_GET['weekOffset'] : 0;

    // Calculate the start and end dates of the week
    $startDate = date('Y-m-d', strtotime("monday this week $weekOffset week"));
    $endDate = date('Y-m-d', strtotime("sunday this week $weekOffset week"));

    // Public holidays for Sarawak in 2025
    $publicHolidays = [
        '2024-12-25' => 'Christmas Day',
        '2025-01-01' => 'New Year\'s Day',
        '2025-01-29' => 'Chinese New Year',
        '2025-01-30' => 'Chinese New Year (2nd Day)',
        '2025-03-31' => 'Hari Raya Aidilfitri',
        '2025-04-01' => 'Hari Raya Aidilfitri (2nd Day)',
        '2025-04-18' => 'Good Friday',
        '2025-05-01' => 'Labour Day',
        '2025-05-12' => 'Wesak Day',
        '2025-06-01' => 'Gawai Dayak',
        '2025-06-02' => 'Gawai Dayak (2nd Day)',
        '2025-06-07' => 'Agong\'s Birthday',
        '2025-06-29' => 'Hari Raya Haji',
        '2025-07-22' => 'Sarawak Independence Day',
        '2025-07-27' => 'Awal Muharram',
        '2025-08-31' => 'National Day',
        '2025-09-16' => 'Malaysia Day',
        '2025-10-11' => 'Yang di-Pertua Negeri Sarawak\'s Birthday',
        '2025-12-25' => 'Christmas Day',
    ];

    // Fetch all staff with worktype for each day
    $staffQuery = "
        SELECT admin_id, staff_id, UPPER(name) AS name, 
               mon_worktype, tue_worktype, wed_worktype, 
               thu_worktype, fri_worktype, sat_worktype, sun_worktype
        FROM admin 
        ORDER BY name ASC";
    $staffResult = mysqli_query($connection, $staffQuery);
    if (!$staffResult) {
        die("Error in staff query: " . mysqli_error($connection));
    }

    // Initialize attendance array
    $attendanceData = [];

    // Fetch attendance for the week
    $attendanceQuery = "
        SELECT staff_id, DATE(date) AS date, clock_in, break1_in, break1_out, clock_out
        FROM attendance
        WHERE DATE(date) BETWEEN '$startDate' AND '$endDate'
    ";
    $attendanceResult = mysqli_query($connection, $attendanceQuery);
    if (!$attendanceResult) {
        die("Error in attendance query: " . mysqli_error($connection));
    }

    // Process attendance data
    while ($row = mysqli_fetch_assoc($attendanceResult)) {
        $attendanceData[$row['staff_id']][$row['date']] = $row;
    }

    // Count total staff in the admin table
    $totalStaffQuery = "SELECT COUNT(*) as total_staff FROM admin";
    $totalStaffResult = mysqli_query($connection, $totalStaffQuery);
    $totalStaff = 0;
    if ($totalStaffResult) {
        $row = mysqli_fetch_assoc($totalStaffResult);
        $totalStaff = $row['total_staff'];
    }

    $todayDate = date('Y-m-d');
    $attendanceTodayQuery = "SELECT COUNT(DISTINCT staff_id) AS total_attendance_today FROM attendance WHERE DATE(date) = '$todayDate'";
    $attendanceTodayResult = mysqli_query($connection, $attendanceTodayQuery);
    $totalAttendanceToday = 0;
    if ($attendanceTodayResult) {
        $row = mysqli_fetch_assoc($attendanceTodayResult);
        $totalAttendanceToday = $row['total_attendance_today'];
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff</title>
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
            padding: 20px;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .table-container {
            overflow-x: auto;
        }

        .table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .table th {
            background: linear-gradient(90deg, #4e73df, #2e59d9);
            color: white;
            text-align: center;
            padding: 12px;
        }

        .table td, .table th {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        .table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .table tr:nth-child(odd) {
            background-color: #ffffff;
        }

        .table tr:hover {
            background-color: #f1f1f1;
        }

        .employee-column {
            width: 20%;
            text-align: left;
        }

        .attendance-data {
            font-size: 1.0rem;
            font-weight: bold;
        }

        .text-highlight {
            color: #2e59d9;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="d-flex">
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
        <div class="content flex-grow-1">
            <div class="card p-4">
                <!-- Total Staff -->
                <div class="row mb-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="card shadow text-white h-100" style="background: linear-gradient(90deg, #4e73df, #2e59d9);">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h3 class="fw-bold mb-0"><?= $totalStaff ?></h3>
                                    <p class="mb-0">Total Staff</p>
                                </div>
                                <i class="bi bi-people-fill display-4"></i>
                            </div>
                            <div class="card-footer text-end">
                                <small class="text-light">Updated: <?= date('d M Y, h:i A') ?></small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="card shadow text-white h-100" style="background: linear-gradient(90deg, #28a745, #218838);">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h3 class="fw-bold mb-0"><?= $totalAttendanceToday ?></h3>
                                    <p class="mb-0">Attendance Today</p>
                                </div>
                                <i class="bi bi-check-circle-fill display-4"></i>
                            </div>
                            <div class="card-footer text-end">
                                <small class="text-light">Updated: <?= date('d M Y, h:i A') ?></small>
                            </div>
                        </div>
                    </div>
                </div>

                <h2 class="text-primary mb-4">Weekly Attendance</h2>

                <!-- Navigation Buttons -->
                <div class="d-flex justify-content-between mb-3">
                    <a href="?weekOffset=<?= $weekOffset - 1 ?>" class="btn btn-primary">&laquo; Previous Week</a>
                    <a href="?weekOffset=<?= $weekOffset + 1 ?>" class="btn btn-primary">Next Week &raquo;</a>
                </div>

                <!-- Attendance Table -->
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="employee-column">Employee</th>
                                <?php
                                // Display week days
                                for ($i = 0; $i < 7; $i++) {
                                    echo "<th>" . date('D, d M', strtotime("$startDate +$i days")) . "</th>";
                                }
                                ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($staff = mysqli_fetch_assoc($staffResult)): ?>
                                <tr>
                                    <td class="text-start"><?= htmlspecialchars($staff['name']) ?></td>
                                    <?php
                                    for ($i = 0; $i < 7; $i++) {
                                        $currentDate = date('Y-m-d', strtotime("$startDate +$i days"));
                                        $dayOfWeek = strtolower(date('D', strtotime($currentDate))); // e.g., "mon"
                                        $workTypeColumn = "{$dayOfWeek}_worktype"; // e.g., "mon_worktype"
                                        $workType = $staff[$workTypeColumn] ?? "Normal"; // Default work type if null
                                        $attendance = $attendanceData[$staff['staff_id']][$currentDate] ?? null;

                                        if (isset($publicHolidays[$currentDate])) {
                                            $holidayName = $publicHolidays[$currentDate];
                                            if (isset($attendance)) {
                                                $clockIn = $attendance['clock_in'] ? date('h:i A', strtotime($attendance['clock_in'])) : 'No Clock In';
                                                $clockOut = $attendance['clock_out'] ? date('h:i A', strtotime($attendance['clock_out'])) : '';
                                            } else {
                                                $clockIn = 'No Clock In';
                                                $clockOut = '';
                                            }
                                            echo "<td style='background-color: #fff3cd; color: #856404;'>
                                                <b class='text-highlight'>$workType</b><br>
                                                <span class='attendance-data'>$clockIn - $clockOut</span><br>
                                                <small><i>$holidayName</i></small>
                                            </td>";
                                        } else {
                                            if ($attendance) {
                                                $clockIn = $attendance['clock_in'] ? date('h:i A', strtotime($attendance['clock_in'])) : 'No Clock In';
                                                $clockOut = $attendance['clock_out'] ? date('h:i A', strtotime($attendance['clock_out'])) : '';
                                                $cellStyle = $attendance['clock_in']
                                                    ? "background-color: #d4edda; color: #155724;" // Green for clocked-in
                                                    : "background-color: #f8d7da; color: #721c24;"; // Red for no clock-in
                                            
                                                echo "<td style='$cellStyle'>
                                                    <b class='text-highlight'>$workType</b><br>
                                                    <span class='attendance-data'>$clockIn - $clockOut</span>
                                                </td>";
                                            } else {
                                                echo "<td style='background-color: #f8d7da; color: #721c24;'>
                                                    <b class='text-highlight'>$workType</b><br>
                                                    <span class='attendance-data'>No Clock In - </span>
                                                </td>";
                                            }
                                        }
                                    }
                                    ?>
                                </tr>
                            <?php endwhile; ?>
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

