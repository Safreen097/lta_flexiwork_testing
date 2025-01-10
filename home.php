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
            padding: 20px;
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
        }

        .content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .card-body {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .d-flex {
            font-size: 14px;
        }

        .fw-bold {
            font-weight: bold;
        }

        .text-muted {
            color: #6c757d;
        }

        .text-success {
            color: #28a745;
        }

        .text-danger {
            color: #dc3545;
        }

        .border-bottom {
            border-bottom: 1px solid #f0f0f0 !important;
        }

        .py-2 {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }

        .small {
            font-size: 12px;
        }

        .card-title {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
            color: #333;
        }

        .table td {
            padding: 8px 10px;
            vertical-align: middle;
        }

        .table td:first-child {
            font-weight: bold;
            color: #555;
        }

        .btn {
            border-radius: 20px;
            padding: 8px 15px;
            font-size: 14px;
            font-weight: bold;
            transition: background-color 0.2s, transform 0.2s;
        }

        .btn:hover {
            transform: scale(1.05);
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .text-primary {
            color: #007bff !important;
        }

        .text-success {
            color: #28a745 !important;
        }

        .text-danger {
            color: #dc3545 !important;
        }

        .calendar-card {
            width: 100%; /* Set the card to 30% of the screen width */
            min-width: 300px; /* Ensure it doesn't get too small */
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            background-color: white;
            padding: 15px;
        }

        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 2px;
            width: 100%;
        }

        .calendar .day,
        .calendar .header {
            text-align: center;
            padding: 5px;
            border: 1px solid #ddd;
            background-color: #fff;
            font-size: 0.9rem; /* Smaller text size for compact look */
        }

        .calendar .header {
            font-weight: bold;
            background-color: #007bff;
            color: white;
        }

        .calendar .day.today {
            background-color: #ffeb3b;
            font-weight: bold;
        }

        .calendar .day.empty {
            background-color: #f9f9f9;
            border: none;
        }

        .calendar .day.holiday {
            background-color: #ffcccc;
            color: #d9534f;
            font-weight: bold;
            cursor: pointer;
        }

        .calendar-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .calendar-nav button {
            background-color: transparent;
            border: none;
            font-size: 20px;
            cursor: pointer;
        }

        .calendar-nav button:hover {
            color: #007bff;
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
        <div class="row g-4">
            <!-- Calendar in a Card -->
            <div class="calendar-card">
                <div class="calendar-nav">
                    <button id="prevMonthBtn"><i class="bi bi-chevron-left"></i></button>
                    <h5 id="currentMonth" class="text-center"></h5>
                    <button id="nextMonthBtn"><i class="bi bi-chevron-right"></i></button>
                </div>
                <div class="calendar" id="calendar"></div>
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
        const calendarElement = document.getElementById("calendar");
        const currentMonthElement = document.getElementById("currentMonth");
        const prevMonthBtn = document.getElementById("prevMonthBtn");
        const nextMonthBtn = document.getElementById("nextMonthBtn");

        let currentYear, currentMonth;

        // Malaysia public holidays for 2024 and 2025
        const publicHolidays = {
            '2024-12-25' : 'Christmas Day',
            '2025-01-01' : 'New Year\'s Day',
            '2025-01-29' : 'Chinese New Year',
            '2025-01-30' : 'Chinese New Year (2nd Day)',
            '2025-03-31' : 'Hari Raya Aidilfitri',
            '2025-04-01' : 'Hari Raya Aidilfitri (2nd Day)',
            '2025-04-18' : 'Good Friday',
            '2025-05-01' : 'Labour Day',
            '2025-05-12' : 'Wesak Day',
            '2025-06-01' : 'Gawai Dayak',
            '2025-06-02' : 'Gawai Dayak (2nd Day)',
            '2025-06-07' : 'Agong\'s Birthday',
            '2025-06-29' : 'Hari Raya Haji',
            '2025-07-22' : 'Sarawak Independence Day',
            '2025-07-27' : 'Awal Muharram',
            '2025-08-31' : 'National Day',
            '2025-09-16' : 'Malaysia Day',
            '2025-10-11' : 'Yang di-Pertua Negeri Sarawak\'s Birthday',
            '2025-12-25' : 'Christmas Day',
        };

        const renderCalendar = () => {
            const now = new Date();
            const todayDate = now.getDate();
            const todayMonth = now.getMonth();
            const todayYear = now.getFullYear();

            const monthNames = [
                "January", "February", "March", "April",
                "May", "June", "July", "August",
                "September", "October", "November", "December"
            ];

            // Display the current month and year
            currentMonthElement.textContent = `${monthNames[currentMonth]} ${currentYear}`;

            // Clear the calendar
            calendarElement.innerHTML = "";

            // Days of the week headers
            const daysOfWeek = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
            daysOfWeek.forEach(day => {
                const header = document.createElement("div");
                header.classList.add("header");
                header.textContent = day;
                calendarElement.appendChild(header);
            });

            // Get the first day of the month
            const firstDayOfMonth = new Date(currentYear, currentMonth, 1).getDay();

            // Get the number of days in the month
            const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();

            // Fill in empty spaces for days before the first of the month
            for (let i = 0; i < firstDayOfMonth; i++) {
                const emptyDay = document.createElement("div");
                emptyDay.classList.add("day", "empty");
                calendarElement.appendChild(emptyDay);
            }

            // Fill in the days of the month
            for (let day = 1; day <= daysInMonth; day++) {
                const dayElement = document.createElement("div");
                dayElement.classList.add("day");
                dayElement.textContent = day;

                const dateKey = `${currentYear}-${String(currentMonth + 1).padStart(2, "0")}-${String(day).padStart(2, "0")}`;

                // Highlight today's date
                if (day === todayDate && currentMonth === todayMonth && currentYear === todayYear) {
                    dayElement.classList.add("today");
                }

                // Highlight public holidays
                if (publicHolidays[dateKey]) {
                    dayElement.classList.add("holiday");
                    dayElement.title = publicHolidays[dateKey]; // Tooltip with holiday name
                    dayElement.addEventListener("click", () => {
                        alert(`Holiday: ${publicHolidays[dateKey]} on ${dateKey}`);
                    });
                }

                calendarElement.appendChild(dayElement);
            }
        };

        const changeMonth = (direction) => {
            if (direction === "prev") {
                currentMonth--;
                if (currentMonth < 0) {
                    currentMonth = 11;
                    currentYear--;
                }
            } else if (direction === "next") {
                currentMonth++;
                if (currentMonth > 11) {
                    currentMonth = 0;
                    currentYear++;
                }
            }
            renderCalendar();
        };

        prevMonthBtn.addEventListener("click", () => changeMonth("prev"));
        nextMonthBtn.addEventListener("click", () => changeMonth("next"));

        // Initialize the calendar with the current month
        const now = new Date();
        currentYear = now.getFullYear();
        currentMonth = now.getMonth();
        renderCalendar();
    </script>

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
