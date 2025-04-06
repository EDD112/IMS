<?php
include 'database.php'; 
ob_start();
include 'sidebar.php'; 

$student_id = $_SESSION['student_id'] ?? null;
if ($student_id) {
    $query = "SELECT * FROM users WHERE student_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $profile_picture = $user['profile_picture'] ?: 'uploads/default.png';
    $firstname = $user['firstname'];
    $lastname = $user['lastname'];
    $email = $user['username'] . "@example.com";
    $strand = $user['strand'] ?? 'N/A';
    $grade = $user['grade'] ?? 'N/A';
    $section = $user['section'] ?? 'N/A';
} else {
    $profile_picture = 'uploads/default.png';
    $firstname = 'Guest';
    $lastname = '';
    $email = '';
    $strand = 'N/A';
    $grade = 'N/A';
    $section = 'N/A';
}

// Fetch attendance record count
$attendance_count = 0;
if ($student_id) {
    $query = "SELECT COUNT(*) AS count FROM records WHERE student_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $attendance_data = $result->fetch_assoc();
    $attendance_count = $attendance_data['count'];
}

// Fetch total minutes/hours accumulated
$total_minutes = 0;
if ($student_id) {
    $query = "SELECT date, 
                     MAX(CASE WHEN action = 'Time In' THEN time END) AS time_in,
                     MAX(CASE WHEN action = 'Break Out' THEN time END) AS break_out,
                     MAX(CASE WHEN action = 'Break In' THEN time END) AS break_in,
                     MAX(CASE WHEN action = 'Time Out' THEN time END) AS time_out
              FROM records 
              WHERE student_id = ? 
              GROUP BY date";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $time_in = isset($row['time_in']) ? strtotime($row['time_in']) : null;
        $break_out = isset($row['break_out']) ? strtotime($row['break_out']) : null;
        $break_in = isset($row['break_in']) ? strtotime($row['break_in']) : null;
        $time_out = isset($row['time_out']) ? strtotime($row['time_out']) : null;

        if ($time_in && $break_out && $break_in && $time_out) {
            $morning_session = $break_out - $time_in; // Time In to Break Out
            $afternoon_session = $time_out - $break_in; // Break In to Time Out
            $total_minutes += ($morning_session + $afternoon_session) / 60;
        }
    }
    $total_hours = floor($total_minutes / 60);
    $remaining_minutes = $total_minutes % 60;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
body {
    display: flex;
    height: 100vh;
    background: url('cec.jpg') no-repeat center center/cover;
    backdrop-filter: blur(5px);
}

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
    text-align: left;
    width: 100%;
    margin-bottom: 25px;
}

.header-box h1 {
    color: white;
    font-size: 26px;
    margin-bottom: 8px;
}

.profile-container {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    gap: 20px;
    width: 500px;
}

.profile-container img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #1e3c72;
}

.profile-details {
    display: flex;
    flex-direction: column;
}

.profile-details h2 {
    margin: 0;
    color: #1e3c72;
    font-size: 22px;
}

.profile-details p {
    margin: 5px 0;
    color: #555;
    font-size: 14px;
}

.info-container {
    background: white;
    margin-top: 20px;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 500px;
}

.info-container h3 {
    margin-bottom: 10px;
    color: #1e3c72;
    font-size: 20px;
}

.info-container p {
    margin: 5px 0;
    color: #555;
    font-size: 14px;
}

/* Attendance Container */
.attendance-container {
    background: white;
    margin-top: 20px;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 500px;
    text-align: center;
}

.attendance-container h3 {
    margin-bottom: 10px;
    color: #1e3c72;
    font-size: 20px;
}

.attendance-container p {
    margin: 5px 0;
    color: #555;
    font-size: 14px;
    font-weight: bold;
}

.total-time-container {
    position: absolute;
    top: 100px;
    right: 50px;
    width: 300px;
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
}

@media only screen and (max-width: 768px) {
    body {
        flex-direction: column;
        overflow-y: auto;
    }

    .content {
        width: 100%;
        padding: 20px;
        box-shadow: none;
        border-radius: 10px;
    }

    .profile-container {
        flex-direction: column;
        width: 100%;
        text-align: center;
    }

    .profile-container img {
        width: 100px;
        height: 100px;
    }

    .info-container, .attendance-container {
        width: 100%;
        margin-top: 10px;
    }

    .total-time-container {
        position: static;
        width: 100%;
        margin-top: 10px;
    }

    .header-box h1 {
        font-size: 20px;
    }

    .profile-details h2 {
        font-size: 18px;
    }

    .profile-details p, .info-container p, .attendance-container p {
        font-size: 14px;
    }
}

    </style>
</head>
<body>
<div class="content">
    <div class="header-box">
        <h1>Student Profile</h1>
    </div>
    <div class="profile-container">
        <img src="<?php echo $profile_picture; ?>" alt="Profile Picture">
        <div class="profile-details">
            <h2><?php echo $firstname . ' ' . $lastname; ?></h2>
            <p>Email: <?php echo $email; ?></p>
            <p>Phone: <?php echo $phone ?? 'N/A'; ?></p>
            <p>School Address: <?php echo $address ?? 'N/A'; ?></p>
        </div>
    </div>

    <!-- Basic Information Section -->
    <div class="info-container">
        <h3>Basic Information</h3>
        <p><strong>Student ID:</strong> <?php echo $student_id ?? 'N/A'; ?></p>
        <p><strong>Strand:</strong> <?php echo $strand; ?></p>
        <p><strong>Grade:</strong> <?php echo $grade; ?></p>
        <p><strong>Section:</strong> <?php echo $section; ?></p><br>
    
        <h3>Personal Information</h3>
        <p><strong>Address:</strong> <?php echo $address ?? 'N/A'; ?></p><br>
       
        <h3>Parents Information</h3>
        <p>Email: <?php echo $email; ?></p>
        <p>Phone: <?php echo $phone ?? 'N/A'; ?></p>
    </div>

    <!-- Attendance Count Container -->
    <div class="info-container">
        <h3>Total Attendance Actions</h3>
        <p><strong><?php echo $attendance_count; ?></strong> Records Found</p>
    </div>

    <div class="info-container total-time-container">
        <h3>Total Accumulated Time</h3>
        <p><strong><?php echo $total_hours; ?></strong> hours <strong><?php echo $remaining_minutes; ?></strong> minutes</p>
    </div>

</div>
</body>
</html>
