<?php
include 'database.php'; 
ob_start();
include 'sidebar.php'; 

// Ensure the user is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Insert attendance records into 'records' table
$sql = "INSERT INTO records (student_id, action, date, time)
        SELECT student_id, action, date, time FROM attendance WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();

// Clear old attendance records
$sql = "DELETE FROM attendance WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();

// Fetch distinct dates for the student, ordered chronologically
$sql = "SELECT DISTINCT date FROM records WHERE student_id = ? ORDER BY date ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$dates = [];
while ($row = $result->fetch_assoc()) {
    $dates[] = $row['date'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Records</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .content {
            flex: 1;
            padding: 40px;
            background-color: rgba(255, 255, 255, 0.9);
            border-top-right-radius: 15px;
            border-bottom-right-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            overflow-y: auto;
            animation: fadeIn 0.7s ease;
        }

        .header-box {
            background: rgb(59, 98, 172);
            padding: 25px;
            border-radius: 15px;
            text-align: left;
            width: 100%;
            margin-bottom: 25px;
        }

        .header-box h1 { color: white; font-size: 26px; margin-bottom: 8px; }
        .header-box p { color: white; font-size: 18px; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th { background: #2a5298; color: white; }
        .day-header {
            background: #4CAF50;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 18px;
            margin-top: 30px;
        }

        .no-records {
            color: #999;
            font-style: italic;
            text-align: center;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

<div class="content">
    <div class="header-box">
        <h1>📊 Attendance Records</h1>
        <p>View all your attendance records here.</p>
    </div>

    <?php
    if (!empty($dates)) {
        foreach ($dates as $index => $date) {
            $dayNumber = $index + 1;
            echo "<div class='day-header'>📅 Day $dayNumber - $date</div>";

            echo "<table>
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>";

            // Fetch actions for the current date
            $sql = "SELECT action, time FROM records WHERE student_id = ? AND date = ? ORDER BY time ASC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $student_id, $date);
            $stmt->execute();
            $actions = $stmt->get_result();

            if ($actions->num_rows > 0) {
                while ($row = $actions->fetch_assoc()) {
                    $timeObj = DateTime::createFromFormat('H:i:s', $row['time']);
                    $formatted_time = $timeObj ? $timeObj->format('g:i A') : 'Invalid time format';
                    echo "<tr>
                            <td>{$row['action']}</td>
                            <td>{$formatted_time}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='2' class='no-records'>No records found for this day.</td></tr>";
            }

            echo "</tbody></table>";
        }
    } else {
        echo "<p class='no-records'>No attendance records found.</p>";
    }
    ?>

</div>

</body>
</html>

<?php
$conn->close();
?>
