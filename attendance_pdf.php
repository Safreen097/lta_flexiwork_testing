<?php
require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Function to generate the PDF report
function generatePDFReport()
{
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
            FROM admin WHERE admin_id = '$id'";

    $result = mysqli_query($connection, $sql);
    $row = mysqli_fetch_assoc($result);
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

    // Start capturing the HTML content
    ob_start();

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
                    font-size: 9px; /* Smaller font size */
                    table-layout: fixed; /* Ensures table fits page */
                }
                th, td {
                    border: 1px solid black;
                    padding: 2px; /* Reduced padding */
                    text-align: center;
                    word-wrap: break-word; /* Handle long text */
                }
                th {
                    background-color: #f4f4f4;
                    font-weight: bold;
                }
                .header {
                    text-align: center;
                    font-size: 10px; /* Smaller header font */
                    font-weight: bold;
                    margin-bottom: 5px; /* Reduced margin */
                }
            </style>
        </head>
        <body>
        <div>
            <div class='header'>
                <table>
                    <thead>
                        <tr>
                            <th>LTA Services Sdn Bhd</th>
                            <th colspan='2'>Timesheet Report</th>
                        <tr>

                        <tr>    
                            <th>Emp Name: $staffname</th>
                            <th>Emp No: $staff_id </th>
                            <th>Report Date: $startDate - $endDate</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <table>
                <thead>
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
                <tbody> "; ?>
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

            <table>
                <tr>
                    <td>Hrs Worked = (<?php echo $hrsWorked; ?>)</td>
                    <td>Day Worked = (<?php echo $daysWorked; ?>)</td>
                    <td>OT = (<?php echo $totalOT; ?>)</td>
                    <td>Lateness = ()</td>
                </tr>

                <tr>
                    <td>Hrs Worked (Rest) = (<?php echo $hrsWorkedRest; ?>)</td>
                    <td>Day Worked (Rest) = (<?php echo $daysWorkedRest; ?>)</td>
                    <td>OT (Rest) = (<?php echo $totalOTRest; ?>)</td>
                    <td>Late Count = ()</td>
                </tr>

                <tr>
                    <td>Hrs Worked (Holiday) = (<?php echo $hrsWorkedHoliday; ?>)</td>
                    <td>Day Worked (Holiday) = (<?php echo $daysWorkedHoliday; ?>)</td>
                    <td>OT (Holiday) = (<?php echo $totalOTHoliday; ?>)</td>
                    <td>Absence = (<?php echo $absenceCount; ?>)</td>
                </tr>

                <tr>
                    <th colspan="5">
                        I certify that the entries on the record which were made by 
                        myself daily at the time of arrival and departure from office are true and correct.
                    </th>
                </tr>
            </table>

            <table>
                <tr>
                    <th>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <hr>
                    </th>

                    <th>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <hr>
                    </th>
                </tr>

                <tr>
                    <th>
                        Employee's Signature
                    </th>

                    <th>
                        Authorized Officer
                    </th>
                </tr>
            </table>
        </div>
        <?php echo "
        </body>
        </html>      
    ";

    // Get the captured HTML content
    $content = ob_get_clean();

    // Create a new Dompdf instance
    $options = new Options();
    $options->set('defaultFont', 'Arial'); // Set the default font (optional)
    $dompdf = new Dompdf($options);

    // Load the HTML content
    $dompdf->loadHtml($content);

    // Set paper size and orientation (e.g., 'A4', 'portrait')
    $dompdf->setPaper('A4', 'landscape');

    // Render the PDF
    $dompdf->render();


    // Sanitize the filename
    $filename = preg_replace('/[^A-Za-z0-9_]/', '_', $staffname . 'Attendance_' . $datemonth . '_' . $year . '.pdf');

    // Output the generated PDF
    $dompdf->stream($filename, array('Attachment' => 0));

}

// Call the function to generate the PDF report
generatePDFReport();
?>