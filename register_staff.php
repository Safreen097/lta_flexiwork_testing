<?php
// Include database connection
include('connection/connection.php');

// Fetch the next available admin_id
$nextAdminIdQuery = "SELECT MAX(admin_id) + 1 AS next_id FROM admin";
$nextAdminIdResult = mysqli_query($connection, $nextAdminIdQuery);
$nextAdminId = mysqli_fetch_assoc($nextAdminIdResult)['next_id'] ?? 1; // Default to 1 if no data exists

// Days of the week for working hours
$daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Register Staff</title>
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

        .content {
            flex-grow: 1;
            padding: 20px;
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
            <div class="header">
                <h1>Register Staff</h1>
                <p class="mb-0">Fill in the form below to add a new staff member to the system.</p>
            </div>

            <div class="card p-4">
                <form id="registerForm" action="register_staff_process.php" method="post">
                    <!-- Staff ID -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-uppercase">Next Staff ID</label>
                        <input type="text" class="form-control" name="staffid" value="<?= htmlspecialchars($nextAdminId); ?>" readonly>
                    </div>

                    <!-- Full Name -->
                    <div class="mb-3">
                        <label for="fullName" class="form-label fw-bold text-uppercase">Full Name</label>
                        <input type="text" class="form-control" id="fullName" name="fullName" placeholder="ENTER FULL NAME" required>
                    </div>

                    <!-- IC Number -->
                    <div class="mb-3">
                        <label for="icNumber" class="form-label fw-bold text-uppercase">IC Number / Passport</label>
                        <input type="text" class="form-control" id="icNumber" name="icNumber" placeholder="Enter IC / PASSPORT NUMBER" required>
                    </div>

                    <!-- Username -->
                    <div class="mb-3">
                        <label for="username" class="form-label fw-bold text-uppercase">Username to login into system</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="USERNAME TO LOGIN" required>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold text-uppercase">Password to login into system</label>
                        <input type="password" class="form-control" id="pass" name="pass" placeholder="PASSWORD TO LOGIN" required>
                    </div>

                    <!-- Working Hours Section -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-uppercase">Working Hours</label>
                        <div class="accordion" id="workingHoursAccordion">
                            <?php foreach ($daysOfWeek as $day): ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading<?= $day ?>">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $day ?>" aria-expanded="false" aria-controls="collapse<?= $day ?>">
                                            <?= strtoupper($day) ?>
                                        </button>
                                    </h2>
                                    <div id="collapse<?= $day ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $day ?>" data-bs-parent="#workingHoursAccordion">
                                        <div class="accordion-body">
                                            <div class="row g-3">
                                                <!-- Working Hours Type -->
                                                <div class="col-md-2">
                                                    <label class="form-label fw-bold text-uppercase">Working Hours Type</label>
                                                    <select name="<?= strtolower($day) ?>_worktype" class="form-select">
                                                        <option value="NORMAL">NORMAL</option>
                                                        <option value="FLEXI DAY">FLEXI DAY</option>
                                                    </select>
                                                </div>

                                                <!-- Start Time -->
                                                <div class="col-md-2">
                                                    <label class="form-label fw-bold text-uppercase">Start Time</label>
                                                    <input type="time" name="<?= strtolower($day) ?>_start" class="form-control" value="08:00">
                                                </div>

                                                <!-- End Time -->
                                                <div class="col-md-2">
                                                    <label class="form-label fw-bold text-uppercase">End Time</label>
                                                    <input type="time" name="<?= strtolower($day) ?>_end" class="form-control" value="17:00">
                                                </div>

                                                <!-- Break In -->
                                                <div class="col-md-2">
                                                    <label class="form-label fw-bold text-uppercase">Break In</label>
                                                    <input type="time" name="<?= strtolower($day) ?>_breakin" class="form-control" value="13:00">
                                                </div>

                                                <!-- Break Out -->
                                                <div class="col-md-2">
                                                    <label class="form-label fw-bold text-uppercase">Break Out</label>
                                                    <input type="time" name="<?= strtolower($day) ?>_breakout" class="form-control" value="12:00">
                                                </div>

                                                <!-- Remarks -->
                                                <div class="col-md-2">
                                                    <label class="form-label fw-bold text-uppercase">Remarks</label>
                                                    <input type="text" name="<?= strtolower($day) ?>_remarks" class="form-control" placeholder="Optional">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-2"></i>Register</button>
                        <button type="button" class="btn btn-secondary" onclick="document.getElementById('registerForm').reset();"><i class="bi bi-x-lg me-2"></i>Clear</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
