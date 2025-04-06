<?php
include_once 'database.php'; 
$student_id = $_SESSION['student_id'] ?? null;
if ($student_id) {
    $query = "SELECT * FROM users WHERE student_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $sidebar_profile_picture = $user['sidebar_profile_picture'] ?: 'uploads/default.png';
    $firstname = $user['firstname'];
    $lastname = $user['lastname'];
} else {
    $sidebar_profile_picture = 'uploads/default.png';
    $firstname = 'Guest';
    $lastname = '';
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['sidebar_profile_picture']) && $_FILES['sidebar_profile_picture']['error'] == UPLOAD_ERR_OK) {
    $file = $_FILES['sidebar_profile_picture'];
    $target_dir = "uploads/";
    $target_file = $target_dir . time() . "_" . basename($file["name"]);
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        $update_sql = "UPDATE users SET sidebar_profile_picture = ? WHERE student_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $target_file, $student_id);
        if ($update_stmt->execute()) {
            $sidebar_profile_picture = $target_file;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Poppins', sans-serif; }
        body { display: flex; height: 100vh; background: url('cec.jpg') no-repeat center center/cover; backdrop-filter: blur(5px); }
        .sidebar { background: linear-gradient(135deg, #1e3c72, #2a5298); width: 250px; padding: 20px; color: white; display: flex; flex-direction: column; gap: 15px; align-items: center; transition: transform 0.3s ease; }
        .sidebar-profile-container { width: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; margin-bottom: 10px; }
        .sidebar-profile-picture { width: 100px; height: 100px; border-radius: 50%; border: solid white; object-fit: cover; display: block; transition: transform 0.3s ease; }
        .sidebar-profile-picture:hover { transform: scale(1.1); }
        .sidebar-profile-name { color: #fff; font-size: 16px; font-weight: bold; text-align: center; }
        .nav-link { display: flex; align-items: center; gap: 10px; font-size: 16px; color: #fff; text-decoration: none; border-radius: 8px; transition: background-color 0.3s ease; padding: 12px; width: 100%; }
        .nav-link i { width: 20px; text-align: center; }
        .nav-link:hover { background-color: rgb(190, 186, 186); }
        .logout-btn { margin-top: auto; padding: 12px; font-size: 16px; color: black; background-color: rgb(255, 255, 255); border: none; border-radius: 8px; cursor: pointer; transition: background-color 0.3s ease; width: 100%; text-align: center; }
        .logout-btn:hover { background-color: rgb(212, 22, 66); }
        @media only screen and (min-width: 769px) {
    .toggle-btn{
        display: none;
    }
}
        @media only screen and (max-width: 768px) {
            .sidebar { position: fixed; top: 0; left: 0; width: 0; height: 100%; overflow-x: hidden; padding: 0; transform: translateX(-100%); transition: transform 0.3s ease; z-index: 1000; }
            .sidebar.active { transform: translateX(0); width: 250px; padding: 20px; }
            .sidebar .nav-link { opacity: 0; transition: opacity 0.3s ease 0.1s; }
            .sidebar.active .nav-link { opacity: 1; }
            .toggle-btn { position: fixed; top: 20px; left: 20px; width: 40px; height: 40px; background-color: #1e3c72; color: white; border: none; border-radius: 50%; font-size: 24px; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 1001; }
            .toggle-btn:hover { background-color: #2a5298; }
        }
    </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <form method="post" enctype="multipart/form-data">
            <div class="sidebar-profile-container">
                <label for="sidebar-profile-upload">
                    <img src="<?php echo $sidebar_profile_picture; ?>" class="sidebar-profile-picture" alt="Profile">
                </label>
                <input type="file" name="sidebar_profile_picture" id="sidebar-profile-upload" style="display: none;" onchange="this.form.submit();">
            </div>
        </form>
        <div class="sidebar-profile-name"><?php echo $firstname . ' ' . $lastname; ?></div>
        <a href="home.php" class="nav-link"><i class="fas fa-home"></i> Home</a>
        <a href="attendance.php" class="nav-link"><i class="fas fa-calendar-check"></i> Attendance Tracker</a>
        <a href="records.php" class="nav-link"><i class="fas fa-users"></i> Student Records</a>
        <a href="student_profile.php" class="nav-link"><i class="fas fa-user"></i> Student Profile</a>
        <form method="post" action="logout.php">
            <button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </form>
    </div>
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }
    </script>
</body>
</html>
