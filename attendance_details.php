<?php
    // Include database connection
    include('connection/connection.php');

    $id = $_REQUEST['id'];
    $datemonth = $_REQUEST['date']; // Format: MM

    $sql = "SELECT name, mon_worktype, mon_start, mon_breakin, mon_breakout, mon_remarks,
                   tue_worktype, tue_start, tue_breakin, tue_breakout, tue_remarks,
                   wed_worktype, wed_start, wed_breakin, wed_breakout, wed_remarks,
                   thu_worktype, thu_start, thu_breakin, thu_breakout, thu_remarks,
                   fri_worktype, fri_start, fri_breakin, fri_breakout, fri_remarks,
                   sat_worktype, sat_start, sat_breakin, sat_breakout, sat_remarks,
                   sun_worktype, sun_start, sun_breakin, sun_breakout, sun_remarks
            FROM admin WHERE staff_id = '$id'";

    $result = mysqli_query($connection, $sql);
    $row = mysqli_fetch_assoc($result);
    if (!$row) {
        die("Invalid staff ID.");
    }

    $staffname = $row['name'];

    // Store individual day worktype columns
    $monWorkType = $row['mon_worktype'];
    $tueWorkType = $row['tue_worktype'];
    $wedWorkType = $row['wed_worktype'];
    $thuWorkType = $row['thu_worktype'];
    $friWorkType = $row['fri_worktype'];
    $satWorkType = $row['sat_worktype'];
    $sunWorkType = $row['sun_worktype'];

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

    // Validate and format month input
    if (!empty($datemonth) && is_numeric($datemonth) && $datemonth >= 1 && $datemonth <= 12) {
        $month = (int)$datemonth;
        $year = date('Y'); // Default to current year

        // Convert numeric month to short name (e.g., 12 -> Dec)
        $monthName = date('M', mktime(0, 0, 0, $month, 10));

        // Prepare attendance data and work status
        $dates = [];

        

        for ($day = 1; $day <= cal_days_in_month(CAL_GREGORIAN, $month, $year); $day++) {
            $dateObj = new DateTime("$year-$month-$day");
            $dateStr = $dateObj->format('Y-m-d');
            $dayOfWeek = strtolower($dateObj->format('D'));

            // Map day of the week to admin table columns
            $workTypeCol = "{$dayOfWeek}_worktype";
            $startCol = "{$dayOfWeek}_start";
            $breakInCol = "{$dayOfWeek}_breakin";
            $breakOutCol = "{$dayOfWeek}_breakout";
            $remarksCol = "{$dayOfWeek}_remarks";

            // Fetch attendance data for the specific day
            $attendanceQuery = "SELECT * FROM attendance WHERE staff_id = '$id' AND date = '$dateStr'";
            $attendanceResult = mysqli_query($connection, $attendanceQuery);
            $attendance = mysqli_fetch_assoc($attendanceResult);

            // Determine remarks based on admin data
            $remarks = $row[$workTypeCol] ?? '';

            $status = null; // Default to null for flexi days and holidays

            // Check if it's a public holiday
            $isPublicHoliday = isset($publicHolidays[$dateObj->format('m-d')]);

            // Check if the work type is "flexi"
            $isFlexi = ($row[$workTypeCol] ?? '') === 'FLEXI DAY';

            // Determine the status based on conditions
            if ($isPublicHoliday || $isFlexi) {
                $status = null; // Empty for public holidays or flexi days
            } elseif (!empty($attendance['clock_in']) && $attendance['clock_in'] != '00:00:00') {
                $status = 'incomplete';
            } else {
                $status = 'absence';
            }

            $dates[] = [
                'date' => $dateObj->format('M j'),
                'day' => $dateObj->format('D'),
                'remarks' => $remarks,
                'status' => $status,
                'note' => isset($publicHolidays[$dateObj->format('Y-m-d')]) 
                          ? $publicHolidays[$dateObj->format('Y-m-d')] 
                          : ($attendance['remarks'] ?? $row[$remarksCol] ?? ''),
                'start_work' => (!empty($attendance['clock_in']) && $attendance['clock_in'] != '00:00:00') 
                                ? date('H:i', strtotime($attendance['clock_in'])) 
                                : '',
                'break_in' => (!empty($attendance['break1_in']) && $attendance['break1_in'] != '00:00:00') 
                                ? date('H:i', strtotime($attendance['break1_in'])) 
                                : '',
                'break_out' => (!empty($attendance['break1_out']) && $attendance['break1_out'] != '00:00:00') 
                                ? date('H:i', strtotime($attendance['break1_out'])) 
                                : '',
                'end_work' => (!empty($attendance['clock_out']) && $attendance['clock_out'] != '00:00:00') 
                              ? date('H:i', strtotime($attendance['clock_out'])) 
                              : '',
                'hour_worked' => $attendance['hours_worked'] ?? '',
                'ot' => $attendance['overtime'] ?? '',
                'lateness' => $attendance['lateness'] ?? '',
            ];
            
            $hrsWorked = 0; // Total hours worked for normal workdays
            $daysWorked = 0; // Total days worked for normal workdays
            $totalOT = 0; // Total overtime for normal workdays

            $hrsWorkedRest = 0; // Total hours worked for rest days
            $daysWorkedRest = 0; // Total days worked for rest days
            $totalOTRest = 0; // Total overtime for rest days

            $hrsWorkedHoliday = 0; // Total hours worked for public holidays
            $daysWorkedHoliday = 0; // Total days worked for public holidays
            $totalOTHoliday = 0; // Total overtime for public holidays
            $absenceCount = 0;

            foreach ($dates as $dateInfo) {
                // Check if it's a rest day
                $isRestDay = strtolower($dateInfo['remarks']) === 'rest day';
            
                // Check if it's a public holiday
                $isHoliday = isset($publicHolidays[date('m-d', strtotime($dateInfo['date']))]);
            
                // Check if the worktype is "normal"
                $isNormalWorkType = strtolower($dateInfo['remarks']) === 'normal';
            
                // Calculate for normal workdays
                if ($isNormalWorkType) {
                    // Calculate total hours worked for normal workdays (where clock-in is done)
                    if (!empty($dateInfo['start_work'])) {
                        $hrsWorked += (float)$dateInfo['hour_worked']; // Add the hours worked
                        $daysWorked++; // Increment days worked
                        $totalOT += (float)($dateInfo['ot'] ?? 0); // Add overtime
                    }
                }
            
                // Calculate for rest days
                if ($isRestDay) {
                    // Calculate total hours worked on rest days (where clock-in is done)
                    if (!empty($dateInfo['start_work'])) {
                        $hrsWorkedRest += (float)$dateInfo['hour_worked']; // Add the hours worked
                        $daysWorkedRest++; // Increment days worked on rest days
                        $totalOTRest += (float)($dateInfo['ot'] ?? 0); // Add overtime on rest days
                    }
                }
            
                // Calculate for public holidays
                if ($isHoliday) {
                    // Calculate total hours worked on public holidays (where clock-in is done)
                    if (!empty($dateInfo['start_work']) ) {
                        $hrsWorkedHoliday += (float)$dateInfo['hour_worked']; // Add the hours worked
                        $daysWorkedHoliday++; // Increment days worked on public holidays
                        $totalOTHoliday += (float)($dateInfo['ot'] ?? 0); // Add overtime on public holidays
                    }
                }
            
                // Count absences
                if ($dateInfo['status'] === 'absence') {
                    $absenceCount++;
                }
            }            

        }
    } else {
        die("Invalid or missing month value.");
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">

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
        table {
            text-align: center;
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
                <h4 class="text-primary mb-4"><i class="bi bi-person-gear me-2"></i> Attendance Record : <?php echo $staffname; ?></h4>
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Attendance Record: <?php echo "$monthName $year"; ?></h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead class="table-primary">
                                    <tr>
                                        <th rowspan='2'>Date</th>
                                        <th rowspan='2'>Day</th>
                                        <th rowspan='2'>Remarks</th>
                                        <th rowspan='2'>Status</th>
                                        <th rowspan='2'>Start Work</th>
                                        <th colspan='2'>Lunch</th>
                                        <th rowspan='2'>End Work</th>
                                        <th rowspan='2'>Hour Worked</th>
                                        <th rowspan='2'>OT</th>
                                        <th rowspan='2'>Lateness</th>
                                        <th rowspan='2'>Notes</th>
                                    </tr>

                                    <tr>
                                        <th>Out</th>
                                        <th>In</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dates as $dateInfo): ?>
                                        <tr>
                                            <td><?php echo $dateInfo['date']; ?></td>
                                            <td><?php echo $dateInfo['day']; ?></td>
                                            <td><?php echo $dateInfo['remarks']; ?></td>
                                            <td><?php echo $dateInfo['status']; ?></td>
                                            <td><?php echo $dateInfo['start_work']; ?></td>
                                            <td><?php echo $dateInfo['break_in']; ?></td>
                                            <td><?php echo $dateInfo['break_out']; ?></td>
                                            <td><?php echo $dateInfo['end_work']; ?></td>
                                            <td><?php echo $dateInfo['hour_worked']; ?></td>
                                            <td><?php echo $dateInfo['ot']; ?></td>
                                            <td><?php echo $dateInfo['lateness']; ?></td>
                                            <td><?php echo $dateInfo['note']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <table class="table table-bordered table-striped table-hover">
                                <thead class="table-primary">
                                    <tr>
                                        <th colspan="6" class="text-center fs-5">Summary of Attendance</th>
                                    </tr>
                                    <tr>
                                        <th>Category</th>
                                        <th>Hours Worked</th>
                                        <th>Days Worked</th>
                                        <th>OT</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Regular</td>
                                        <td>Hrs Worked = (<?php echo $hrsWorked; ?>)</td>
                                        <td>Day Worked = (<?php echo $daysWorked; ?>)</td>
                                        <td>OT = (<?php echo $totalOT; ?>)</td>
                                        <td>Lateness = ()</td>
                                    </tr>
                                    <tr>
                                        <td>Rest Day</td>
                                        <td>Hrs Worked (Rest) = (<?php echo $hrsWorkedRest; ?>)</td>
                                        <td>Day Worked (Rest) = (<?php echo $daysWorkedRest; ?>)</td>
                                        <td>OT (Rest) = (<?php echo $totalOTRest; ?>)</td>
                                        <td>Late Count = ()</td>
                                    </tr>
                                    <tr>
                                        <td>Holiday</td>
                                        <td>Hrs Worked (Holiday) = (<?php echo $hrsWorkedHoliday; ?>)</td>
                                        <td>Day Worked (Holiday) = (<?php echo $daysWorkedHoliday; ?>)</td>
                                        <td>OT (Holiday) = (<?php echo $totalOTHoliday; ?>)</td>
                                        <td>Absence = (<?php echo $absenceCount; ?>)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="attendance_pdf.php?id=<?php echo $id; ?>&date=<?php echo $datemonth; ?>" class="btn btn-danger btn-sm" target="_blank">
                            <i class="bi bi-file"></i> PDF
                        </a>
                        <!-- <a href="attendance_excel.php?id=<?php echo $id; ?>&date=<?php echo $datemonth; ?>" class="btn btn-success btn-sm" target="_blank">
                            <i class="bi bi-file"></i> Excel
                        </a> -->
                    </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
