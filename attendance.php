<?php 
include 'database.php'; 
ob_start();
include 'sidebar.php'; 

// Set timezone to Philippine Time
date_default_timezone_set('Asia/Manila');

// Check if student_id is stored in SESSION
if (!isset($_SESSION['student_id'])) {
    echo "<script>alert('Session expired. Please log in again.'); window.location='login.php';</script>";
    exit();
}

$student_id = $_SESSION['student_id']; // Get the logged-in student ID
$date = date("Y-m-d"); // Current date

// Function to check if an action has already been recorded for today
function actionExists($conn, $student_id, $action, $date) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM attendance_temp WHERE student_id = ? AND action = ? AND date = ?");
    $stmt->bind_param("iss", $student_id, $action, $date);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count > 0;
}

// Function to check if attendance has already been submitted for the current day in `records` table
function isAttendanceSubmitted($conn, $student_id, $date) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM records WHERE student_id = ? AND date = ?");
    $stmt->bind_param("is", $student_id, $date);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count > 0;
}

// Function to delete attendance action
function deleteAttendance($conn, $student_id, $action, $date) {
    $stmt = $conn->prepare("DELETE FROM attendance_temp WHERE student_id = ? AND action = ? AND date = ?");
    $stmt->bind_param("iss", $student_id, $action, $date);
    $stmt->execute();
    $stmt->close();
}

$attendanceSubmitted = isAttendanceSubmitted($conn, $student_id, $date); // Check if attendance is submitted for the day

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && !$attendanceSubmitted) { // Only allow actions if attendance isn't submitted for today
        $action = $_POST['action'];

        // Format the time in 24-hour format
        $time = date("H:i"); // Get the current time in 24-hour format (00:00 to 23:59)

        if (actionExists($conn, $student_id, $action, $date)) { 
            echo "<script>alert('You have already recorded \"$action\" for today.');</script>";
        } else {
            $sql = "INSERT INTO attendance_temp (student_id, action, date, time) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isss", $student_id, $action, $date, $time);

            if ($stmt->execute()) {                    
                header("Location: attendance.php"); // Refresh the page
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    } elseif (isset($_POST['submit_attendance']) && !$attendanceSubmitted) {
        // Move temporary attendance to final records
        $sql = "INSERT INTO records (student_id, action, date, time)
                SELECT student_id, action, date, time FROM attendance_temp WHERE student_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $stmt->close();

        // Clear temporary records for this student
        $sql = "DELETE FROM attendance_temp WHERE student_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $stmt->close();

        header("Location: records.php");
        exit();
    } elseif (isset($_POST['delete_action'])) { // If delete action is requested
        $action = $_POST['delete_action'];
        deleteAttendance($conn, $student_id, $action, $date); // Delete the record
        header("Location: attendance.php"); // Refresh the page
        exit();
    } else {
        // If attendance is already submitted
        echo "<script>alert('Attendance for today has already been submitted.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <title>Attendance Tracker</title>
    <style>
        .content {
            flex: 1;
            padding: 40px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            overflow-y: auto;
        }

        .header-box {
            background: rgb(59, 98, 172);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            color: white;
        }

        .attendance-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-bottom: 25px;
        }

        .attendance-buttons button {
            padding: 12px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
            width: 150px;
        }

        .time-in { background: #28a745; color: white; }
        .time-out { background: #dc3545; color: white; }
        .break-in { background: #ffc107; color: black; }
        .break-out { background: #17a2b8; color: white; }

        .submit-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 50px;
        }

        .submit-btn {
            padding: 12px 20px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            background: #007bff;
            color: white;
            cursor: pointer;
            transition: 0.3s;
        }

        .submit-btn:hover { background: #0056b3; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th { background: #2a5298; color: white; }

        .delete-btn {
            color: red;
            font-size: 20px;
            cursor: pointer;
        }

        .delete-btn:hover {
            color: darkred;
        }

    </style>
</head>
<body>

    <div class="content">
        <div class="header-box">
            <h1>📌 Attendance Tracker</h1>
            <p>📝 Track your attendance here.</p>
        </div>

        <form method="POST" id="attendanceForm">
            <input type="hidden" name="action" id="attendance-action">
            <div class="attendance-buttons">
                <button type="button" class="time-in" onclick="confirmAction('Time In')">
                    Time In
                </button>
                <button type="button" class="break-out" onclick="confirmAction('Break Out')">
                    Break Out
                </button>
                <button type="button" class="break-in" onclick="confirmAction('Break In')">
                    Break In
                </button>
                <button type="button" class="time-out" onclick="confirmAction('Time Out')">
                    Time Out
                </button>
            </div>
        </form>

        <!-- Attendance Table -->
        <table>
            <thead>
                <tr>
                    <th>Action</th>
                    <th>Date</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT id, action, date, time FROM attendance_temp WHERE student_id = ? AND date = ? ORDER BY time ASC";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("is", $student_id, $date);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Convert time to 12-hour format with AM/PM
                        $time = new DateTime($row['time']);
                        $time->setTimezone(new DateTimeZone('Asia/Manila'));
                        $formatted_time = $time->format('g:i A'); // Convert to 12-hour format with AM/PM
                        
                        echo "<tr>
                                <td>{$row['action']}</td>
                                <td>{$row['date']}</td>
                                <td>{$formatted_time}</td>
                                <td>
                                    <form method='POST'>
                                        <input type='hidden' name='delete_action' value='{$row['action']}'>
                                        <button type='submit' onclick='return confirm(\"Are you sure you want to delete this action?\")' class='delete-btn'>
                                            <i class='fas fa-times'></i>
                                        </button>
                                    </form>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No attendance recorded for today.</td></tr>";
                }
                $stmt->close();
                ?>
            </tbody>
        </table>

        <div class="submit-container">
            <form method="POST">
                <button class="submit-btn" type="submit" name="submit_attendance" <?php echo $attendanceSubmitted ? 'disabled' : ''; ?>>
                    Submit
                </button>
            </form>
        </div>
    </div>

    <script>
        // Get current time in AM/PM format
        function getTimePeriod() {
            const hours = new Date().getHours();
            return hours >= 12 ? 'PM' : 'AM';
        }

        function confirmAction(action) {
            const ampm = getTimePeriod(); // Get the AM/PM indicator
            const time = new Date().toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'}) + ' ' + ampm; // Get current time with AM/PM
            if (confirm(`Do you want to ${action}?`)) {
                document.getElementById('attendance-action').value = action;
                document.getElementById('attendanceForm').submit();
            }
        }

        // Check if all actions are recorded before submitting
        document.querySelector('.submit-btn').addEventListener('click', function(event) {
            const rows = document.querySelectorAll('table tbody tr');
            if (rows.length < 4) { 
                alert('Please record all attendance actions before submitting.');
                event.preventDefault();
            } else {
                if (!confirm('Are you sure you want to submit your attendance for today?')) {
                    event.preventDefault();
                }
            }
        });
    </script>

</body>
</html>

<?php $conn->close(); ?>
