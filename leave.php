<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LTA Attendance System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 10px;
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
        }

        .content {
            padding: 15px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
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
            padding: 8px 0;
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
    </style>
</head>
<body>
    <div class="content">
        <!-- Navigation Menu -->
        <table class="menu">
            <tr>
                <td onclick="window.location.href='#'">Calendar</td>
                <td onclick="window.location.href='#'">My Leave</td>
            </tr>
        </table>

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

            <div class="card shadow-lg">
                    <div class="card-body">
                        <table class="table">
                            <tr>
                                <td>Annual Leave</td>
                                <td style="text-align:right;">
                                    12d
                                    <i class="bi bi-chevron-right"></i>
                                </td>
                            </tr>
                            <tr>
                                <td>Hospitalization Leave</td>
                                <td style="text-align:right;">
                                    0d
                                    <i class="bi bi-chevron-right"></i>
                                </td>
                            </tr>
                            <tr>
                                <td>Sick Leave</td>
                                <td style="text-align:right;">
                                    11d
                                    <i class="bi bi-chevron-right"></i>
                                </td>
                            </tr>
                            <tr>
                                <td>Unpaid Leave</td>
                                <td style="text-align:right;">
                                    0d
                                    <i class="bi bi-chevron-right"></i>
                                </td>
                            </tr>
                        </table>
                    </div>
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
            "2024-01-01": "New Year's Day",
            "2024-05-01": "Labour Day",
            "2024-08-31": "National Day",
            "2024-09-16": "Malaysia Day",
            "2024-12-25": "Christmas Day",
            "2025-01-01": "New Year's Day",
            "2025-05-01": "Labour Day",
            "2025-08-31": "National Day",
            "2025-09-16": "Malaysia Day",
            "2025-12-25": "Christmas Day"
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
</body>
</html>
