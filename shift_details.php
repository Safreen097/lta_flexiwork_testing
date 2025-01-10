<?php
    // Include database connection
    include('connection/connection.php');

    // Check if 'date' is passed via GET and session is set
    if (isset($_GET['date']) && isset($_SESSION['id'])) {
        $id = $_SESSION['id'];
        $selectedDate = $_GET['date'];

        // Extract day name and shorten it
        $dayName = strtolower(date('l', strtotime($selectedDate))); // Full day name (e.g., 'Monday')
        $tableDayMap = [
            'monday' => 'mon',
            'tuesday' => 'tue',
            'wednesday' => 'wed',
            'thursday' => 'thu',
            'friday' => 'fri',
            'saturday' => 'sat',
            'sunday' => 'sun',
        ];
        $shortDay = $tableDayMap[$dayName] ?? null;

        if (!$shortDay) {
            die('Invalid day selected');
        }

        // Query the `admin` table dynamically for the specific day columns
        $columns = [
            "{$shortDay}_worktype",
            "{$shortDay}_start",
            "{$shortDay}_end",
            "{$shortDay}_breakin",
            "{$shortDay}_breakout",
            "{$shortDay}_remarks"
        ];
        $columnsList = implode(", ", $columns); // Create a comma-separated list of column names

        $sql = "SELECT $columnsList FROM admin WHERE staff_id='$id'";
        $result = mysqli_query($connection, $sql);
        $data = mysqli_fetch_assoc($result);

        // Format times to hh:mm AM/PM
        function formatTime($time) {
            return ($time && $time !== 'N/A') ? date('h:i A', strtotime($time)) : 'N/A';
        }

        if ($data) {
            // Determine work day based on work type
            $workType = $data["{$shortDay}_worktype"] ?? 'N/A';
            $workDay = ($workType === 'NORMAL') ? 'WORK DAY' : (($workType === 'FLEXI DAY') ? 'REST DAY' : 'N/A');

            // Map the data to a more descriptive array
            $shiftData = [
                'work_type' => $workType,
                'work_day' => $workDay,
                'start_time' => formatTime($data["{$shortDay}_start"] ?? 'N/A'),
                'end_time' => formatTime($data["{$shortDay}_end"] ?? 'N/A'),
                'break_time' => isset($data["{$shortDay}_breakin"], $data["{$shortDay}_breakout"])
                    ? formatTime($data["{$shortDay}_breakin"]) . ' - ' . formatTime($data["{$shortDay}_breakout"])
                    : 'N/A',
                'notes' => $data["{$shortDay}_remarks"] ?? 'No notes available.'
            ];
        } else {
            $shiftData = [
                'work_type' => 'N/A',
                'work_day' => 'N/A',
                'start_time' => 'N/A',
                'end_time' => 'N/A',
                'break_time' => 'N/A',
                'notes' => 'No notes available.'
            ];
        }

        // Query the `attendance` table for clock times
        $attendanceQuery = "SELECT clock_in, break1_in, break1_out, clock_out FROM attendance WHERE staff_id='$id' AND date='$selectedDate'";
        $attendanceResult = mysqli_query($connection, $attendanceQuery);
        $attendanceData = mysqli_fetch_assoc($attendanceResult);

        $attendanceTimes = [
            'clock_in' => formatTime($attendanceData['clock_in'] ?? 'N/A'),
            'break_in' => formatTime($attendanceData['break1_in'] ?? 'N/A'),
            'break_out' => formatTime($attendanceData['break1_out'] ?? 'N/A'),
            'clock_out' => formatTime($attendanceData['clock_out'] ?? 'N/A')
        ];
    } else {
        header("Location: shift.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LTA Attendance System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h3 class="mb-3 text-center">SHIFT DETAILS</h3>
        <div class="card shadow-lg">
            <div class="card-body">
                <table class="table table-striped">
                    <tr>
                        <td><i class="bi bi-person-workspace me-2"></i> <strong>DAY TYPE</strong></td>
                        <td><?= $shiftData['work_day']; ?></td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-info-square me-2"></i> <strong>WORK TEMPLATE</strong></td>
                        <td><?= $shiftData['work_type']; ?></td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-clock me-2"></i> <strong>START WORK</strong></td>
                        <td><?= $shiftData['start_time']; ?></td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-cup-hot me-2"></i> <strong>BREAK</strong></td>
                        <td><?= $shiftData['break_time']; ?></td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-clock-fill me-2"></i> <strong>END WORK</strong></td>
                        <td><?= $shiftData['end_time']; ?></td>
                    </tr>
                    <tr>
                        <td>
                            <i class="bi bi-card-text me-2"></i>
                            <strong>NOTES</strong>
                            <?php if ($shiftData['notes'] !== 'No notes available.'): ?>
                                <span class="text-muted ms-2">(<?= $shiftData['notes']; ?>)</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editNotesModal">
                                Notes
                            </button>
                        </td>
                    </tr>

                    <!-- Attendance data section -->
                    <tr>
                        <td colspan="2"><i class="bi bi-clock-fill me-2"></i> <strong>CLOCK TIMES</strong></td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-clock me-2"></i> <strong>START TIME</strong></td>
                        <td><?= $attendanceTimes['clock_in']; ?></td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-clock me-2"></i> <strong>BREAK IN</strong></td>
                        <td><?= $attendanceTimes['break_in']; ?></td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-clock me-2"></i> <strong>BREAK OUT</strong></td>
                        <td><?= $attendanceTimes['break_out']; ?></td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-clock me-2"></i> <strong>END TIME</strong></td>
                        <td><?= $attendanceTimes['clock_out']; ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal for Editing Notes -->
    <div class="modal fade" id="editNotesModal" tabindex="-1" aria-labelledby="editNotesModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="update_notes.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editNotesModalLabel">Edit Notes</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="notesTextarea" class="form-label">Notes</label>
                            <textarea class="form-control" id="notesTextarea" name="notes" rows="4"><?= $shiftData['notes'] !== 'No notes available.' ? $shiftData['notes'] : ''; ?></textarea>
                        </div>
                        <input type="hidden" name="date" value="<?= $selectedDate; ?>">
                        <input type="hidden" name="id" value="<?= $id; ?>">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
