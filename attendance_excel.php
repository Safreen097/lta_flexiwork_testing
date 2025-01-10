<?php
    // Include database connection
    include('connection/connection.php');

    $id = $_REQUEST['id'];
    $datemonth = $_REQUEST['date']; // Format: MM

    $sql = "SELECT name, staff_id, mon_worktype, mon_start, mon_breakin, mon_breakout, mon_remarks,
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
    $staff_id = $row['staff_id'];

    // Store individual day worktype columns
    $monWorkType = $row['mon_worktype'];
    $tueWorkType = $row['tue_worktype'];
    $wedWorkType = $row['wed_worktype'];
    $thuWorkType = $row['thu_worktype'];
    $friWorkType = $row['fri_worktype'];
    $satWorkType = $row['sat_worktype'];
    $sunWorkType = $row['sun_worktype'];

    // Public holidays in Malaysia (adjust as needed)
    $publicHolidays = [
        '01-01' => 'New Years Day',
        '02-01' => 'Federal Territory Day',
        '05-01' => 'Labour Day',
        '05-30' => 'Hari Raya Puasa',
        '05-31' => 'Hari Raya Puasa',
        '08-31' => 'National Day',
        '09-16' => 'Malaysia Day',
        '12-25' => 'Christmas Day',
    ];

    // Validate and format month input
    if (!empty($datemonth) && is_numeric($datemonth) && $datemonth >= 1 && $datemonth <= 12) {
        $month = (int)$datemonth;
        $year = date('Y'); // Default to current year

        // Convert numeric month to short name (e.g., 12 -> Dec)
        $monthName = date('M', mktime(0, 0, 0, $month, 10));

        // Calculate the first and last dates of the selected month
        $startDate = date('01-' . $datemonth . '-' . $year); // First date of the month
        $endDate = date('t-' . $datemonth . '-' . $year);   // Last date of the month

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

            $dates[] = [
                'date' => $dateObj->format('M j'),
                'day' => $dateObj->format('D'),
                'remarks' => $remarks,
                'status' => $attendance['status'] ?? '',
                'note' => $attendance['remarks'] ?? $row[$remarksCol] ?? $publicHolidays[$dateObj->format('m-d')] ?? '', // Status or Holiday Note
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
        }
    } else {
        die("Invalid or missing month value.");
    }

    // Sanitize the filename
    $filename = preg_replace('/[^A-Za-z0-9_]/', '_', $staffname . '_Attendance_' . $monthName . '_' . $year);

    // Set headers to indicate file type as Excel
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=$filename.xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    // Sample HTML table
    echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Attendance Report</title>
            <style>
                @page {
                    margin: 0.2in;
                }
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    font-size: 10px;
                }
                th, td {
                    border: 1px solid black;
                    padding: 4px;
                    text-align: center;
                    word-wrap: break-word;
                }
                th {
                    background-color: #f4f4f4;
                    font-weight: bold;
                }
                .header {
                    text-align: center;
                    font-size: 14px;
                    font-weight: bold;
                    margin-bottom: 10px;
                }
            </style>
        </head>
        <body>
        <div>
            <div class='header'>
                <table>
                    <thead>
                        <tr>
                            <th colspan='5'>LTA Services Sdn Bhd</th>
                            <th colspan='6'>Timesheet Report</th>
                        <tr>

                        <tr>    
                            <th colspan='5'>Emp Name: $staffname</th>
                            <th colspan='3'>Emp No: $staff_id </th>
                            <th colspan='3'>Report Date: $startDate - $endDate</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Day</th>
                        <th>Remarks</th>
                        <th>Status</th>
                        <th>Start Work</th>
                        <th>Break </th>
                        <th>End Work</th>
                        <th>Hour Worked</th>
                        <th>OT</th>
                        <th>Lateness</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody> "; ?>
                    <?php foreach ($dates as $dateInfo): ?>
                        <tr>
                            <td><?php echo $dateInfo['date']; ?></td>
                            <td><?php echo $dateInfo['day']; ?></td>
                            <td><?php echo $dateInfo['remarks']; ?></td>
                            <td><?php echo $dateInfo['status']; ?></td>
                            <td><?php echo $dateInfo['start_work']; ?></td>
                            <td><?php echo $dateInfo['break1']; ?></td>
                            <td><?php echo $dateInfo['end_work']; ?></td>
                            <td><?php echo $dateInfo['hour_worked']; ?></td>
                            <td><?php echo $dateInfo['ot']; ?></td>
                            <td><?php echo $dateInfo['lateness']; ?></td>
                            <td><?php echo $dateInfo['note']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <table>
                <tr>
                    <th colspan="2">Hrs Worked:</th>
                    <th colspan="2">Day Worked</th>
                    <th colspan="2">OT</th>
                    <th colspan="2">Pending OT:</th>
                    <th colspan="3">Lateness :</th>
                </tr>

                <tr>
                    <th colspan="2">Hrs Worked (Rest):</th>
                    <th colspan="2">Day Worked (Rest)</th>
                    <th colspan="2">OT (Rest)</th>
                    <th colspan="2">Pending OT: (Rest)</th>
                    <th colspan="3">Late count:</th>
                </tr>

                <tr>
                    <th colspan="2">Hrs Worked (Off):</th>
                    <th colspan="2">Day Worked (Off)</th>
                    <th colspan="2">OT (Off)</th>
                    <th colspan="2">Pending OT: (Off)</th>
                    <th colspan="3">Absence:</th>
                </tr>

                <tr>
                    <th colspan="2">Hrs Worked (Holiday):</th>
                    <th colspan="2">Day Worked (Holidayf)</th>
                    <th colspan="2">OT (Holiday)</th>
                    <th colspan="2">Pending OT: (Holiday)</th>
                    <th colspan="3">UL:</th>
                </tr>

                <tr>
                    <th colspan="11">
                        I certify that the entries on the record which were made by 
                        myself daily at the time of arrival and departure from office are true and correct.
                    </th>
                </tr>
            </table>

            <table>
                <tr>
                    <th colspan="5">
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        
                    </th>

                    <th colspan="6">
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        
                    </th>
                </tr>

                <tr>
                    <th colspan="5">
                        Employee's Signature
                    </th>

                    <th colspan="6">
                        Authorized Officer
                    </th>
                </tr>
            </table>
        </div>
        <?php echo "
        </body>
        </html>";
?>
