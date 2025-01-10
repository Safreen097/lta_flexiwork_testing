<?php
    // Include database connection
    include('connection/connection.php');

    $id = $_SESSION['id']; // Staff ID for filtering

    $sql = "SELECT * FROM admin WHERE staff_id='$id'";
    $result = mysqli_query($connection, $sql);
    $row = mysqli_fetch_assoc($result);

    // Monday details
    $mon_worktype = $row['mon_worktype'];
    $mon_start = $row['mon_start'];
    $mon_end = $row['mon_end'];
    $mon_breakin = $row['mon_breakin'];
    $mon_breakout = $row['mon_breakout'];
    $mon_remarks = $row['mon_remarks'];

    // Tuesday details
    $tue_worktype = $row['tue_worktype'];
    $tue_start = $row['tue_start'];
    $tue_end = $row['tue_end'];
    $tue_breakin = $row['tue_breakin'];
    $tue_breakout = $row['tue_breakout'];
    $tue_remarks = $row['tue_remarks'];

    // Wednesday details
    $wed_worktype = $row['wed_worktype'];
    $wed_start = $row['wed_start'];
    $wed_end = $row['wed_end'];
    $wed_breakin = $row['wed_breakin'];
    $wed_breakout = $row['wed_breakout'];
    $wed_remarks = $row['wed_remarks'];

    // Thursday details
    $thu_worktype = $row['thu_worktype'];
    $thu_start = $row['thu_start'];
    $thu_end = $row['thu_end'];
    $thu_breakin = $row['thu_breakin'];
    $thu_breakout = $row['thu_breakout'];
    $thu_remarks = $row['thu_remarks'];

    // Friday details
    $fri_worktype = $row['fri_worktype'];
    $fri_start = $row['fri_start'];
    $fri_end = $row['fri_end'];
    $fri_breakin = $row['fri_breakin'];
    $fri_breakout = $row['fri_breakout'];
    $fri_remarks = $row['fri_remarks'];

    // Saturday details
    $sat_worktype = $row['sat_worktype'];
    $sat_start = $row['sat_start'];
    $sat_end = $row['sat_end'];
    $sat_breakin = $row['sat_breakin'];
    $sat_breakout = $row['sat_breakout'];
    $sat_remarks = $row['sat_remarks'];

    // Sunday details
    $sun_worktype = $row['sun_worktype'];
    $sun_start = $row['sun_start'];
    $sun_end = $row['sun_end'];
    $sun_breakin = $row['sun_breakin'];
    $sun_breakout = $row['sun_breakout'];
    $sun_remarks = $row['sun_remarks'];

    // Create a structured array for all days
    $workingHoursData = [
        "mon" => [
            "work_type" => $mon_worktype,
            "start_time" => $mon_start,
            "end_time" => $mon_end,
            "break_in" => $mon_breakin,
            "break_out" => $mon_breakout,
            "remarks" => $mon_remarks
        ],
        "tue" => [
            "work_type" => $tue_worktype,
            "start_time" => $tue_start,
            "end_time" => $tue_end,
            "break_in" => $tue_breakin,
            "break_out" => $tue_breakout,
            "remarks" => $tue_remarks
        ],
        "wed" => [
            "work_type" => $wed_worktype,
            "start_time" => $wed_start,
            "end_time" => $wed_end,
            "break_in" => $wed_breakin,
            "break_out" => $wed_breakout,
            "remarks" => $wed_remarks
        ],
        "thu" => [
            "work_type" => $thu_worktype,
            "start_time" => $thu_start,
            "end_time" => $thu_end,
            "break_in" => $thu_breakin,
            "break_out" => $thu_breakout,
            "remarks" => $thu_remarks
        ],
        "fri" => [
            "work_type" => $fri_worktype,
            "start_time" => $fri_start,
            "end_time" => $fri_end,
            "break_in" => $fri_breakin,
            "break_out" => $fri_breakout,
            "remarks" => $fri_remarks
        ],
        "sat" => [
            "work_type" => $sat_worktype,
            "start_time" => $sat_start,
            "end_time" => $sat_end,
            "break_in" => $sat_breakin,
            "break_out" => $sat_breakout,
            "remarks" => $sat_remarks
        ],
        "sun" => [
            "work_type" => $sun_worktype,
            "start_time" => $sun_start,
            "end_time" => $sun_end,
            "break_in" => $sun_breakin,
            "break_out" => $sun_breakout,
            "remarks" => $sun_remarks
        ]
    ];
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

    <script>
        const workingHoursData = <?php echo json_encode($workingHoursData); ?>;
    </script>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
        }

        .content {
            padding: 20px;
        }

        .menu {
            width: 100%;
            margin-bottom: 20px;
            border-radius: 10px;
            overflow: hidden;
            display: flex;
        }

        .menu td {
            text-align: center;
            padding: 10px 0;
            font-weight: bold;
            color: #555;
            flex: 1;
            cursor: pointer;
            background-color: #fff;
            border: 1px solid #ddd;
        }

        .menu td:hover {
            background-color: #007bff;
            color: white;
            transition: background-color 0.3s ease;
        }

        .card {
            border: none;
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .card:hover {
            transform: scale(1.02);
            transition: transform 0.2s ease;
        }

        .card-body {
            padding: 15px;
        }

        .card-body table {
            width: 100%;
        }

        .card-body tr {
            cursor: pointer;
        }

        .card-body tr:hover {
            background-color: #f1f1f1;
            transition: background-color 0.2s ease;
        }

        .card-body td {
            padding: 10px;
            vertical-align: middle;
        }

        .card-body .arrow-icon {
            font-size: 20px;
            color: #007bff;
            margin-left: 10px;
        }

        .card-body .arrow-icon:hover {
            color: #0056b3;
            transition: color 0.2s ease;
        }

        .icon-dot {
            color: #28a745;
        }

        .shift-item {
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .shift-item:last-child {
            border-bottom: none; /* Remove the line for the last item */
        }

        .shift-item a {
            text-decoration: none;
            color: inherit; /* Inherit text color */
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%; /* Make the link span full width */
        }

        .shift-item a:hover {
            background-color: #f9f9f9; /* Add a hover effect */
            border-radius: 5px;
            padding: 5px;
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="col-12">
            <div class="card shadow-sm mb-3">
                <div class="card-body d-flex align-items-center justify-content-center text-center">
                    <i class="bi bi-clock text-primary fs-4 me-2"></i>
                    <h5 class="card-title m-0">Shift</h5>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card shadow-sm mb-3">
                <div class="card-body p-3 shift-container">
                    <!-- Shifts will be dynamically inserted here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    const shiftContainer = document.querySelector(".shift-container");

    // Function to calculate the current real-time week (Monday to Sunday)
    const getRealTimeWeekDays = () => {
        const today = new Date();
        const dayOfWeek = today.getDay(); // 0 = Sunday, 1 = Monday, ..., 6 = Saturday

        // Find the start of the week (Monday)
        const startOfWeek = new Date(today);
        const offset = dayOfWeek === 0 ? -6 : 1 - dayOfWeek; // Adjust for Monday as the first day
        startOfWeek.setDate(today.getDate() + offset);

        // Collect all days of the current week
        const weekDays = [];
        const dayNames = ["mon", "tue", "wed", "thu", "fri", "sat", "sun"]; // Short day names

        for (let i = 0; i < 7; i++) {
            const currentDay = new Date(startOfWeek);
            currentDay.setDate(startOfWeek.getDate() + i);

            // Format date for display
            const display = currentDay.toLocaleDateString("en-US", {
                weekday: "short",
                day: "numeric",
                month: "short",
            });

            // Format date as key (e.g., "2024-10-26")
            const dateKey = currentDay.toISOString().split("T")[0];

            weekDays.push({
                name: dayNames[i], // Short day name (mon, tue, etc.)
                date: dateKey,
                display: display,
            });
        }

        return weekDays;
    };

    // Fetch attendance data from the server
    const fetchAttendanceData = async () => {
        try {
            const response = await fetch("fetch_attendance.php");
            const data = await response.json();

            console.log("Fetched Attendance Data:", data); // Debugging

            if (data.success) {
                return data.data; // Return attendance data
            } else {
                console.error("Failed to fetch attendance data:", data.message);
                return {};
            }
        } catch (error) {
            console.error("Error fetching attendance data:", error);
            return {};
        }
    };

    // Render shifts for the current real-time week
    const renderRealTimeShifts = async () => {
        const attendanceData = await fetchAttendanceData();
        const weekDays = getRealTimeWeekDays(); // Get current week's dates
        shiftContainer.innerHTML = ""; // Clear the container

        weekDays.forEach((dayObj) => {
            const dayData = workingHoursData[dayObj.name]; // Access PHP data
            const workType = dayData?.work_type || "N/A";
            const startTime = dayData?.start_time || "N/A";
            const endTime = dayData?.end_time || "N/A";
            const hours =
                startTime !== "N/A" && endTime !== "N/A"
                    ? `${startTime} - ${endTime}`
                    : "N/A";

            // Get clock-in status from attendance data
            const attendanceRecord = attendanceData[dayObj.date];
            let clockIn = "Not Clocked In";
            let textColorClass = "text-danger"; // Red color for "Not Clocked In"

            if (attendanceRecord && attendanceRecord.clock_in) {
                const clockInTime = attendanceRecord.clock_in.split(" ")[1]; // Extract time part (hh:mm:ss)
                if (clockInTime) {
                    const [hours, minutes, seconds] = clockInTime.split(":"); // Extract hh, mm, and ss
                    let period = "AM"; // Default to AM
                    let hour = parseInt(hours, 10); // Convert to an integer

                    if (hour >= 12) {
                        period = "PM";
                        if (hour > 12) hour -= 12; // Convert to 12-hour format
                    } else if (hour === 0) {
                        hour = 12; // Midnight is 12 AM
                    }

                    clockIn = `${hour}:${minutes} ${period}`; // Format as hh:mm AM/PM
                    textColorClass = "text-success"; // Green color for "Clocked In"
                }
            }

            const dayHTML = `
                <div class="shift-item">
                    <a href="shift_details.php?date=${dayObj.date}" target="mainframe">
                        <div>
                            <span class="fw-bold">${dayObj.display}</span>
                        </div>
                        <div class="text-center">
                            <span class="fw-bold">${workType}</span><br>
                            <span class="small text-secondary">${hours}</span>
                        </div>
                        <div class="text-end">
                            <span class="${textColorClass} small">${clockIn}</span>
                            <i class="bi bi-chevron-right"></i>
                        </div>
                    </a>
                </div>
            `;

            shiftContainer.innerHTML += dayHTML;
        });
    };

    // Initial load of real-time shifts
    renderRealTimeShifts();
</script>
</body>
</html>
