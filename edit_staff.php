<?php
    // Include database connection
    include('connection/connection.php');

    $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

    $id = $_REQUEST['id'];

    $sql = "SELECT * FROM admin WHERE admin_id='$id'";
    $result = mysqli_query($connection, $sql);
    $staffdetails = mysqli_fetch_assoc($result);

    $staff_id = $staffdetails['admin_id'];
    $staff_name = $staffdetails['name'];
    $ic_pass = $staffdetails['ic_passport'];

    // Map days to their database fields
    $workDetails = [
        'mon' => 'Monday',
        'tue' => 'Tuesday',
        'wed' => 'Wednesday',
        'thu' => 'Thursday',
        'fri' => 'Friday',
        'sat' => 'Saturday',
        'sun' => 'Sunday',
    ];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff Working Hours</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f5f7;
            margin: 0;
        }

        .sidebar {
            background-color: #4e73df;
            color: #fff;
            min-height: 100vh;
            padding: 20px;
        }

        .sidebar h5 {
            font-weight: bold;
            margin-bottom: 30px;
            text-align: center;
        }

        .sidebar a {
            color: #fff;
            text-decoration: none;
            display: block;
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 6px;
            transition: all 0.3s;
        }

        .sidebar a:hover {
            background-color: #2e59d9;
        }

        .content {
            padding: 30px;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            overflow: hidden;
        }

        .card-header {
            background-color: #4e73df;
            color: white;
            padding: 20px;
            font-size: 18px;
            font-weight: bold;
        }

        table {
            font-size: 14px;
            margin: 0 auto;
            width: 100%;
        }

        th, td {
            text-align: center;
            vertical-align: middle;
        }

        th {
            background-color: #f8f9fc;
            color: #6c757d;
        }

        .form-control {
            border-radius: 6px;
            font-size: 14px;
        }

        .btn-custom {
            background-color: #4e73df;
            color: #fff;
            font-size: 16px;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #2e59d9;
        }

        @media (max-width: 768px) {
            .content {
                padding: 20px;
            }

            .sidebar {
                padding: 15px;
            }

            .sidebar a {
                font-size: 14px;
                padding: 10px;
            }
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
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-person me-2"></i> <?= htmlspecialchars($staff_name); ?> - Working Hours
                </div>
                <div class="card-body">
                    <form id="updateworkinghours" action="update_working_hours_process.php" method="post">
                        <input type="hidden" name="id" class="form-control" value="<?= htmlspecialchars($staff_id); ?>">
                        <div class="accordion" id="workingHoursAccordion">
                            <?php foreach ($workDetails as $dayCode => $dayName): ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading<?= ucfirst($dayCode) ?>">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= ucfirst($dayCode) ?>" aria-expanded="false" aria-controls="collapse<?= ucfirst($dayCode) ?>">
                                            <?= strtoupper($dayName) ?>
                                        </button>
                                    </h2>
                                    <div id="collapse<?= ucfirst($dayCode) ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= ucfirst($dayCode) ?>" data-bs-parent="#workingHoursAccordion">
                                        <div class="accordion-body">
                                            <table class="table table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Work Type</th>
                                                        <th>Start Time</th>
                                                        <th>End Time</th>
                                                        <th>Break Start</th>
                                                        <th>Break End</th>
                                                        <th>Remarks</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <select name="<?= $dayCode ?>_worktype" class="form-select">
                                                                <option value="NORMAL" <?= ($staffdetails[$dayCode . '_worktype'] == 'NORMAL') ? 'selected' : '' ?>>NORMAL</option>
                                                                <option value="FLEXI DAY" <?= ($staffdetails[$dayCode . '_worktype'] == 'FLEXI DAY') ? 'selected' : '' ?>>FLEXI DAY</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="time" name="<?= $dayCode ?>_start" class="form-control" value="<?= htmlspecialchars($staffdetails[$dayCode . '_start']) ?>">
                                                        </td>
                                                        <td>
                                                            <input type="time" name="<?= $dayCode ?>_end" class="form-control" value="<?= htmlspecialchars($staffdetails[$dayCode . '_end']) ?>">
                                                        </td>
                                                        <td>
                                                            <input type="time" name="<?= $dayCode ?>_breakstart" class="form-control" value="<?= htmlspecialchars($staffdetails[$dayCode . '_breakin']) ?>">
                                                        </td>
                                                        <td>
                                                            <input type="time" name="<?= $dayCode ?>_breakend" class="form-control" value="<?= htmlspecialchars($staffdetails[$dayCode . '_breakout']) ?>">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="<?= $dayCode ?>_remark" class="form-control" value="<?= htmlspecialchars($staffdetails[$dayCode . '_remarks']) ?>" placeholder="Enter remarks">
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" name="action" value="Update" class="btn btn-custom w-50">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

